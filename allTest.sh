#!/bin/bash

for db in *.test
 do
       ./parser.sh $db
done