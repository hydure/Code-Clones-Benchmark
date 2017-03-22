#!/bin/sh

git ls-remote $1 | head -1 | awk '{print $1}'
