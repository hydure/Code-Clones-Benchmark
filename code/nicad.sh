#!/bin/sh
#script file to be placed on the machine hosting NiCad

if [ "$#" -lt 3 ]; then
    echo "nicad.sh: less than 3 arguments"
    exit
fi

lang=$1; shift
datasetID=$1; shift

CC_BENCH=/home/f85/jskimko/cc_bench
NICAD_PATH=/home/f85/jskimko/NiCad-4.0
DATASET_PATH=/home/f85/jskimko/cc_bench/$datasetID

i=0
while [ "$#" -gt 1 ]; do
    projectID[$i]=$1; shift
    projectURL[$i]=$1; shift
    i=`expr $i + 1`
done

cd $CC_BENCH
mkdir $datasetID; cd $datasetID

j=0
while [ "$j" -lt "$i" ]; do
    git clone ${projectURL[$j]} ${projectID[$j]}
    j=`expr $j + 1`
done

cd $NICAD_PATH
./nicad4 functions $lang $DATASET_PATH defaultreport &>/dev/null
cat ${DATASET_PATH}_functions-clones/*.html
rm -rf $DATASET_PATH
