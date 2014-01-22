#!/bin/sh
####################################
# Run migration script
####################################
OPTION1="$1"
OPTION2="$2"
OPTION2="$3"
DIR=$(cd $(dirname "$0"); pwd)
php $DIR/migrate.php $OPTION1 $OPTION2 $OPTION3 