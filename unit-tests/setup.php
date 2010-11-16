<?php

require_once dirname(__FILE__).'/../autoload.php';
require_once 'PHPUnit/Framework.php';

spl_autoload_register('autoloadPhpYamdiUnitTests');

function autoloadPhpYamdiUnitTests($class) {
	require_once dirname(__FILE__) . '/' . str_replace('_', '/', $class) . '.php';
}