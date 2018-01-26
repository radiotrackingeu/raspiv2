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

// resampler
#define stages			(5)
#define ratio			(32)
#define Fs				(1024000) 	// 1024kHz
#define Fs2				(Fs/ratio)	//   32kHz

// database
#define DB_BASE "rteu"
#define DB_TABLE "rteu.signals"

#define tmpl_length ((25*Fs2)/1000)	// max. length 25ms
#define sig_length  (tmpl_length*2)
#define corr_length (sig_length-tmpl_length+1)

#define BILLION 	1000000000L

struct detect
{
	float maxVal;
	unsigned int index;
	// number of convolutions
	unsigned int nr_convol;
};

float complex template[tmpl_length];
float  	      correlation[corr_length];

float average = 0;
struct detect sig_detect;

//float psd_template[nfft];
//float psd         [nfft];
//float psd_max     [nfft];
struct timespec psd_time[nfft];
//int   detect      [nfft];
//int   count       [nfft];
//int   groups      [nfft];

struct              timespec now;
int                 keepalive = 300;

int					run_id=0;

int   				timestep=tmpl_length; // time between convolution [samples]
unsigned long int 	num_convolutions = 0;
int 				tmp_transforms = 0;
struct 				timespec start_time;

int                 write_to_db = 0;
MYSQL           *   con;
char            *   db_host = NULL, *db_user = NULL, *db_pass = NULL;
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
    printf("%s [options]\n", __FILE__);
    printf("  -h        	: print help\n");
    printf("  -i <file> 	: input data filename\n");
    printf("  -t <thsh> 	: detection threshold above psd, default: 10 dB\n");
    printf("  -s        	: use STDIN as input\n");
    printf("  -r        	: sampling rate in Hz, default 1024kHz\n");
	printf("  -f        	: signal frequency offset, +- 1000kHz\n");
	printf("  -k <seconds>  : prints a keep-alive statement every <sec> seconds, default is 300\n");
	printf("  -p        	: pulse length in microseconds, default 22ms\n");
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

int   update_detect(float _threshold, float _freq_offset, unsigned int _nPts);;
void  format_timestamp(const struct timespec _time, char * _buf, const unsigned long _buf_len);
struct timespec timeAdd(const struct timespec _t1, const struct timespec _t2);
char  before(const struct timespec _a, const struct timespec _b);
void  init_time();
void  free_memory();


