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

$metaReader = new Yamdi_FlvMetadataReader();
$metaReader->run($filename);
print_r($metaReader->exportMetadata());
