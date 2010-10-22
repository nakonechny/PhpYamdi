<?php
/**
 * @author Alexey Nakonechny (nakonechny@gmail.com)
 * @package PhpYamdi (http://github.com/nakonechny/PhpYamdi)
 */

class Yamdi_FlvFile
{
	static $version = '0.2';
	
	/**
	 * @var string
	 */
	protected $sourceFilename;
	/**
	 * @var int
	 */
	protected $sourceMediaTagsStartPosition;
	
	/*
	 * File parts 
	 */
	
	/**
	 * @var Yamdi_FlvFileHeader
	 */
	protected $header;

	/**
	 * @var Yamdi_FlvTagSize
	 */
	protected $zeroTagSize;

	/**
	 * @var Yamdi_FlvMetadataBody
	 */
	protected $metadata;

	/**
	 * @var Yamdi_FlvTag
	 */
	protected $metadataTag;
	
	/**
	 * @var Yamdi_FlvTagSize
	 */
	protected $metadataTagSize;

	/**
	 * @var int
	 */
	protected $metadataTagBodyLenghth;
	
	public function __construct()
	{
		$this->header		= new Yamdi_FlvFileHeader();
		$this->zeroTagSize	= new Yamdi_FlvTagSize();
		$this->metadata		= new Yamdi_FlvMetadataBody();
		$this->metadataTag	= new Yamdi_FlvTag();
		$this->metadataTagSize = new Yamdi_FlvTagSize();
	}
		
	/**
	 * @param string $filename
	 * @return Yamdi_InputStream
	 */
	protected function read($filename)
	{
		$this->sourceFilename = $filename;
		
		/*
		 * File to read 
		 */
		$stream = new Yamdi_InputStream($this->sourceFilename);

		/*
		 * Constant header
		 */
		$this->header->read($stream);
		if (!$this->header->isValid()) {
			throw new Exception('File '.$this->sourceFilename.' is not of flv format');
		}
		
		/*
		 * Constant size of zero tag
		 */
		$this->zeroTagSize->read($stream);
		// $this->zeroTagSize->size should be zero
		
		/*
		 * First tag (meta)
		 */
		$this->metadataTag->read($stream);
		if (!$this->metadataTag->isMeta()) {
			throw new Exception('No metadata tag found if file '.$this->sourceFilename);
		}
		$tagBodyString = $this->metadataTag->readTagBody($stream);
		$this->metadataTagBodyLenghth = strlen($tagBodyString);
		$this->metadata->read($tagBodyString);
		
		/*
		 * Previous tag size
		 */
		$this->metadataTagSize->read($stream);

		$this->sourceMediaTagsStartPosition = $stream->getPosition();
		
		return $stream;
	}
	
	protected function write($filename)
	{
		/*
		 * File to write
		 */
		$stream = new Yamdi_OutputStream($filename);
		
		if (! $this->header->isValid()) {
			throw new Exception('Must read an flv file before writing it');
		}
		
		/*
		 * Writing preamble tags
		 */
		
		$this->header->write($stream);
		$this->zeroTagSize->write($stream);
		$this->writeMetaBlock($stream);
		$this->writeMediaBlocks($stream);
		
		$stream->close();
	}
	
	/**
	 * Writing media tags (passing through with no changes)
	 * 
	 * @param Yamdi_OutputStream $stream
	 */
	protected function writeMediaBlocks($stream)
	{
		$stream->passthrough(new Yamdi_InputStream($this->sourceFilename), $this->sourceMediaTagsStartPosition);
	}
	
	protected function writeMetaBlock($stream)
	{
		$this->shiftMetaSize();
		
		$this->metadataTag->write($stream);
		$stream->write($this->metadata->write());
		$this->metadataTagSize->write($stream);
	}
	
	protected function shiftMetaSize()
	{
		$old_size = $this->metadataTagBodyLenghth;
		$new_size = $this->metadata->getSize();
		
		$shift = $new_size - $old_size;
		
		$this->metadataTagSize->size += $shift;
		$this->metadataTag->setDataSize($new_size);
		
		return $shift;
	}
}
