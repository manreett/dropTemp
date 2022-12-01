#!/bin/bash

sudo bluetoothctl -- trust 00:14:03:05:08:42
sleep 3
sudo rfcomm bind /dev/rfcomm0 00:14:03:05:08:42 1
sleep 4
python3 POSTtest.py
python3 HCRead.py