<?

/*
 * Setting up path to library containing SabreAMF
 * @see http://code.google.com/p/sabreamf/
 * @see http://osflash.org/sabreamf
 */
require_once dirname(__FILE__).'/autoload.php';

/*
 * Usage example
 */
$flv = new Yamdi_FlvMetadataInjector();
$flv->run(
	dirname(__FILE__).'/../PhpYamdi_data/trailer_2.flv',
	dirname(__FILE__).'/../PhpYamdi_data/trailer_2_meta.flv'
);

$thumb = new Yamdi_FlvThumbnailMaker();
$thumb->run(
	dirname(__FILE__).'/../PhpYamdi_data/trailer_2.flv',
	dirname(__FILE__).'/../PhpYamdi_data/trailer_2_thumb.flv'
);
