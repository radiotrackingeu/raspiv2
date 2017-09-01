#!/bin/bash

cd tmp
AUDIODEV=hw:1 rec -c1 -r 22000 record.wav sinc 400 silence 1 0.1 1% trim 0 5
sox record.wav -n spectrogram -t record.wav -o record.png