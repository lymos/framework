<?php
/**
 * Controller class
 */
namespace Controller;

use Template\Template as Tpl;
class Controller{

	public function __construct(){

	}

	public static function display($tpl_name){
		Tpl::display($tpl_name);
	}

	public static function fetch($tpl_name){
		return Tpl::fetch($tpl_name);
	}

	/**
	 * ajax return 
	 * @param mixed $data
	 * @param string $type
	 * @param boolean $json_option
	 * @return void
	 */
	public static function ajaxReturn($data, $type = '', $json_option = 0){
		switch(strtolower($type)){
			case 'json':
				header('Content-Type: application/json; charset=utf-8');
				exit(json_encode($data, $json_option));
			case 'xml':
				header('Content-Type: text/xml; charset=utf-8');
				exit($data);
			case 'jsonp':
				header('Content-Type: application/json; charset=utf-8');
				$callback = 'callback';
				exit($callback . '(' . json_encode($data, $json_option) . ')');
			case 'eval':
				header('Content-Type: text/html; charset=utf-8');
				exit($data);
			default:
				exit(1);
		}
	}
}
?>