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

require_once dirname(__FILE__).'/../../autoload.php';

echo "Source metadata: ";
$metaReader = new Yamdi_FlvMetadataReader();
$metaReader->run($filename);
print_r($metaReader->exportMetadata());

$new_filename = pathinfo($filename, PATHINFO_FILENAME)."_keyframes.flv";

$flv = new Yamdi_FlvKeyframesInjector();
$flv->run($filename, $new_filename);

echo "\nWritten $new_filename\n";
echo "Metadata: ";
$metaReader->run($new_filename);
print_r($metaReader->exportMetadata());