//
// matched_filter.c : detect signals within captured rtlsdr sample file
//
// methodology: compute spectral periodogram, look for where power spectral
// density exceeds threshold, count number of transforms exceeding this
// threshold. Observe time, duration, and bandwidth of signal.
//

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>
#include <liquid/liquid.h>
#include <getopt.h>
#include <time.h>
#include <limits.h>
#include <complex.h>
#include <mysql.h>

// Matlab debug output
#define DEBUG 0
#define OUTPUT_FILENAME "matched_filter_test.m"

// Downsampling
#define ratio	(128)
#define Fs		(1024000) 	// 1024kHz
#define Fs2		(Fs/ratio)	//   32kHz

// database
#define DB_BASE "rteu"
#define DB_TABLE "rteu.signals"

#define tmpl_length ((25*Fs2)/1000)	// max. length 25ms
#define sig_length  (tmpl_length*2)
#define corr_length (sig_length-tmpl_length+1)

#define BILLION 	1000000000L
#define MH_BUF_LEN	500

struct detect
{
	float maxVal;
	unsigned int index;
	// number of convolutions
	unsigned int nr_convol;
};

float complex       template[tmpl_length];
float  	            correlation[corr_length];

float               average = 0;
struct              detect sig_detect;

struct              timespec t_start;
int                 keepalive = 300;
int                 keepalive_cnt = 0;

int					run_id=0;

int   				timestep=tmpl_length; // time between convolution [samples]
unsigned long int 	num_convolutions = 0;
int 				tmp_transforms = 0;
unsigned int	    pulse_length = 22;

float MHBuffer[MH_BUF_LEN];
int   MHPos = 0;
double MHSum = 0;

int                 write_to_db = 0;
MYSQL           *   con;
char            *   db_host = NULL,
                *   db_user = NULL,
                *   db_pass = NULL;
unsigned int 		db_port=0;

struct option longopts[] = {
    {"sql",       no_argument, &write_to_db, 1},
    {"db_host",   required_argument, NULL, 900},
    {"db_port",   required_argument, NULL, 901},
    {"db_user",   required_argument, NULL, 902},
    {"db_pass",   required_argument, NULL, 903},
    {"db_run_id", required_argument, NULL, 904},
    {0,0,0,0}
};


// print usage/help message
void usage()
{
    printf("%s [-d name] [-f <freq>] [-h] [-i <file>] [-k <sec>] [-p <msec>] [-r <rate>] [-s] [--sql [--db_host <host>] --db_user <user> --db_pass <pass>] [-t thre]\n", __FILE__);
    printf("  -h        		: print help\n");
    printf("  -i <file> 		: input data filename\n");
    printf("  -t <thsh> 		: detection threshold above psd, default: 15 dB\n");
    printf("  -s        		: use STDIN as input\n");
    printf("  -r <rate>    		: sampling rate in Hz, default 1024kHz\n");
    printf("  -f <freq>   		: signal frequency offset, +- 1000kHz\n");
    printf("  -k <seconds>  	: prints a keep-alive statement every <sec> seconds, default is 300\n");
    printf("  -p <msec>    		: pulse length in milliseconds, default 22ms\n");
    printf(" --sql              : write to database, requires --db_user, --db_pass\n");
    printf(" --db_host <host>   : address of SQL server to use, default is localhost\n");
    printf(" --db_port <pass>   : port on which to connect, use 0 if unsure\n");
    printf(" --db_user <user>   : username for SQL server \n");
    printf(" --db_pass <pass>   : matching password\n");
    printf(" --db_run_id <id>	: numeric id of this recording run. Used to link it to its metadata in the SQL database");
}

// read samples from file and store into buffer
unsigned int buf_read(FILE *          _fid,
                      float complex * _buf,
                      unsigned int    _buf_len);

// forward declaration of methods for signal detection
void calc_convolution(float complex * _buf,
        		      unsigned int    _buf_len,
					  float complex * _template,
					  unsigned int    _tmpl_len,
					  float 	    * _result,
					  unsigned int    _nPts);

int             update_detect(float _threshold, float _freq_offset, unsigned int _nPts);;
void            format_timestamp(const struct timespec _time, char * _buf, const unsigned long _buf_len);
struct timespec timeAdd(const struct timespec _t1, const struct timespec _t2);
float           MovingAverage(float *_InBuf, unsigned int len);
void            print_keepalive();
void            open_connection();


