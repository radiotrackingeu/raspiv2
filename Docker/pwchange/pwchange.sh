#!/bin/sh
FILE="/tmp/pwfile"
UNAME="pi"

if [[ $# -ne 3 ]]; then
	printf "Usage: $0 <old_pw> <new_pw> <new_pw>\n"
	printf "Password was NOT changed!"
	exit 1
fi

echo "$1" | htpasswd -vi $FILE $UNAME
RETVAL=$?
if [[ $RETVAL -ne 0 ]]; then
        printf "Old password is incorrect.\n"
        exit $RETVAL
fi

if [[ "$2" != "$3" ]]; then
        printf "Password verification failed: New passwords don't match.\n"
        exit 3
fi

echo "$2" | htpasswd -i $FILE $UNAME
RETVAL=$?
if [[ $RETVAL -ne 0 ]]; then
        printf "Password could not be changed!\n"
        exit $RETVAL
fi

printf "Password successfully changed.\n"
exit 0

