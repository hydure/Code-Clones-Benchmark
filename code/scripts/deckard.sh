#!/bin/bash
# script file to be placed on the machine hosting Deckard

CC_BENCH=/home/clone/cc_bench/deckard
DECKARD_PATH=/home/clone/Deckard

if [ "$#" -lt 3 ]; then
    echo "deckard.sh: less than 3 arguments"
    exit
fi

if [ ! -d "$CC_BENCH" ]; then
    mkdir -p $CC_BENCH
fi

if [ ! -f "$DECKARD_PATH/scripts/clonedetect/deckard.sh" ]; then
    echo"deckard.sh: cannot find deckard.sh"
    exit
fi

if [ ! -f "$DECKARD_PATH/config" ]; then
    echo"deckard.sh: cannot find config"
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
mkdir -p $datasetID/src; cd $datasetID

j=0
while [ "$j" -lt "$i" ]; do
    git clone ${projectURL[$j]} src/${projectID[$j]}
    j=`expr $j + 1`
done

cp $DECKARD_PATH/config .
sed -i 's/@LANG@/\*\.'$lang'/' config
$DECKARD_PATH/scripts/clonedetect/deckard.sh &> /dev/null

err=`grep Usage times/*`
if [ ! -z "$err" ]; then
    echo 'Error: there are no *.'$lang' files in this dataset.'
else
    cat clusters/cluster_vdb_30_0_allg_0.95_30
    mv clusters/cluster_vdb_30_0_allg_0.95_30 $CC_BENCH
fi

rm -rf $DATASET_PATH
