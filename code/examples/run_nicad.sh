#!/bin/sh

sshpass -p $2 ssh -o StrictHostKeyChecking=no $1@pepe.cs.wm.edu "/home/f85/$1/nicad.sh $3"
