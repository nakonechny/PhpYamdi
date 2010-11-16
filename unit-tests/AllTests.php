#!/usr/local/bin/phpunit --colors
<?php

include_once dirname(__FILE__).'/setup.php';

class AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Project');

        $suite->addTestSuite('StructTest');
        $suite->addTestSuite('OutputStreamTest');
        $suite->addTestSuite('InputStreamTest');
        $suite->addTestSuite('FlvTagSizeTest');
        $suite->addTestSuite('FlvFileHeaderTest');

        return $suite;
    }
}
