#!/usr/bin/env python
# -*- coding: utf-8 -*-

import deepzoom
import os

os.listdir('.')

for element in os.listdir('.'):
	if element.endswith('.JPG') or element.endswith('.jpg'):
		name,ext=element.split(".")

		# Specify your source image
		SOURCE = element
		DESTINATION=name+".dzi"

		# Create Deep Zoom Image creator with weird parameters
		creator = deepzoom.ImageCreator(tile_size=128, tile_overlap=2, tile_format="png",image_quality=0.8, resize_filter="bicubic")

		# Create Deep Zoom image pyramid from source
		creator.create(SOURCE,DESTINATION)
	    #    print("succes" )
	#else:
     #	 	print("fail" )