#!/bin/bash
if [ ! -d "ext" ]; then
	mkdir ext
	cd ext
	svn export http://sabreamf.googlecode.com/svn/trunk/ SabreAMF
fi
