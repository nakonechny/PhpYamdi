<?

/*
 * Setting up path to library containing SabreAMF
 * @see http://code.google.com/p/sabreamf/
 * @see http://osflash.org/sabreamf
 */
$path = '/home/allen/workspace2/lib';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once dirname(__FILE__).'/Yamdi/FlvFile.php';

/*
 * Usage example
 */
$flv = new Yamdi_FlvFile();
$flv->run(
	dirname(__FILE__).'/trailer_2.flv',
	dirname(__FILE__).'/trailer_2_meta1.flv'
);
