#include "DHT.h"
#include <SoftwareSerial.h>
#define DHTPIN 9
#define DHTTYPE DHT11 

SoftwareSerial btSerial(4, 5); // TX, RX
DHT dht(DHTPIN, DHTTYPE);

float temp, humid;

void setup() {
  btSerial.begin(9600);
  Serial.begin(9600);
  btSerial.flush();
  Serial.flush();
  dht.begin();
}

void loop() {
  temp = dht.readTemperature();
  humid = dht.readHumidity();
  if (isnan(temp)||isnan(humid)){
    Serial.println("Error Reading values, retrying in 2 seconds");
  }
  else {
    String sending=String(temp,2)+","+String(humid,2)+"!";
    Serial.println(sending);
    char btSend[sending.length()+1];
    sending.toCharArray(btSend, sending.length()+1);
    btSerial.write(btSend);
  }
  delay(2000);
}
