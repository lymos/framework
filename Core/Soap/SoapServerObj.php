<?php
/**
 * Soap Server class usage:
 * \Soap\SoapServerObj::initNoWsdl();
 * \Soap\SoapServerObj::setClass('your class');
 * \Soap\SoapServerObj::handle();
 */
namespace Soap;

// some actions example you can use your class also by Soap\SoapServerObj::setClass(your class)
use \Soap\SoapAction;
require_once 'SoapAction.php';

class SoapServerObj{

	public static $instance = null;
	private static $_server;
	public static $wsdl_file = 'soap.wsdl';
	public static $uri = 'myuri';

	
	public function __construct(){

	}

	public static function init(){
		self::_startServer();		
	}

	public static function initNoWsdl(){
		self::_startServerNoWsdl();
		
	}

	private static function _startServer(){
		self::$_server = new \SoapServer(self::$wsdl_file);	
	}

	public static function handle(){
		self::$_server->handle();
	}
	
	/**
 	 * get instance object
	 *
	 */
	public static function getInstance(){
		if(! self::$instance){
			return self::$instance =  new self();
		}
		return self::$instance;
	}

	private static function _startServerNoWsdl(){
		self::$_server = new \SoapServer(null, array('uri' => self::$uri));
	}

	/**
	 * add class to server first you should require/include the class
	 * @param string $class
	 * @return boolean
	 */
	public static function setClass($class){
		if(! $class){
			return false;
		}
		self::$_server->setClass($class);
		return true;
	}

	/**
 	 * add function to server 
	 * @param string|[] $functions myFun or [myFun1, myFun2]
	 *
	 */
	public static function addFunction($functions){
		if(! $functions){
			return false;
		}
		self::$_server->addFunction($functions);
		return true;
	}
}
\Soap\SoapServerObj::initNoWsdl();
\Soap\SoapServerObj::setClass('\Soap\SoapAction');
\Soap\SoapServerObj::handle();


