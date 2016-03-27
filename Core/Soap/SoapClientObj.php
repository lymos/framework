<?php
/**
 * Soap Client Class Usage:
 * $soap = \Soap\SoapClientObj::initNoWsdl();
 * echo $soap->test(' hello');  the test() is soap server's function and it must be existed in server
 * 
 */
namespace Soap;

class SoapClientObj{
	public static $wsdl_file = 'soap.wsdl';
	public static $localtion = 'http://localhost/Soap/SoapServerObj.php';
	public static $uri = 'myuri';
	private static $_soap = null;


	public function __construct(){

	}

	/**
 	 * @return soap object
	 */
	public static function init(){
		return self::_startClient();
	}

	public static function initNoWsdl(){
		return self::_startClientNoWsdl();

	}

	private static function _startClient(){
		return self::$_soap = new \SoapClient(self::$wsdl_file);
	}

	private static function _startClientNoWsdl(){
		return self::$_soap = new \SoapClient(null, [
					'location' => self::$localtion,
					'uri' => self::$uri,
					]);
	}
	
	public static function getInstance(){
		if(! self::$instance){
			return self::$instance = new self();
		}
		return self::$instance;
	}

}
/*
$soap = \Soap\SoapClientObj::initNoWsdl();
echo $soap->test(' hello');
*/

