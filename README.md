# dropTemp

`dropTemp` provides a cheap and small deployment to allow users to keep track of temperature and humidity data within their homes.

# Table of contents
  * [Requirements](#requirements)
  * [Installation](#installation)
  * [Usage](#usage)
  * [Potential improvements](#potential-improvements)


## Requirements
* LAMP/WAMP server with Node-RED installed.
* Arduino with the following:
  * Components:
    * DHT11
    * HC-05
    * OSOYOO ESP8266 WiFi shield
  * DHT sensor library
* Raspberry Pi with the following packages:
  * python
  * python-pip
  * bluez
  * bluez-utils

## Installation
1. Install packages within [requirements.txt](/Pi.requirements.txt) on the Raspberry Pi.
2. Place all [server](/Server) `.php` files in the proper directory for Apache to serve.
3. Create `config.php` within that same directory with the following structure:
```php
return array(
  'db_server' => *SERVER IP*,
  'db_user' => *MYSQL USERNAME*,
  'db_pass' => *MYSQL PASSWORD*,
  'db_name' => *MYSQL TABLE NAME*
)
```
4. Connect HC-05 and DHT11 to the corresponding pins on the ESP8266.
5. Upload [HC_to_DHT11.ino](/Arduino/HC_to_DHT11.ino) to the Arduino board.
6. Pair the Raspberry Pi with the HC-05 module using the following commands where X's are your HC-05's MAC address:
```
$ sudo bluetoothctl
pair XX:XX:XX:XX:XX:XX
quit
```
7. Create a MySQL database and create the following table:
```SQL
CREATE TABLE `data` (
  `id` int NOT NULL,
  `email` varchar(40) NOT NULL,
  `timestamp` varchar(15) NOT NULL,
  `temp` float NOT NULL,
  `humid` float NOT NULL
)

ALTER TABLE `data`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `data`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
```
8. Import Node-RED [flow](/Server/flows.json) and fill in credentials of your MySQL data table.

## Usage
1. Execute `startRead.sh` on the Raspberry Pi and follow the prompts.
2. View results on your web server using the email you registered with.

## Potential improvements
* Allow multiple `drops` to connect to a single controller.
  * Add a UUID to each drop to ensure unique values are posted.
  * Scale website to scale to amount of unique drops.
* Provide customizable emails alert functionality.
* Add opt-in passwords to lock out unauthorized drops.
