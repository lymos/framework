<?php
/**
 * common function
 */
function getConfig($name = ''){
	$config = require_once 'Config/Config.php';
	if(! $name){
		return $config;
	}
}


function getAutoLoadConfig($name = ''){
	static $config_class;
	if(! $config_class){
		$config_class = require_once 'Config/AutoLoadClass.php';
	}
	if(! $name){
		return $config_class;
	}else{

		return $config_class[$name];
	}
}

/**
* Get A Model
* @param string $name FileName or ModelName
*/
function model($name){
	$model_file = app_path . 'Model/' . $name . '.php';
	if(file_exists($model_file)){
		return new $name();
	}else{
		// require_once root_path . 'Core/Model/Model.php';
		return new Model();
	}
}
?>
