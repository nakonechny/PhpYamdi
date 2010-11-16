<?php

PhpYamdiLoader::setLibraryDefaultPath('Yamdi', dirname(__FILE__) . '/');
PhpYamdiLoader::setLibraryDefaultPath('SabreAMF', dirname(__FILE__) . '/ext/SabreAMF/');
// SabreAMF is loading itself by require_once() instructions, need to set up include_path
set_include_path(get_include_path() . PATH_SEPARATOR . PhpYamdiLoader::getLibraryPath('SabreAMF'));

spl_autoload_register(array('PhpYamdiLoader','autoload'));

class PhpYamdiLoader
{
	static public $libraryMap = array();
	
	static public function setLibraryDefaultPath($class_prefix, $path)
	{
		if (! array_key_exists($class_prefix, self::$libraryMap)) {
			self::setLibraryPath($class_prefix, $path);
		}
	}
	
	static public function setLibraryPath($class_prefix, $path)
	{
			self::$libraryMap[$class_prefix] = $path;
	}
	
	static public function getLibraryPath($class_prefix)
	{
		return array_key_exists($class_prefix, self::$libraryMap) ? self::$libraryMap[$class_prefix] : null; 
	}
	
	static public function autoload($class)
	{
		global $autoload_map;
		
		if (false === ($p = strpos($class, '_'))) {
			$libraryName = $class;
		} else {
			$libraryName = substr($class, 0, $p);
		}
	
		$path = self::getLibraryPath($libraryName);
		if ($path) {
			require_once $path . str_replace('_', '/', $class) . '.php';
		}
	}	
}