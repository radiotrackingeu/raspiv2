//
// rtlsdr_signal_detect.c : detect signals within captured rtlsdr sample file
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

int nfft;
float *psd_template;
float *psd;
float *psd_max;
int   *detect;
int   *count;
int   *groups;
int   timestep;
unsigned long int num_transforms = 0;
int tmp_transforms = 0;


// print usage/help message
void usage()
{
    printf("%s [options]\n", __FILE__);
    printf("  -h         : print help\n");
    printf("  -i <file>  : input data filename\n");
    printf("  -t <thsh>  : detection threshold above psd, default: 10 dB\n");
    printf("  -p         : use STDIN as input\n");
    printf("  -s <rate>  : sampling rate in Hz, default: 250000Hz\n");
    printf("  -f <frac>  : set timestep as fraction of nfft, ie timestep = nfft/<frac>. default: 400/[1mbold8[0m=50\n");
	printf("  -n         : number of bins used for fft, default: 400\n");
}

// read samples from file and store into buffer
unsigned int buf_read(FILE *          _fid,
                      float complex * _buf,
                      unsigned int    _buf_len);

// forward declaration of methods for signal detection
int   update_detect(float _threshold);
int   update_count();
int   update_groups();
int   signal_complete  	(int _group_id);
float get_group_freq   	(int _group_id);
float get_group_bw     	(int _group_id);
float get_group_time   	(int _group_id);
float get_group_max_sig (int _group_id);
int   clear_group_count	(int _group_id);
int   step(float _threshold, unsigned int _sampling_rate);
void  get_timestamp(char * _buf, unsigned long _buf_len);
void  free_memory();

