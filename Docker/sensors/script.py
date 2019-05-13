import mysql.connector
import io
import os
import time
import datetime
import socket
import psutil
import smbus
import getopt
import sys

def getserial():
# Extract serial from cpuinfo file
  cpuserial = "0000000000000000"
  try:
    f = open('/proc/cpuinfo','r')
    for line in f:
      if line[0:6]=='Serial':
        cpuserial = line[10:26]
    f.close()
  except:
    cpuserial = "ERROR000000000"

  return cpuserial

def main(argv):

    db_hostname = 'db.radio-tracking.eu'
    db_port = 3306
    db_user = 'rteu'
    db_password = 'rteuv2!'
    db_database = 'rteu'

    hostname = ''

    try:
        opts, args=getopt.getopt(argv,"h:u:p:d:n:",["help"])
    except getopt.GetoptError:
        print('script.py -h <db_hostname> -P <db_port> -u <db_user> -p <db_password> -d <db_database> -n <local_hostname>')
        sys.exit(2)
    for opt, arg in opts:
        if opt == '-h':
            db_hostname = arg
        elif opt == '-P':
            db_port = arg
        elif opt == '-u':
            db_user = arg
        elif opt == '-p':
            db_password = arg
        elif opt == '-d':
            db_database = arg
        elif opt == '-n':
            hostname = arg
        elif opt == '--help':
            print('script.py -h <db_hostname> -u <db_user> -p <db_password> -d <db_database> -n <local_hostname>')
            sys.exit()
            
    print('Hostname:\t\t'+hostname)
    
    #serial number
    SN = getserial()
    print('Pi SN:\t\t\t'+SN)

    df=psutil.disk_usage('/')[3]
    print('disk spaced used:\t' + str(df)+'%')

    #CPU temperature
    f = open("/sys/class/thermal/thermal_zone0/temp","r")
    cpu_temp = str(float(f.readline())/1000)
    print('CPU temp: \t\t' + str(cpu_temp))
    
    #CPU and memory load
    cpu_load = psutil.cpu_percent()
    mem_load = psutil.virtual_memory()[2]
    print('CPU load:\t\t'+str(cpu_load))
    print('Memory load:\t\t'+str(mem_load))

    # Time Pi
    time_pi = str(datetime.datetime.now())
    print('Time:\t\t\t'+time_pi)

    # Get I2C bus
    bus = smbus.SMBus(1)

    # ADC121C021 address, 0x50(80)
    # Select configuration register, 0x02(02)
    #               0x20(32)        Automatic conversion mode enabled
    bus.write_byte_data(0x50, 0x02, 0x20)

    time.sleep(0.5)

    # ADC121C021 address, 0x50(80)
    # Read data back from 0x00(00), 2 bytes
    # raw_adc MSB, raw_adc LSB
    data = bus.read_i2c_block_data(0x50, 0x00, 2)

    # Convert the data to 12-bits
    raw_adc = (((data[0] & 0x0F) << 8 | data[1]) & 0xFFF)*2*3000/4096
    print('Voltage:\t\t'+str(raw_adc))

    query = "INSERT INTO `sensors` (`hostname`, `PiSN`, `timestamp`, `cpu_temp`, `cpu_load`, `mem_load`, `battery_voltage`, `disk_space_used`) VALUES ('"+hostname+"','"+SN+"','"+time_pi+"','"+str(cpu_temp)+"','"+str(cpu_load)+"','"+str(mem_load)+"','"+str(raw_adc)+"','"+str(df)+"')"
    #print(query)
    
    # Verbindung zur Datenbank und Dateneintrag
    mydb = mysql.connector.connect(
        host = db_hostname,
        port = db_port,
        user = db_user,
        passwd = db_password,
        database = db_database
    )
    mycursor =mydb.cursor()
    mycursor.execute(query)
    mydb.commit()

if __name__ == "__main__":
    main(sys.argv[1:])