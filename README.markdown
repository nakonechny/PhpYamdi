README
======

What is PhpYamdi?
-----------------

PhpYamdi is a flv utility inspired by [yamdi][1], that is well known metadata injector for flv media files.

With PhpYamdi you can not only inject keyframes positions to flv, but easily manage all metadata as well. 
Read metadata from any flv file. Write your own structures. Data is interfaced as native php types, no need to serialize or prepare it.

And even more. You can make a one-frame flv, to be used as thumbnail in flash player. It does not require third-party video-to-image convertors installed.

Demo
----

There are some scripts eliminating sample usage of PhpYamdi classes in demo/ folder.
Run following commands to see how it works:

	# cd demo/
	# php dump_meta.php barsandtone.flv
	# php inject_keyframes.php barsandtone.flv
	# php make_1frame_thumbnail.php barsandtone.flv

Requirements
------------

PhpYamdi is supported on [PHP][3] 5.2 and up.

Requires [SabreAMF][2] library to be located in ext/SabreAMF.
You can run init_externals.sh script to svn-export that from code.google.com automatically (requires subversion installed).

[1]: http://yamdi.sourceforge.net "Original yamdi utility written in C"
[2]: http://osflash.org/sabreamf "SabreAMF classes"
[3]: http://www.php.net