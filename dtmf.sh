#!/bin/bash
cp ami_dtmf.php /var/www/html/
cp call_dtmf.php /var/www/html
cp send_queue.php /var/www/html
cp dtmf.service /etc/systemd/system
cp features_applicationmap_custom.conf /etc/asterisk
cp extensions_custom.conf /etc/asterisk

chown asterisk:asterisk /etc/asterisk/features_applicationmap_custom.conf
chown asterisk:asterisk /etc/asterisk/extensions_custom.conf

chmod +x /var/www/html/ami_dtmf.php
chmod +x /var/www/html/call_dtmf.php
chmod +x /var/www/html/send_queue.php

sed -i 's/\r$//' /var/www/html/send_queue.php

systemctl daemon-reload

service dtmf start

cron_job_a="* * * * * /var/www/html/send_queue.php"
cron_job_b="* * * * * sleep 30; /var/www/html/send_queue.php"

if crontab -l | grep -Fq "$cron_job_a"; then
    echo "Cron job A already exists."
else
    (crontab -l 2>/dev/null; echo "$cron_job_a") | crontab -
    echo "Cron job A added successfully."
fi

#if crontab -l | grep -Fq "$cron_job_b"; then
#    echo "Cron job B already exists."
#else
#    (crontab -l 2>/dev/null; echo "$cron_job_b") | crontab -
#    echo "Cron job B added successfully."
#fi