// main program
int main(int argc, char*argv[])
{
    char            filename_input[256] = "data/zeidler-2017-08-06/g10_1e_120kHz.dat";
    float           threshold           = 10.0f; //-60.0f;
    char            read_from_stdin     = 0;
    unsigned long   sampling_rate       = 250000;
    int             nfft                = 400;
    int             timestep_factor     = 8;

    // read command-line options
    int dopt;
    while ((dopt = getopt(argc,argv,"hi:t:s:pn:f:")) != EOF) {
        switch (dopt) {
        case 'h': usage();                              return 0;
        case 'i': strncpy(filename_input,optarg,256);   break;
        case 't': threshold = atof(optarg);             break;
        case 's': sampling_rate = atoi(optarg);         break;
        case 'p': read_from_stdin = 1;                  break;
		case 'n': nfft = atoi(optarg);         			break;
        case 'f': timestep_factor = atoi(optarg);       break;
        default:  exit(1);
        }
    }
    timestep = nfft/timestep_factor;
    fprintf(stderr,"Setting nfft=%d and timestep=%d\n",nfft,timestep);

    psd_template = (float*) calloc(nfft, sizeof(float));
    psd          = (float*) calloc(nfft, sizeof(float));
	psd_max      = (float*) calloc(nfft, sizeof(float));
    detect       = (int*)   calloc(nfft, sizeof(int  ));
    count        = (int*)   calloc(nfft, sizeof(int  ));
    groups       = (int*)   calloc(nfft, sizeof(int  ));

    // create spectrogram
    spgramcf periodogram = spgramcf_create(nfft, LIQUID_WINDOW_HAMMING, nfft/2, timestep);

    // buffer
    unsigned int  buf_len = 64;
    float complex buf[buf_len];

    // DC-blocking filter 1e-3f
    iirfilt_crcf dcblock = iirfilt_crcf_create_dc_blocker(1e-3f);

    // open input file
    FILE * fid;
    if (read_from_stdin){
        fprintf(stderr,"Reading from stdin.\n");
        fid = stdin;
    } else {
        fid = fopen(filename_input,"r");
        if (fid == NULL) {
	        fprintf(stderr,"error: could not open %s for reading\n", filename_input);
        	exit(-1);
	}
    }

    // continue processing as long as there are samples in the file
    unsigned long int total_samples  = 0;
    num_transforms = 0;
    do
    {
        // read samples into buffer
        unsigned int r = buf_read(fid, buf, buf_len);
        if (r != buf_len)
            break;

        // apply DC blocking filter
        iirfilt_crcf_execute_block(dcblock, buf, buf_len, buf);

        // accumulate spectrum
        spgramcf_write(periodogram, buf, buf_len);

		//get number of transforms per cycle tmp_transforms
		tmp_transforms = spgramcf_get_num_transforms(periodogram);

        if (tmp_transforms >= 16)
        {
            // compute power spectral density output
            spgramcf_get_psd(periodogram, psd);

            // compute average template
            if (num_transforms==0) {
                // set template PSD for relative signal detection
                memmove(psd_template, psd, nfft*sizeof(float));
				memmove(psd_max, psd, nfft*sizeof(float));
            } else {
                // detect differences between current PSD estimate and template
                step(threshold, sampling_rate);
            }

            // update counters and reset spectrogram object
            num_transforms += spgramcf_get_num_transforms(periodogram);
            spgramcf_reset(periodogram);
        }

        // update total sample count
        total_samples += buf_len;

    } while (!feof(fid));

    // close input files
    fclose(fid);

    // write accumulated PSD
    spgramcf_destroy(periodogram);
    iirfilt_crcf_destroy(dcblock);

    printf("total samples in : %lu\n", total_samples);
    printf("total transforms : %lu\n", num_transforms);

    free_memory();
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

// update detect
int update_detect(float _threshold)
{
    int i;
    int total=0;
    for (i=0; i<nfft; i++) {
        // relative
        // detect[i] = ((psd[i] - psd_template[i]) > _threshold) ? 1 : 0;
		if((psd[i] - psd_template[i]) > _threshold){
			detect[i]=1; //write matrix for detection
			psd_max[i] = (psd_max[i]>psd[i]) ? psd_max[i] : psd[i]; //save highes values
			printf("test treshold /n");
		}
		else{
			detect[i]=0;
		}
        // absolute
        //detect[i] = (psd[i] > _threshold) ? 1 : 0;
        total += detect[i];
    }
    return total;
}

// update count
int update_count()
{
    int i;
    int total=0;
    for (i=0; i<nfft; i++) {
        count[i] += detect[i];
        total += count[i];
    }
    return total;
}

// update groups
int update_groups()
{
    // replace all non-zero entries with a 1
    int i;
    for (i=0; i<nfft; i++)
        groups[i] = count[i] > 0; //Was macht dieser Operator?

    // look for adjacent groups and refactor...
    int group_id = 0;
    i = 0;
    while (i < nfft) {
        // find non-zero value
        if (count[i] == 0) {
            i++;
            continue;
        }

        //
        group_id++;
        while (count[i] > 0 && i <nfft) {
            groups[i] = group_id;
            i++;
        }
    }

    // return number of groups
    return group_id;
}

// update signal detection
int signal_complete(int _group_id)
{
    int i;
    for (i=0; i<nfft; i++) {
        if (groups[i] == _group_id && detect[i])
            return 0;
    }
    return 1;
}

// get group center frequency
float get_group_freq(int _group_id)
{
    int i, n=0;
    float fc = 0.0f;
    for (i=0; i<nfft; i++) {
        if (groups[i] == _group_id) {
            fc += ((float)i/(float)nfft - 0.5f) * count[i];
            n  += count[i];
        }
    }
    return fc / (float)n;
}

// get group bandwidth
float get_group_bw(int _group_id)
{
    int i, imin=-1, imax=-1;
    for (i=0; i<nfft; i++) {
        if (groups[i] == _group_id) {
            if (i < imin || imin == -1) imin = i;
            if (i > imax || imax == -1) imax = i;
        }
    }
    if (imin == -1 || imax == -1)
        return 0.0f;

    return (float)(imax - imin + 1) / (float)nfft;
}

// get group maximum count from group
float get_group_time(int _group_id)
{
    int i;
    int max = -1;
    for (i=0; i<nfft; i++) {
        if (groups[i] == _group_id && count[i] > max)
            max = count[i];
    }
    return max;
}

// get group maximum signal from group
float get_group_max_sig(int _group_id)
{
    int i;
    float max = -1000;
    for (i=0; i<nfft; i++) {
        if (groups[i] == _group_id && psd_max[i] > max)
            max = psd_max[i];
    }
    return max;
}

// clear count and max for group
int clear_group_count(int _group_id)
{
    int i;
    for (i=0; i<nfft; i++) {
        if (groups[i] == _group_id)
            count[i] = 0;
			psd_max[i] = -1000;
    }
    return 0;
}

// look for signal
int step(float _threshold, unsigned int _sampling_rate)
{
    update_detect(_threshold);
    update_count ();
    int num_groups = update_groups();
    char timestamp[30];
    // determine if signal has stopped based on group and detection
    int i;
    for (i=1; i<=num_groups; i++) {
        if (signal_complete(i)) {
            // signal started & stopped
            get_timestamp(timestamp, 30);
            float duration    = tmp_transforms*get_group_time(i)*timestep/_sampling_rate; // duration [samples]
			float signal_freq = get_group_freq(i)*_sampling_rate;          // center frequency estimate (normalized)
            float signal_bw   = get_group_bw(i)*_sampling_rate;            // bandwidth estimate (normalized)
			float max_signal  = get_group_max_sig(i);						// maximum signal strength per group
//            float start_time  = num_transforms*timestep - duration; // approximate starting time
            printf("signal detected! time=%s, duration=%-10.6f, freq=%9.6f, bw=%9.6f, strength=%f\n",
                    timestamp, duration, signal_freq, signal_bw,max_signal);
			fflush(stdout);

            // reset counters for group
            clear_group_count(i);
        }
    }
    return 0;
}

void get_timestamp(char * _buf, unsigned long _buf_len)
{
    struct timespec time;
    char buffer[11];
    clock_gettime(CLOCK_REALTIME, &time);
    const time_t tm = (time_t) time.tv_sec;
    strftime(_buf, _buf_len, "%F %T",gmtime(&tm));
    sprintf(buffer, ".%-9ld", time.tv_nsec);
    strncat(_buf, buffer, 10);
}

//release allocate memory space
void free_memory()
{
    free(psd_template);
    free(psd);
    free(psd_max);
    free(detect);
    free(count);
    free(groups);
}