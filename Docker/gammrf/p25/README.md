
Using ΓRF for P25 Digital Trunking
======================================

* In the ΓRF client, run the p25rx module on a virtual device (type `mods`
if you're unsure about syntax.)  This will tell the client to accept
`trunk-recorder` log output on a UDP port, process it, and send it to the
server.

* Get `trunk-recorder` from [here](https://github.com/robotastic/trunk-recorder).
Make sure to use version 2.

* Configure.  Don't configure any analog or digital recorders (value should be '0')

* Build `trunk-recorder`: `# docker build .`  Make note of the ID given at the
end of the Docker output.

* Run:
`# docker run -d -it --net="host" --privileged -v /dev/bus/usb:/dev/bus/usb [ID] /bin/sh -c 'cd /src/trunk-recorder ; ./recorder'`

* Get the process ID: `# docker ps`

* Forward the logs: `# docker logs --tail 1 -f [PID] | nc -u 127.0.0.1 50000`