// main program
int main(int argc, char*argv[])
{
  char          filename_input[256] = "SDR_2018_01_10_00_29.cu8"; // "SDR_record_pi.cu8";
  float         threshold           = 15.0f; //-60.0f;
  char          read_from_stdin     = 0;
  unsigned long sampling_rate       = 1024000;
  float			frequency_offset	= 0;

  // read command-line options
  int dopt;
  while ((dopt = getopt_long(argc,argv,"hi:t:sr:k:p:f:", longopts, NULL)) != -1) {
      switch (dopt) {
      case 'h': usage();                              return 0;
      case 'i': strncpy(filename_input,optarg,256);   break;
      case 't': threshold = atof(optarg);             break;
      case 's': read_from_stdin = 1;                  break;
      case 'r': sampling_rate = atoi(optarg);         break;
      case 'f': frequency_offset = atof(optarg);      break;
      case 'p': pulse_length = atoi(optarg);   	      break;
      case 'k': keepalive = atoi(optarg);             break;
      case 900: db_host = optarg;                     break;
      case 901: db_port = atoi(optarg);               break;
      case 902: db_user = optarg;                     break;
      case 903: db_pass = optarg;                     break;
      case 904: run_id  = atoi(optarg);               break;
      case 0  :                                       break; // return value of getopt_long() when setting a flag
      default : exit(1);
      }
  }

	// arbitrary resampler
//	float r=0.03125f;     // 1/32 resampling rate (output/input)
//	float r=0.015625f;    // 1/64 resampling rate (output/input)
	float r=0.0078125f;   // 1/128 resampling rate (output/input)
//	float r=0.00390625f;  // 1/256 resampling rate (output/input)
	float As=60.0f;       // resampling filter stop-band attenuation [dB]

  // reset buffers etc.
  memset(template, 	0x0, tmpl_length*sizeof(float));
  memset(correlation, 0x0, corr_length*sizeof(float));

  //init_time();
  keepalive *= Fs2 / timestep;

  // create agc object
  agc_crcf agc = agc_crcf_create();

  // create multi-stage arbitrary resampler object
  msresamp_crcf resamp = msresamp_crcf_create(r, As);

  // create window buffer with n elements
  windowcf window = windowcf_create(sig_length);

  // DC-blocking filter 1e-3f
  iirfilt_crcf dcblock = iirfilt_crcf_create_dc_blocker(1e-3f);

  // buffer
  unsigned int  buf_len = 64;
  float complex buf[buf_len];
  float complex * signal;

  uint8_t agc_locked = 0x00;

  unsigned int num_written;       // number of values written to buffer

  unsigned int i;

//	printf("write_to_db=%i\n", write_to_db);
    // open SQL database
  if (write_to_db!=0)
  {
	  open_connection();
  }

  // open input file
  FILE * fid;
  if (read_from_stdin){
      fprintf(stderr,"reading from stdin.\n");
      fid = stdin;
  } else {
      fid = fopen(filename_input,"r");
      if (fid == NULL) {
        fprintf(stderr,"error: could not open %s for reading\n", filename_input);
        exit(-1);
      }
  }

#if(DEBUG)
    //debug output to mathlab .m file
	FILE * fout = fopen(OUTPUT_FILENAME,"w");
	long unsigned int fi_res = 0;
	long unsigned int fi_sig = 0;
	fprintf(fout,"%% %s : auto-generated file\n\n", OUTPUT_FILENAME);
	fprintf(fout,"%% %s : test output\n\n", OUTPUT_FILENAME);
	fprintf(fout,"clear all;\n");
	fprintf(fout,"close all;\n");
#endif

	if (write_to_db!=0)
	    printf("Also sending data to SQL Server at %s.\n", db_host);
	clock_gettime(CLOCK_REALTIME,&t_start);
	char tbuf[30];
	format_timestamp(t_start,tbuf,30);
	printf("Will print timestamp every %i transforms\n", keepalive);
	printf("%s\n",tbuf);
	//print row names
	printf("timestamp;samples;duration;signal_freq;signal_bw;max_signal\n");

  // generate template
  unsigned int n = (Fs2 / 1000) * pulse_length;

  for (i=0; i<n; i++)
    template[i]= 1.0;
  for (i=n;i<tmpl_length;i++)
    template[i]= 0.0;

  // continue processing as long as there are samples in the file
  unsigned long long total_samples  = 0;
  unsigned long int group_samples  = 0;
  num_convolutions = 0;
  do
  {
      // read samples into buffer
      unsigned int r = buf_read(fid, buf, buf_len);
      if (r != buf_len)
          break;

      // execute AGC, scale input sample
      agc_crcf_execute_block(agc, buf, buf_len, buf);

      // lock agc after 5s to reduce cpu load // TODO
      if((total_samples > 5*sampling_rate) && (agc_locked==0x00)){
        agc_crcf_lock(agc);
        agc_locked=0xFF;
//        agc_crcf_print(agc);
      }

      // apply DC blocking filter
      iirfilt_crcf_execute_block(dcblock, buf, buf_len, buf);

    // execute resampler
    msresamp_crcf_execute(resamp, buf, buf_len, buf, &num_written);

    // push data into the window buffer
    windowcf_write(window, buf, num_written);

    if(group_samples >= (tmpl_length*ratio))
    {
      // get content of window buffer
      windowcf_read(window, &signal);

#if(DEBUG)
            // debug print correlation result into matlab file
            for (i=0; i<sig_length;  i++) fprintf(fout,"sig(%3lu) = %f;\n", i+fi_sig+1, creal(signal[i]));
      		fi_sig += sig_length;
#endif

      // calculate convolution
      calc_convolution(signal, sig_length, template, tmpl_length, correlation, corr_length);

      // calculate average
      average = MovingAverage(correlation, corr_length);

      // find peaks in correlation
      update_detect(threshold*10, frequency_offset, corr_length);

#if(DEBUG)
			// debug print correlation result into matlab file
			for (i=0; i<corr_length;  i++) fprintf(fout,"res(%3lu) = %f;\n", i+fi_res+1, creal(correlation[i]));
			fi_res += corr_length;
#endif
      num_convolutions++;
      group_samples = 0;
      keepalive_cnt++;
      if (keepalive_cnt > keepalive) {
        keepalive_cnt =0;
        print_keepalive();
      }
    }

    // update total sample count
    group_samples += buf_len;
    total_samples += buf_len;

  } while (!feof(fid));

  // close input files
  fclose(fid);

    // close debug output file
#if(DEBUG)
	fprintf(fout,"figure;\n");
	fprintf(fout,"plot(sig);\n");
	fprintf(fout,"figure;\n");
	fprintf(fout,"plot(res);\n");
	fprintf(fout,"grid on;\n");
	fclose(fout);
#endif


  // clean up allocated objects
  msresamp_crcf_destroy(resamp);
  agc_crcf_destroy(agc);
  windowcf_destroy(window);
  iirfilt_crcf_destroy(dcblock);


  printf("total samples in : %llu\n", total_samples);
  return 0;
}

