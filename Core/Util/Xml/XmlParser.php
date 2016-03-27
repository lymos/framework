<?php
/**
 * Xml Parser Extension
 */
namespace Util\Xml;

class XmlParser{
	public static $encoding = 'utf-8';
	public static $res;
	public static $option = [
					XML_OPTION_CASE_FOLDING => 1,
					XML_OPTION_SKIP_TAGSTART => 0,
					XML_OPTION_SKIP_WHITE => 0,
					XML_OPTION_TARGET_ENCODING => 'UTF-8'
						];

	public function __construct(){

	}

	public static function init(){
		self::$res = xml_parser_create(self::$encoding);
	}

	public static function setOption($option = []){
		if(! $option){
			$option = self::$option;
		}
		foreach($option as $name => $val)
			xml_parser_set_option(self::$res, $name, $val);
	}

	/**
	 * @param constant $name like XML_OPTION_CASE_FOLDING
	 */
	public static function getOption($name){
		return xml_parser_get_option(self::$res, $name);
	}

	/**
	 * xml to array 
	 * support xml highest level is 3 
	 * eg:<root><foo1><foo2></foo2></foo1></root>
	 */
	public static function xmlToArray($data){
		if(! $data){
			return false;
		}
		if(! is_resource(self::$res)){
			self::init();
		}
		if(is_file($data)){
			$data = file_get_contents($data);
		}
		xml_parse_into_struct(self::$res, $data, $ret, $index);
		self::xmlFree();
		$data = $level_data = $type = [];

		foreach($ret as $key => $rs){
			// attributes
			if(isset($rs['attributes'])){
				foreach($rs['attributes'] as $attr_id => $attr_val){
					$data['attributes'][$rs['tag']][$attr_id] = $attr_val;
				}
			}
			if($rs['type'] == 'open' || $rs['type'] == 'complete'){
				$level_data[$rs['level']][$rs['tag']] = $rs['value'];
			}
			if($rs['type'] != 'close'){
				$type[$rs['tag']] = $rs['type'];				
			}
		}
		foreach($level_data as $level => $rs_tags){
			foreach($rs_tags as $tag => $val){
				if($type[$tag] == 'open'){
					// push his children in
					$data['result'][$tag] = $level_data[$level + 1];
				}
			}
		}
		foreach($data['result'] as $root_tag => $rs){
			foreach($rs as $tag => $val){
				if($type[$tag] == 'open'){
					// push its children in
					$data['result'][$root_tag][$tag] = $data['result'][$tag];
					unset($data['result'][$tag]);
				}
			}
		}
		return $data;
	}

	public static function xmlFree(){
		return xml_parser_free(self::$res);
	}
}
//\Util\Xml\XmlParser::init();
$str = '<?xml version="1.0" encoding="utf-8" ?><root xnlms="http://google.com"><name id="uid" p="4">admin</name><password>123456</password><age><sex>8888</sex><aaa>999</aaa></age></root>';
//\Util\Xml\XmlParser::xmlToArray($str);
?>
