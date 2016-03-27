<?php
//namespace AutoLoad;

function __autoload($class){
	$class_path = getAutoLoadConfig($class);
	require_once $class_path;
}

/*
class AutoLoad{

	/**
	 * autoload class function 
	 * @param string $class
	 *
	public static function splAutoLoad($class){
		$class_file = core_path.$class.'.php';
		if(file_exists($class_file)){
			return require $class_file;
		}
	}
}
*/
// register autoload function
// spl_autoload_register('\AutoLoad\AutoLoad::splAutoLoad');
?>