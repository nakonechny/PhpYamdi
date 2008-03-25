<?php
/*
 * SabreAMF based on requires
 */
if (!defined('SABREAMF_ROOT')) define('SABREAMF_ROOT', '/home/allen/workspace2/lib');
set_include_path(get_include_path() . PATH_SEPARATOR . SABREAMF_ROOT);

/* Yamdi based on autoloads */
if (!defined('YAMDI_ROOT')) define('YAMDI_ROOT', dirname(__FILE__));

global $autoload_map;
$autoload_map = array(
	'Yamdi'		=> YAMDI_ROOT.'/',
	'SabreAMF'	=> SABREAMF_ROOT.'/'
);

function __autoload($class)
{
	global $autoload_map;
	
	if (false === ($p = strpos($class, '_')))
		$libraryName = $class;
	else
		$libraryName = substr($class, 0, $p);

	if (isset($autoload_map[$libraryName]))
		require_once $autoload_map[$libraryName] . str_replace('_', '/', $class) . '.php';
}
