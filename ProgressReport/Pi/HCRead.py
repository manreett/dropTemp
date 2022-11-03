import serial, time
import sys

ser = serial.Serial()
ser.port = "/dev/rfcomm0"
ser.baudrate = 115200
ser.timeout = 1
ser.setDTR(False)
ser.setRTS(False)
ser.open()
c=0
totalTemp=0
totalHumid=0
ser.flushInput()
ser.flush()
ser.flushOutput()


print("Start")
while True:
  raw_data = ser.readline()
  try:
    ser.flushInput()
    decoded_data=raw_data.decode("utf-8")
    ser.flush()
  except ValueError:
    pass

  if len(decoded_data)!=0:
    ind = decoded_data.find(",")
    end = decoded_data.find("!")
    temperature = float(decoded_data[:ind])
    humid = float(decoded_data[ind + 1:end])
    totalTemp+=temperature
    totalHumid+=humid
    c+=1

  if c==30:
    avgTemp=round(totalTemp/30,2)
    avgHumid=round(totalHumid/30,2)
    c=0
    print(str(avgTemp)+" - "+str(avgHumid)+" - "+str(int(time.time())))
    totalTemp=0
    totalHumid=0

  time.sleep(.05)
