import serial, requests
ser = serial.Serial()
ser.port = "/dev/rfcomm0"
ser.baudrate = 115200
ser.timeout = 1
ser.setDTR(False)
ser.setRTS(False)
ser.open()
email="test@test.test"
temperature=humid=0.0
ser.flushInput()
ser.flush()
ser.flushOutput()
done = False
while (done == False):
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

    if (temperature==0):
        print(decoded_data)
    else:
        r=requests.post('https://droptemp.online:1880/submit', json={
        "email": email,
        "temp": temperature,
        "humid": humid
        })
        print(f"Status Code: {r.status_code}, Response: {r.json()}")
        done = True
