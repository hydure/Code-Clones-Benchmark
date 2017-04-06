#!/bin/sh

ssh -o StrictHostKeyChecking=no jskimko@homer.cs.wm.edu "/home/f85/jskimko/nicad.sh $1" | grep -v "known hosts"
