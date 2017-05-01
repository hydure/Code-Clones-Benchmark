#!/usr/bin/env bash

main_dir=`readlink -f $0 | sed 's:\(.*/\).*:\1:'`
read -p "Would you like to change the storage directory? [Y/n] " input

if [[ "$input" == "Y" ]] || [[ "$input" == "y" ]]; then
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

    echo "Done"; echo
fi

read -p "Will you be running NiCad and Deckard remotely? [Y/n] " input

if [[ "$input" == "Y" ]] || [[ "$input" == "y" ]]; then
    old_dir=/home/clone

    read -p "Please enter the full path to the directory you will be running from: " input
    new_dir=`echo $input | sed 's:/$::'`

    first=`grep "$old_dir" $(find $main_dir -type f) | grep -v $0 | grep -v 'examples/' |  wc -l`
    if [[ $first -eq 0 ]]; then
        read -p "Please enter the full path of the previously entered directory: " input
        old_dir=`echo $input | sed 's:/$::'`
    fi

    matches=`grep -l "$old_dir" $(find $main_dir -type f) | grep -v $0 | grep -v 'examples/'`
    sed -i 's:'$old_dir':'$new_dir':' $matches

    # don't delete output files from remote host
    sed -i 's/^rm ${/#rm ${/' $main_dir/scripts/nicad.sh $main_dir/scripts/deckard.sh

    echo "Done"
elif [[ "$input" == "N" ]] || [[ "$input" == "n" ]]; then
    # delete redundant output files on host
    sed -i 's/^#rm/rm/' $main_dir/scripts/nicad.sh $main_dir/scripts/deckard.sh
fi

read -p "Please enter the full path up to and including the 'NiCad-4.0' directory: " input
nicad_path=`echo $input | sed 's:/$::'`
sed -i 's:^NICAD_PATH=.*:NICAD_PATH='$nicad_path':' $main_dir/scripts/nicad.sh
echo "Done"

read -p "Please enter the full path up to and including the 'Deckard' directory: " input
deckard_path=`echo $input | sed 's:/$::'`
sed -i 's:^DECKARD_PATH=.*:DECKARD_PATH='$deckard_path':' $main_dir/scripts/deckard.sh
echo "Done"
