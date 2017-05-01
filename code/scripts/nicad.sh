#!/bin/bash 
# script file to be placed on the machine hosting NiCad

CC_BENCH=/home/clone/cc_bench/nicad
NICAD_PATH=/home/clone/NiCad-4.0

if [ "$#" -lt 3 ]; then
    echo "nicad.sh: less than 3 arguments"
    exit
fi

if [ ! -d "$CC_BENCH" ]; then
    mkdir -p $CC_BENCH
fi

if [ ! -f "$NICAD_PATH/nicad4" ]; then
    echo"nicad.sh: cannot find nicad4"
    exit
fi

lang=$1; shift
datasetID=$1; shift
DATASET_PATH=$CC_BENCH/$datasetID

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

mv ${DATASET_PATH}_functions-clones/*.html $CC_BENCH
cd $DATASET_PATH/../
rm -rf `ls -1 | grep -v "html"`
