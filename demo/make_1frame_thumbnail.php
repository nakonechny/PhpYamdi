#!/usr/bin/php
<?php

if ($_SERVER['argc'] !== 2) {
	echo "Usage: ", basename(__FILE__), " <path_to_flv_file>\n";
	exit;
}

$filename = @$_SERVER['argv'][1];
if(! file_exists($filename)) {
	echo "File ", $filename, " not found\n";
	exit;
}

require_once dirname(__FILE__).'/../autoload.php';

$new_filename = pathinfo($filename, PATHINFO_FILENAME)."_thumb.flv";

$thumb = new Yamdi_FlvThumbnailMaker();
$thumb->run($filename, $new_filename);

echo "Written $new_filename\n";