// main program
int main(int argc, char*argv[])
{
    char            filename_input[256] = "SDR_2018_01_10_00_29.cu8"; // "SDR_record_pi.cu8";
    float           threshold           = 4.0f; //-60.0f;
    char            read_from_stdin     = 0;
    unsigned long   sampling_rate       = 1024000;
    float			frequency_offset	= 0;
    unsigned int	pulse_length		= 22;	// 22ms

    // read command-line options
    int dopt;
    while ((dopt = getopt(argc,argv,"hi:t:sr:")) != EOF) {
        switch (dopt) {
        case 'h': usage();                              return 0;
        case 'i': strncpy(filename_input,optarg,256);   break;
        case 't': threshold = atof(optarg);             break;
        case 's': read_from_stdin = 1;                  break;
        case 'r': sampling_rate = atoi(optarg);         break;
		case 'f': frequency_offset = atof(optarg);   	break;
		case 'p': pulse_length = atoi(optarg);   		break;
		case 'k': keepalive = atoi(optarg);             break;
		case 900: db_host = optarg;                     break;
		case 901: db_port = atoi(optarg);               break;
        case 902: db_user = optarg;                     break;
        case 903: db_pass = optarg;                     break;
        case 904: run_id = atoi(optarg);                break;
        case 0  :                                       break; // return value of getopt_long() when setting a flag
        default : exit(1);
        }
    }

	// arbitrary resampler
	int          type       = LIQUID_RESAMP_DECIM;
	unsigned int num_stages =  stages;  // decimate by 2^5=32
	float        fc         =  0.1f;    // signal cut-off frequency
	float        f0         =  0.0f;    // (ignored)
	float        As         = 60.0f;    // stop-band attenuation

    // reset buffers etc.
    memset(template, 	0x0, tmpl_length*sizeof(float));
    memset(correlation, 0x0, corr_length*sizeof(float));

    init_time();
	keepalive *= sampling_rate / timestep;
	keepalive += keepalive%16;

	// create agc object
	agc_crcf agc = agc_crcf_create();

	// create the NCO object
	//nco_crcf nco = nco_crcf_create(LIQUID_NCO);
	//nco_crcf_set_phase(nco, 0.0f);
	//nco_crcf_set_frequency(nco, 0.102539063f);

	//create multi-stage arbitrary resampler object
	msresamp2_crcf resamp = msresamp2_crcf_create(type, num_stages, fc, f0, As);

    // create window buffer with n elements
    windowcf window = windowcf_create(sig_length);

    // DC-blocking filter 1e-3f
    iirfilt_crcf dcblock = iirfilt_crcf_create_dc_blocker(1e-3f);

    // buffer
    unsigned int  buf_len = 64;
    float complex buf[buf_len];
    float complex buf1;
    float complex * signal;

    uint8_t agc_locked = 0x00;

    unsigned int i;
	
	printf("write_to_db=%i\n", write_to_db);
    // open SQL database
	if (write_to_db!=0)
    {
        if (db_user==NULL || db_pass==NULL) {
            fprintf(stderr, "Incomplete credentials supplied. Not writing to database.\n");
            write_to_db = 0;
        } else {
            if (db_host == NULL){
                db_host = "127.0.0.1";
                fprintf(stderr, "No hostname given, trying 127.0.0.1.\n");
            }

            con =mysql_init(NULL);
            if (con!=NULL) {
                if (mysql_real_connect(con, db_host, db_user, db_pass,
                      DB_BASE, db_port, NULL, 0) == NULL)
                {
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
	if (write_to_db!=0)
	    printf("Also sending data to SQL Server at %s.\n", db_host);
	clock_gettime(CLOCK_REALTIME,&now);
	char tbuf[30];
	format_timestamp(now,tbuf,30);
	printf("Will print timestamp every %i transforms\n", keepalive);
	printf("%s\n",tbuf);
	//print row names
	printf("time;duration;freq;bw;strength;sample\n");

	// generate template
    unsigned int n = (Fs2 / 1000) * pulse_length;

    for (i=0; i<n; i++)
    	template[i]= 1.0;
    for (i=n;i<tmpl_length;i++)
    	template[i]= 0.0;

    // continue processing as long as there are samples in the file
    unsigned long int total_samples  = 0;
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
//        	agc_crcf_print(agc);
        	agc_locked=0xFF;
        }

        // apply DC blocking filter
        iirfilt_crcf_execute_block(dcblock, buf, buf_len, buf);

        //nco_crcf_mix_block_down(nco, buf, &buf, buf_len);

        for (i=0; i<(buf_len/ratio); i++)
        {
        	// execute resampler
        	msresamp2_crcf_execute(resamp, &buf[i*ratio], &buf1);

			// push data into the window buffer
			windowcf_push(window, buf1);
        }

        if(group_samples >= (tmpl_length*ratio)) // TODO RICHTIG???
        {
            // get content of window buffer
            windowcf_read(window, &signal);

            // calculate convolution
        	calc_convolution(signal, sig_length, template, tmpl_length, correlation, corr_length);

			// find peaks in correlation
			update_detect(threshold*10, frequency_offset, corr_length); //why times 10 - cor results are between 0 and 1?

			num_convolutions++;
			group_samples = 0;
        }

        // update nco object
	    nco_crcf_step(nco);

        // update total sample count
        group_samples += buf_len;
        total_samples += buf_len;

    } while (!feof(fid));

    // close input files
    fclose(fid);

    // clean up allocated objects
    msresamp2_crcf_destroy(resamp);
    agc_crcf_destroy(agc);
    windowcf_destroy(window);
    iirfilt_crcf_destroy(dcblock);


    printf("total samples in : %lu\n", total_samples);
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

// calculate convolution
void calc_convolution(float complex * _buf,
        		      unsigned int    _buf_len,
					  float complex * _template,
					  unsigned int    _tmpl_len,
					  float  		* _result,
					  unsigned int    _nPts)	/* nPts is the length of the required filter output data */
{
  size_t i, j;
  float complex sum = 0.0;
  float r=0;
  double tmp_average = average;

  for (i = 0; i<_nPts; i=i+100)
  {
	  sum = 0.0; // zero output array
	  for (j=0; j<_tmpl_len; j++)
	  {
		  sum += _template[j] * cabs(_buf[i + j]);
	  }

	   if((r=creal(sum)-average) > 0) {
		   _result[i] = r; }
	   else {
		   _result[i] = 0; }

	  // calc average
	  tmp_average += creal(sum);
  }

  tmp_average /= (float)(10);
  average = tmp_average; //((9*average) + tmp_average)/10;
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

	// find peak
    for (i=0; i<_nPts; i++)
    {
        // find values above threshold and find the highest value
		if( ((correlation[i] /*- tmp_average*/) > _threshold) && (correlation[i] > maxVal))
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

			sample = (sig_detect.nr_convol*timestep + sig_detect.index);
			ftime  = ((float)sample / (float)Fs2);
			tm.tv_nsec = (long)(fmodf(ftime,1)*BILLION);
			tm.tv_sec = (long)ftime;
			tm = timeAdd(start_time, tm);
			format_timestamp(tm, timestamp, 30);
            printf("%s;%10ld;%9.6f;%6.2f\n",timestamp, sample, _freq_offset, sig_detect.maxVal);
			fflush(stdout);

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

// returns true iff a is smaller than b
char before(const struct timespec _a, const struct timespec _b)
{
    if (_a.tv_sec==_b.tv_sec)
        return _a.tv_nsec < _b.tv_nsec;
    else
        return _a.tv_sec < _b.tv_sec;
}

// initialize psd_time
void init_time() {
    int i;
    for (i=0; i<nfft; i++) {
        psd_time[i].tv_sec = INT_MAX;
		psd_time[i].tv_nsec = 999999999;
	}
}



