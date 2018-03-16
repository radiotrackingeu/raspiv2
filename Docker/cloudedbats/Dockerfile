FROM microphone

MAINTAINER ralf.zeidler@radio-tracking.eu

# Instructions: https://github.com/cloudedbats/cloudedbats_wurb/blob/master/doc/software_installation_cloudedbats-wurb.md

RUN apt-get update && \
    apt-get install -y git portaudio19-dev gpsd gpsd-clients usbmount && \
						python3 python3-pip python3-numpy python3-scipy && \
						python3-all-dev python3-rpi.gpio ntp

RUN pip3 install pyaudio gps3 python-dateutil pyusb pytz

COPY /etc/default/gpsd 
COPY /etc/usbmount/usbmount.conf
# /lib/systemd/system/systemd-udevd.service ? check

RUN systemctl enable ntp && \
	timedatectl set-ntp 1
	


RUN mkdir cloudedbats && \
	cd cloudedbats/ && \
	git clone https://github.com/cloudedbats/cloudedbats_wurb.git
	
# CloudedBats. Scripts to start. 
sudo -u pi python3 /cloudedbats/cloudedbats_wurb/wurb_raspberry_pi/control_raspberrypi_by_gpio.py &
sudo -u pi python3 /cloudedbats/cloudedbats_wurb/wurb_main.py &
# sudo -u pi python3 /cloudedbats/cloudedbats_wurb/wurb_main_no_usb.py &

cat /home/pi/cloudedbats/cloudedbats_wurb/wurb_log_files/wurb_log.txt
cat /home/pi/cloudedbats/cloudedbats_wurb/wurb_log_files/raspberry_pi_gpio_control_log.txt