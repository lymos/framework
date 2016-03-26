<?php
/**
 * soap action class you can use your class myself 
 * by \Soap\SoapServerObj::setClass(your class name)
 */
namespace Soap;

class SoapAction{
	public static function test($params){
		return 'mytest' . $params;
	}
}

