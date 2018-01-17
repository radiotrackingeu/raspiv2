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

// Matlab debug output
#define DEBUG 0

// resampler
#define stages			(5)
#define ratio			(32)
#define sampling_rate	(1024000) 	// 1024kHz
#define Fs2				(sampling_rate/ratio)	//   32kHz

#define tmpl_length ((24*Fs2)/1000)	// max. length 24ms
#define sig_length  (tmpl_length*2)
#define corr_length (sig_length-tmpl_length+1)

#define nfft 		(400)
#define KEEPALIVE 	(300) // keepalive interval in seconds

#define OUTPUT_FILENAME "matched_filter_test.m"

struct detect
{
	struct timespec time;
	float PeakValue;
	unsigned int Index;
};

float complex template[tmpl_length];
float  	      correlation[corr_length];

double tmp_average = 0;
struct detect sig_detect;

float psd_template[nfft];
float psd         [nfft];
float psd_max     [nfft];
struct timespec psd_time[nfft];
int   detect      [nfft];
int   count       [nfft];
int   groups      [nfft];
int   timestep    =nfft/8; // time between transforms [samples]
unsigned long int num_transforms = 0;
int tmp_transforms = 0;
struct timespec now;
unsigned int keepalive;


// print usage/help message
void usage()
{
    printf("%s [options]\n", __FILE__);
    printf("  -h        : print help\n");
    printf("  -i <file> : input data filename\n");
    printf("  -t <thsh> : detection threshold above psd, default: 10 dB\n");
    printf("  -s        : use STDIN as input\n");
    printf("  -r        : sampling rate in Hz, default 1024kHz\n");
	printf("  -f        : signal frequency offset, +- 1000kHz\n");
	printf("  -p        : pulse length in microseconds, default 22ms\n");
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
char  before(const struct timespec _a, const struct timespec _b);
void  init_time();


// main program
int main(int argc, char*argv[])
{
    char            filename_input[256] = "SDR_2017_12_27_11_31.cu8"; // "SDR_record_pi.cu8";
    float           threshold           = 10.0f; //-60.0f;
    char            read_from_stdin     = 0;
//    unsigned long   sampling_rate       = 1024000;
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
//        case 'r': sampling_rate = atoi(optarg);         break;
		case 'f': frequency_offset = atof(optarg);   	break;
		case 'p': pulse_length = atoi(optarg);   		break;
        default:  exit(1);
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
	keepalive = KEEPALIVE * sampling_rate / timestep;
	keepalive += keepalive%16;

	// create agc object
	agc_crcf agc = agc_crcf_create();

	// create the NCO object
	nco_crcf nco = nco_crcf_create(LIQUID_NCO);
	nco_crcf_set_phase(nco, 0.0f);
	nco_crcf_set_frequency(nco, 0.102539063f);

	// create multi-stage arbitrary resampler object
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
		FILE * fout = fopen(OUTPUT_FILENAME,"w");
		fprintf(fout,"%% %s : auto-generated file\n\n", OUTPUT_FILENAME);
		fprintf(fout,"%% %s : test output\n\n", OUTPUT_FILENAME);
		fprintf(fout,"clear all;\n");
		fprintf(fout,"close all;\n");

		unsigned int fi = 1;
		long unsigned int fi_res = 0;
#endif

	clock_gettime(CLOCK_REALTIME,&now);
	char tbuf[30];
	format_timestamp(now,tbuf,30);
	printf("Will print timestamp every %d seconds / %u transforms\n", KEEPALIVE, keepalive);
	printf("%s\n",tbuf);
	//print row names
	printf("time;duration;freq;bw;strength\n");

    // generate template
    unsigned int n = (Fs2 / 1000) * pulse_length;

    for (i=0; i<n; i++)
    	template[i]= 1.0;
    for (i=n;i<tmpl_length;i++)
    	template[i]= 0.0;

    // continue processing as long as there are samples in the file
    unsigned long int total_samples  = 0;
    unsigned long int group_samples  = 0;
    num_transforms = 0;
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
        	agc_crcf_print(agc);
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
#if(DEBUG)
			fprintf(fout,"signal(%3u) = %f;\n", fi++, creal(buf1));
#endif
        }

        if(group_samples >= (tmpl_length*ratio)) // TODO RICHTIG???
        {
            // get content of window buffer
            windowcf_read(window, &signal);

            // calculate convolution
        	calc_convolution(signal, sig_length, template, tmpl_length, correlation, corr_length);

			// find peaks in correlation
			update_detect(threshold*10, frequency_offset, corr_length);

#if(DEBUG)
			for (i=0; i<corr_length;  i++) fprintf(fout,"res(%3u) = %f;\n", i+fi_res+1, creal(correlation[i]));
//			fprintf(fout,"res(%3u) = %f;\n", i+fi_res+1, 20.0);
			fi_res += corr_length;
#endif

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

    // close debug output file
#if(DEBUG)
	fprintf(fout,"figure;\n");
	fprintf(fout,"plot(res);\n");
	fprintf(fout,"plot(signal);\n");
	fprintf(fout,"grid on;\n");
	fclose(fout);
#endif


    // clean up allocated objects
    msresamp2_crcf_destroy(resamp);
    agc_crcf_destroy(agc);
    windowcf_destroy(window);
    iirfilt_crcf_destroy(dcblock);


    printf("total samples in : %lu\n", total_samples);
#if(DEBUG)
    printf("total out : %lu\n", fi_res);
#endif

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
  tmp_average = 0;

  for (i = 0; i<_nPts; i=i+100)
  {
	  sum = 0.0; // zero output array
	  for (j=0; j<_tmpl_len; j++)
	  {
		  sum += _template[j] * cabs(_buf[i + j]);
	  }
	  _result[i] = creal(sum);

	  // calc average
	  tmp_average += (double)_result[i];
  }

  tmp_average /= (double)_nPts;
}

// look for signal
int update_detect(float _threshold, float _freq_offset, unsigned int _nPts)
{
    unsigned int i;
    int total=0;
    // find peak
    float tmp_PeakValue = 0;
    unsigned int tmp_Index = 0;
    struct timespec tmp_time;


    for (i=0; i<_nPts; i++)
    {
        // find values above threshold and find the highest value
		if( ((correlation[i] /*- tmp_average*/) > _threshold) && (correlation[i] > tmp_PeakValue))
		{

			tmp_PeakValue = correlation[i];
			tmp_Index = i;
			clock_gettime(CLOCK_REALTIME,&now);
	//            if (psd_time[i].tv_sec==INT_MAX) psd_time[i]=now;
			tmp_time=now;
		}
	}

    // is value above last peak?
    if(tmp_PeakValue > sig_detect.PeakValue)
    {
    	// save new peak temporarily until next loop
    	sig_detect.PeakValue = tmp_PeakValue- tmp_average;
    	sig_detect.Index = tmp_Index;
    	sig_detect.time = tmp_time;
    }
    else
    {
    	if(sig_detect.PeakValue > _threshold)
    	{
    		// calc timestamp and print out data

			char timestamp[30];
			format_timestamp(sig_detect.time, timestamp, 30);
			// float signal_bw   = get_group_bw(i)*_sampling_rate;            // bandwidth estimate (normalized)
			// float max_signal  = get_group_max_sig(i);						// maximum signal strength per group
			// float start_time  = num_transforms*timestep - duration; // approximate starting time
            printf("%s+%d;%-10.6f;%9.6f;%9.6f;%f;\n",timestamp, sig_detect.Index, 0.0000, _freq_offset, 0.0000, sig_detect.PeakValue);
//			printf("%s;%d;%f;%f;\n", timestamp, sig_detect.Index, _freq_offset, sig_detect.PeakValue);
			fflush(stdout);

			sig_detect.PeakValue = 0;
			sig_detect.Index = 0;
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
    sprintf(buffer, ".%-9ld", _time.tv_nsec);
    strncat(_buf, buffer, 10);
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



