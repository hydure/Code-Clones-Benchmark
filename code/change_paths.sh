#!/usr/bin/env bash

# change paths to storage directory
main_dir=`readlink -f $0 | sed 's:\(.*/\).*:\1:'`
old_dir=/home/pi/MyNAS

read -p "Please enter the full path to a directory to store project and clone files: " input
new_dir=`echo $input | sed 's:/$::'`

first=`grep "$old_dir" $(find $main_dir -type f) | grep -v $0 |  wc -l`
if [[ $first -eq 0 ]]; then
    read -p "Please enter the full path of the previously entered directory: " input
    old_dir=`echo $input | sed 's:/$::'`
fi

matches=`grep -l "$old_dir" $(find $main_dir -type f) | grep -v $0`
sed -i 's:'$old_dir':'$new_dir':' $matches
