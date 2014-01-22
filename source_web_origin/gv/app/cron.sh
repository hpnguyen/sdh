#!/bin/sh
####################################
# Run cron script
####################################
OPTION1="$1"
OPTION2="$2"
DIR=$(cd $(dirname "$0"); pwd)
clear
NOW=$(date +"%d-%m-%Y %r")
echo "[$NOW] $OPTION1 $OPTION2"
php $DIR/cron.php $OPTION1 $OPTION2