#!/bin/bash
# save_frags.sh $detector $file $lang $project1ID $project1URL ..

detector=$1
file=$2
shift; shift; shift;

i=0
while [ "$#" -gt 1 ]; do
    projectID[$i]=$1; shift
    projectURL[$i]=$1; shift
    i=`expr $i + 1`
done

cd /home/pi/MyNAS/$detector

if [ "$detector" = "nicad" ]; then
    datasetID=`echo $file | sed 's:.*/\([0-9]*\)\.html:\1:'`

    if [ ! -d "$datasetID" ]; then
        mkdir $datasetID
    fi

    cd $datasetID

    j=0
    while [ "$j" -lt "$i" ]; do
        git clone ${projectURL[$j]} ${projectID[$j]}
        j=`expr $j + 1`
    done

    files=`grep "Lines" $file | awk '{print $6}' | uniq | sed 's:.*/'$datasetID'/::'`
    dir_files=`find . -type f | sed 's:^\./::'`

    for f in $dir_files; do
        if [[ ! "$files" =~ $f ]]; then
            rm $f
        fi
    done
elif [ "$detector" = "deckard" ]; then
    datasetID=`echo $2 | sed 's/_out//'`
fi

