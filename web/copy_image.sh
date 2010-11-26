#!/bin/bash

usage () {
	echo "copy_image.sh repo_path image_path"
}

# Handles Parameters
if [ ${#} -lt 2 ] ; then usage ;
else
	
	# Get Image Path
	image_path=${1}
	image=`basename ${1}`
	repo_path=${2}

	# Git Stuff
	cd repo_path
	
	git pull origin master
	
	# Go to the image directory (creating if necessary)
	cd images
	if [ ${?} != 0 ]; then
		mkdir images
		cd images
	fi

	# Copy the image over
	cp -f $image_path ./

	# Git Madness
	git add .
	git commit -m "Updated $image"
	git push origin master

	# Push and pull until the system is happy :)
	while [ ${?} != 0 ] ; do
		git pull origin master
		git push origin master
	done
fi
	
