import serial, time, re, configparser, requests
from os.path import exists

config = configparser.ConfigParser()
regex = r'\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b'
email = ""
ser = serial.Serial()
ser.port = "/dev/rfcomm0"
ser.baudrate = 115200
ser.timeout = 1
ser.setDTR(False)
ser.setRTS(False)
ser.open()
c=-1
totalTemp=totalHumid=0.0
ser.flushInput()
ser.flush()
ser.flushOutput()

def emailcheck(emailchk):
    if re.fullmatch(regex, emailchk):
        return True
    else:
        return False


if exists("login.ini"):
    config.read_file(open('login.ini'))
    email = config['login']['email']
    print("Logged in as: "+config['login']['email'])
else:
    print("Please enter your email to login to the Drop system with: ")
    email = input()
    while not emailcheck(email):
        print("Not a valid email: ")
        email = input()
    config['login'] = {
        'email': email
    }
    with open('login.ini', 'w') as cfgfile:
        config.write(cfgfile)

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
    if c==-1:
        r=requests.post('https://droptemp.online:1880/submit', json={
        "email": email,
        "temp": temperature,
        "humid": humid
        })
        print(f"Initial post: Status Code: {r.status_code}, Response: {r.json()}")
        totalTemp=totalHumid=0.0
        c+=1
    c+=1

  if c==30:
    avgTemp=round(totalTemp/30,2)
    avgHumid=round(totalHumid/30,2)
    c=0
    r=requests.post('https://droptemp.online:1880/submit', json={
    "email": email,
    "temp": avgTemp,
    "humid": avgHumid
    })
    print(f"Status Code: {r.status_code}, Response: {r.json()}")
    totalTemp=0
    totalHumid=0

  time.sleep(.05)
