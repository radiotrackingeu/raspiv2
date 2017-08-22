#!/bin/bash
i=`docker build . | tail -1 | cut -d ' ' -f 3`
sed -i "s/^IMAGE=.*/IMAGE=\"$i\"/" ./rungrf.sh
echo $i
