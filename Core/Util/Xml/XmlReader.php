<?php
/**
 * Xml class
 */
namespace Util\Xml;

class Xml{

	public function __construct(){

	}

	function xmlReaderParse($data){
		$reader = new XMLReader;
		$reader->xml($data);
		//$reader->open($data;
		$data = [];
		while($reader->read()){
			$node_type = $reader->nodeType;
			echoLog($reader->depth);
			if($reader->hasAttributes){
		
			}
			if($node_type == XMLReader::ELEMENT){	// element tag start
				//echoLog($reader->value);
			}
			if($reader->hasValue){
				//echoLog($reader->localName);
			}


			if($node_type == XMLReader::ELEMENT){
				$tag_name = $reader->name;
			}
			if($node_type == XMLReader::TEXT){
				$data[$tag_name] = $reader->value;
			}
			}
			echoLog($data);
			while($str = $reader->readString()){
			echoLog($reader->name);
			}
		}
	}

	function echoLog($msg){
		print_r($msg);
		echo "\n";
	}
}
$str = '<?xml version="1.0" encoding="utf-8" ?><root><name id="uid" p="4">admin</name><password>123456</password><age></age></root>';
$res = xmlReaderParse($str);
?>