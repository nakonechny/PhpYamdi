<?php
/**
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

class Yamdi_FlvMetadataReader extends Yamdi_FlvFile
{
	public function run($source)
	{
		$this->read($source);
	}

	public function exportMetadata()
	{
		return $this->metadata->export();
	}
}