// read samples from file and store into buffer
unsigned int buf_read(FILE *          _fid,
                      float complex * _buf,
                      unsigned int    _buf_len)
{
    int num_read = 0;
    unsigned int i;
    uint8_t      buf2[2];
    for (i=0; i<_buf_len; i++) {
      // try to read 2 samples at a time
      if (fread(buf2, sizeof(uint8_t), 2, _fid) != 2)
          break;
      // convert to float complex type
      float complex x = ((float)(buf2[0]) - 127.0f) +
                        ((float)(buf2[1]) - 127.0f) * _Complex_I;
      // scale resulting samples
      _buf[i] = x * 1e-3f;
      num_read++;
    }
    return num_read;
}

#define Div 10
#define Inc (corr_length / Div)

// calculate convolution
void calc_convolution(float complex * _buf,
                      unsigned int    _buf_len,
                      float complex * _template,
                      unsigned int    _tmpl_len,
                      float  		* _result,
                      unsigned int    _nPts)	/* nPts is the length of the required filter output data */
{
  size_t i, j;
  float sum = 0.0;

  for (i = 0; i<_nPts; i=i+Inc)
  {
	  sum = 0.0; // zero output array
	  for (j=0; j<_tmpl_len; j++)
	  {
		  sum += cabsf(_template[j]) * cabsf(_buf[i + j]);
	  }

	  _result[i] = sum;
	  if(_result[i] < 0) {
		  _result[i] = 0; }
  }
}

