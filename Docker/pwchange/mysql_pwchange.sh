#!/bin/sh
UNAME="rteu"

if [[ $# -ne 3 ]]; then
	printf "Usage: $0 <old_pw> <new_pw> <new_pw>\n"
	printf "Password was NOT changed!\n"
	exit 1
fi

mysql --host=127.0.0.1 --user=$UNAME --password=$1 -e "exit" 2>/dev/null
RETVAL=$?
if [[ $RETVAL -ne 0 ]]; then
        printf "Could not verify old password. Make sure old password is correct, and local MySQL server is reachable.\n"
        exit $RETVAL
fi

if [[ "$2" != "$3" ]]; then
        printf "Password verification failed: New passwords don't match.\n"
        exit 3
fi

mysql --host=127.0.0.1 --user=$UNAME --password=$1 -e "SET PASSWORD = PASSWORD('$2');"
RETVAL=$?
if [[ $RETVAL -ne 0 ]]; then
        printf "Password could not be changed!\n"
        exit $RETVAL
fi

printf "Password successfully changed.\n"
exit 0

