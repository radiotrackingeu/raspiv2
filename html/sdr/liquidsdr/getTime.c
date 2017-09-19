#include <stdio.h>
#include <time.h>

int main () {
        struct timespec time, res;
        clock_getres(CLOCK_REALTIME,&res);
        clock_gettime(CLOCK_REALTIME,&time);
        time_t time2 = (time_t) time.tv_sec;
        char  strtime[50];
        strftime(strtime,50, "%F %T",gmtime(&time2));
        printf("%s.%ld\n", strtime,time.tv_nsec);
        printf("Time in s:\t%lld.%.9ld\n",(long long)time.tv_sec,(long int) time.tv_nsec);
        printf("Time Resolution: %ld ns\n",(unsigned long)res.tv_nsec);
        return 0;
}
