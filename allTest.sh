#!/bin/bash

for db in *.test
 do
       ./parser.sh $db
       sleep 1
done