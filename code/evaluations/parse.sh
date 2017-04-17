#!/bin/sh

if [ "$#" != 1 ] || [ ! -f "$1" ]; then
    echo "usage: $0 nicad_html"
    exit
fi

num_classes=`grep "Number" $1 | awk '{print $4}'`
total_frags=`grep "Lines" $1 | wc -l`

sum_frags=0
args=""
for i in `seq $num_classes`; do
    num_frags=`grep "Clone class $i" $1 | awk '{print $4}'` 
    sim=`grep "Clone class $i" $1 | awk '{print $11}' | sed 's:%::'`
    args="$args $num_frags $sim"
    for j in `seq $num_frags`; do
        clone=`grep "Lines" $1 | head -$(($sum_frags+$num_frags)) | \
            tail -$num_frags | head -$j | tail -1`
        datasetID=`echo $clone | awk -F/ '{print $6}'`
        projectID=`echo $clone | awk -F/ '{print $7}'`
        file=`echo $clone | awk '{print $6}' | sed 's:.*cc_bench/nicad/[0-9]*/[0-9]*/::'`
        st=`echo $clone | awk '{print $2}'`
        end=`echo $clone | awk '{print $4}'`
        args="$args $datasetID $projectID $file $st $end"
    done
    sum_frags=$sum_frags+$num_frags
done

echo $args
#python gen_pairs.py $args
