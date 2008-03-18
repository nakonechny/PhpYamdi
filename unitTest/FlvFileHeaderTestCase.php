<?php

require_once dirname(__FILE__).'/../Yamdi/Struct.php';
require_once dirname(__FILE__).'/../Yamdi/FlvFileHeader.php';

class FlvFileHeaderTestCase extends UnitTestCase
{
	public function testUnpack()
	{
		$header = new Yamdi_FlvFileHeader();
		$header->signature = 'FLV';
		$header->version = 1;
		$header->flags = 5;
		$header->headersize = 1000;
		$out = $header->pack();
		$header2 = new Yamdi_FlvFileHeader();
		$header2->unpack($out);
		$this->assertEqual($header, $header2);
	}
}