// look for signal
int update_detect(float _threshold, float _freq_offset, unsigned int _nPts)
{
  unsigned int i;
  int total=0;

	float maxVal = 0;
	unsigned int index = 0;
	unsigned int nr_convol = 0;

	unsigned long sample;
	float ftime;
	float fSNR;

	// find peak
  for (i=0; i<_nPts; i++)
  {
      // find values above threshold and find the highest value
  if( (correlation[i] > _threshold) && (correlation[i] > maxVal))
  {
    maxVal = correlation[i];
    index = i;
    nr_convol = num_convolutions;
  }
}

  // is value above last peak?
  if(maxVal > sig_detect.maxVal)
  {
    // save new peak temporarily until next loop
    sig_detect.maxVal = maxVal;
    sig_detect.index = index;
    sig_detect.nr_convol = nr_convol;
  }
  else
  {
    if(sig_detect.maxVal > _threshold)
    {
      // calc timestamp and print out data
      struct timespec tm;
    char timestamp[30];
    char sql_statement[256];

    sample = (sig_detect.nr_convol*timestep + sig_detect.index);
    ftime  = ((float)sample / (float)Fs2);
    tm.tv_nsec = (long)(fmodf(ftime,1)*BILLION);
    tm.tv_sec = (long)ftime;
    tm = timeAdd(t_start, tm);
    format_timestamp(tm, timestamp, 30);
    fSNR = 20 * log10(sig_detect.maxVal/average);
    printf("%s;%10lu;%1.3f;%9.6f;%9.2f;%9.2f\n",
        timestamp, sample, ((float)pulse_length)/1000, _freq_offset, -1.0f, fSNR);
    fflush(stdout);
    if (write_to_db!=0) {
        snprintf(sql_statement, sizeof(sql_statement),
            "INSERT INTO %s (timestamp,samples,duration,signal_freq,signal_bw, max_signal, run) VALUE(\"%s\",%lu,%1.3f,%9.6f,%9.6f,%f,%i)",
            DB_TABLE, timestamp, sample , ((float)pulse_length)/1000, _freq_offset, -1.0f, fSNR, run_id);
        mysql_query(con, sql_statement);
        if (*mysql_error(con))
            fprintf(stderr, "Error while writing to db: %s", mysql_error(con));
}
    sig_detect.maxVal = 0;
    }
  }
  return total;
}


// pretty-prints _time into _buf
void format_timestamp(const struct timespec _time, char * _buf, const unsigned long _buf_len)
{
  char buffer[11];
  const time_t tm = (time_t) _time.tv_sec;
  strftime(_buf, _buf_len, "%F %T",gmtime(&tm));
  sprintf(buffer, ".%09ld", _time.tv_nsec);
  strncat(_buf, buffer, 10);
}


// add 2 timestamps
struct timespec timeAdd(const struct timespec _t1, const struct timespec _t2)
{
  long sec = _t2.tv_sec + _t1.tv_sec;
  long nsec = _t2.tv_nsec + _t1.tv_nsec;
  if (nsec >= BILLION) {
      nsec -= BILLION;
      sec++;
  }
  return (struct timespec){ .tv_sec = sec, .tv_nsec = nsec };
}

// simple moving average filter
float MovingAverage(float *_InBuf, unsigned int len)
{
	int i;
	//Subtract the oldest number from the prev sum, add the new number
	for (i=0; i<len; i++)
	{
		if(_InBuf[i] != 0)
		{
			MHSum = MHSum - MHBuffer[MHPos] + _InBuf[i];
			//Assign the nextNum to the position in the array
			MHBuffer[MHPos] = _InBuf[i];
			MHPos++;
			if(MHPos >= MH_BUF_LEN){
				MHPos = 0;
			}
		}
	}
	return (float)(MHSum / MH_BUF_LEN);
}

void print_keepalive() {
    mysql_ping(con);
    struct timespec now;
    clock_gettime(CLOCK_REALTIME,&now);
    char tbuf[30];
    char sql_statement[256];
    format_timestamp(now,tbuf,30);
    printf("%s;;;;;\n",tbuf);
    fflush(stdout);
    if (write_to_db!=0) {
        snprintf(sql_statement, sizeof(sql_statement),
            "INSERT INTO %s (timestamp,samples,duration,signal_freq,signal_bw, max_signal, run) VALUE(\"%s\",0,0.0,0.0,-1.0,0.0,%i)",
            DB_TABLE, tbuf, run_id
        );
        mysql_query(con, sql_statement);
        if (*mysql_error(con))
            fprintf(stderr, "Error while writing to db: %s", mysql_error(con));
    }
}

void open_connection() {
    if (db_user==NULL || db_pass==NULL) {
        fprintf(stderr, "Incomplete credentials supplied. Not writing to database.\n");
        write_to_db = 0;
    } else {
        if (db_host == NULL){
            db_host = "127.0.0.1";
            fprintf(stderr, "No hostname given, trying 127.0.0.1.\n");
        }
        con = mysql_init(NULL);
        my_bool reconnect = 1;
        mysql_options(con, MYSQL_OPT_RECONNECT, &reconnect);
        if (con!=NULL) {
            if (mysql_real_connect(con, db_host, db_user, db_pass,
                DB_BASE, db_port, NULL, 0) == NULL) {
                fprintf(stderr, "%s\n", mysql_error(con));
                mysql_close(con);
                write_to_db = 0;
            }
        } else {
            fprintf(stderr, "ERROR: %s\n", mysql_error(con));
            write_to_db = 0;
        }
    }
}

