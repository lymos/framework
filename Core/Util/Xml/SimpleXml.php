<?php
/**
 * simple xml class
 */
namespace Util\Xml;

class SimpleXml{

	const CRLF = "\r\n";
	const TAB = "\t";

	public function __construct(){

	}

	/**
	 * Parse Xml To Array
	 * @return array 
	 */
	public static function parseXmlToArray($data){
		if(is_file($data)){
			$xml_obj = simplexml_load_file($data);
		}else{
			$xml_obj = simplexml_load_string($data);
		}
		return self::objectToArray($xml_obj);
	}

	/**
	 * Object To Array
	 */
	private static function objectToArray($obj){
		$data = [];
		$arr = get_object_vars($obj);
		foreach($arr as $key => $rs){
			if(is_object($rs)){
				$data[$key] = self::objectToArray($rs);
			}else{
				if(is_array($rs)){
					foreach($rs as $sup_key => $val){
						if(is_object($val)){
							$data[$key][$sup_key] = self::objectToArray($val);
						}else{
							$data[$key][$sup_key] = $val;
						}
					}
				}else{
					$data[$key] = $rs;
				}
			}
		}
		return $data;
	}

	/**
	 * Set Array To XML
	 */
	public static function setArrayToXml($data, $root = '', $file = ''){
		if(! $data || ! is_array($data)){
			return false;
		}
		if(! $root){
			$root = 'root';
		}
		//$xml = '<?xml version="1.0" encoding="utf-8">' . "\n";
		$xml = '<' . $root . '>' . self::CRLF;
		foreach($data as $key => $rs){
			$xml .= self::TAB . '<' . $key . '>';
			if(is_array($rs)){
				$xml .= self::CRLF;
				$xml .= self::foreachArr($rs);
				$xml .= self::TAB . '</' . $key . '>' . self::CRLF;
			}else{
				$xml .= $rs . '</' . $key . '>' . self::CRLF;
			}
		}
		$xml .= '</' . $root . '>' . self::CRLF;
		$xml_obj = new \SimpleXMLElement($xml);
		if($file){
			return $xml_obj->asXML($file);
		}
		return $xml_obj->asXML();
	}

	/** 
	 * foreach all array until not is array
	 */
	private static function foreachArr($arr){
		if(! is_array($arr)){
			return false;
		}
		$xml = '';
		foreach($arr as $key => $rs){
			if(is_array($rs)){
				$xml .= self::foreachArr($rs);
			}else{
				$xml .= self::TAB . self::TAB . '<' . $key . '>';
				$xml .= $rs . '</' . $key . '>' . self::CRLF;
			}
		}
		return $xml;
	}
}

// $data = \Util\Xml\simpleXml::parseXmlToArray(str_replace('ns1:', '', $xml2)); // eg str replace some prefix

?>