<?php
/**
 * init
 */
namespace Core;

class Init{
	public function __construct(){

	}
	
	public static function run($config){
		self::genAppPath($config);
		self::startRoute();
	}

	private static function genAppPath($config){
		$app_path = $config['app_path'];
		$app_path_status = true;
		if(! file_exists($app_path)){
			$app_path_status = mkdir($app_path, 0777, true);
		}
		if($app_path_status){
			$default_module_path = $app_path . '/' . $config['app_default_module'];
			if(! file_exists($default_module_path)){
				mkdir($default_module_path, 0777);
			}
			if(! file_exists($model_path = $default_module_path . '/' . $config['app_model_dirname'])){
				mkdir($model_path, 0777, true);
			}
			if(! file_exists($controller_path = $default_module_path . '/' . $config['app_controller_dirname'])){
				mkdir($controller_path, 0777, true);
			}
			if(! file_exists($view_path = $default_module_path . '/' . $config['app_view_dirname'])){
				mkdir($view_path, 0777, true);
			}
			if(! file_exists($web_path = $app_path . '/' . $config['app_web_dirname'])){
				mkdir($web_path, 0777);
			}
			if(! file_exists($data_path = $app_path . '/' . $config['app_data_dirname'])){
				mkdir($data_path, 0777);
			}
		}
	}

	/**
	 * start url route
	 */
	private static function startRoute(){
		$uri = $_SERVER['REQUEST_URI'];	
		// if no uri query 
		if(preg_match('/^.*\/index.php$/', $uri)){
			$module_name = 'Home';
			$controller_name = 'Index';
			$action_name = 'index';
		}else{
			$query = $_SERVER['QUERY_STRING'];
			$query_arr = explode('=', $query);
			$args_arr = explode('/', $query_arr[1]);
			$module_name = ucfirst($args_arr[0]);
			$controller_name = ucfirst($args_arr[1]);
			$action_name = isset($args_arr[2]) ? $args_arr[2] : 'index';
		}
		$class = '\\' . $module_name . '\\Controllers\\' . $controller_name . 'Controller';
		$action = $action_name . 'Action';
		$class::$action();
	}
}
	
