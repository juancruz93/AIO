#!/bin/bash

echo "Starting import process in background"

(nohup forever start /websites/aio/app/servers/node/sms/serversms/bin/www 1>/dev/null 2>&1)&

echo "Done!"