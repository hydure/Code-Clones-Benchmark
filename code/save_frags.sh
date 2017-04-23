#!/bin/bash -x
# save_frags.sh $detector $file $lang $datasetID $project1ID $project1URL ..

detector=$1
file=$2
datasetID=$4
shift; shift; shift; shift;

i=0
while [ "$#" -gt 1 ]; do
    projectID[$i]=$1; shift
    projectURL[$i]=$1; shift
    i=`expr $i + 1`
done

cd /home/pi/MyNAS/$detector
if [ ! -d "$datasetID" ]; then
    mkdir $datasetID
fi
cd $datasetID

if [ "$detector" = "nicad" ]; then
    files=`grep "Lines" $file | awk '{print $6}' | uniq | sed 's:.*/'$datasetID'/::'`

    j=0
    while [ "$j" -lt "$i" ]; do
        git clone ${projectURL[$j]} ${projectID[$j]}
        j=`expr $j + 1`
    done
elif [ "$detector" = "deckard" ]; then
    files=`grep "FILE" $file | awk '{print $4}' | uniq`

    j=0
    while [ "$j" -lt "$i" ]; do
        git clone ${projectURL[$j]} src/${projectID[$j]}
        rm -rf src/${projectID[$j]}/.git
        j=`expr $j + 1`
    done
fi

dir_files=`find . -type f | sed 's:^\./::'`

for f in $dir_files; do
    if [ "$files" != "$f" ]; then
        rm $f
    fi
done
find . -type d -empty -delete
