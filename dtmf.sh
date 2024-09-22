#!/bin/bash

cp ami_dtmf.php /var/www/html
cp call_dtmf.php /var/www/html
cp dtmf.service /etc/systemd/system
cp features_applicationmap_custom.conf /etc/asterisk
cp extensions_custom.conf /etc/asterisk

chown asterisk:asterisk /etc/asterisk/features_applicationmap_custom.conf
chown asterisk:asterisk /etc/asterisk/extensions_custom.conf

chmod +x /var/www/html/ami_dtmf.php
chmod +x /var/www/html/call_dtmf.php

systemctl daemon-reload

service dtmf start