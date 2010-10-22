<?php
/**
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

/**
 * Class of a PreviousTagSize block of FLV file
 * 
 * @see http://osflash.org/flv#flv_format
 */
class Yamdi_FlvTagSize extends Yamdi_Struct
{
	protected $dataTypes = array(
		'size' => 'N' // Total size of previous tag, or 0 for first tag
	);
}
