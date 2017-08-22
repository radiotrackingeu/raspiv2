#!/bin/bash

IMAGE="319bacf33b33"
sudo docker run -it --net="host" --privileged $IMAGE
