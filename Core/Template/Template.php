<?php
namespace Template;

class Template{
	public static $ext_name = 'tpl';
	public static $template_dir = root_path.'Tpl/';
	public static $template_left = '{';
	public static $tmeplate_right = '}';
	private static $tags;

	public function __construct(){

	}

	public static function display($template_name = 'index'){
		$content = self::fetch($template_name);
		echo $content;
	}

	public static function fetch($template_name = 'index'){
		$template_file = self::$template_dir.$template_name.'.'.self::$ext_name;
		return self::getContent($template_file);
	}

	public static function getContent($file){
		$fp = fopen($file, 'r');
		if(! $fp){
			throw new Execption($file.' is not exists!');
		}
		$content = '';
		while(! feof($fp))
			$content .= fread($fp, filesize($file));
		$content = self::parseContent($content);
		return $content;
	}

	private static function parseContent($content){
		$content = self::parseTag($content);
	}

	private static function parseTag($content){
		$content = preg_match_all('/\{(.+?)\}/', $content, $match);
		foreach($match as $key => $rs){
			self::$tags = $rs;
		}
		//error_log(print_r($match, 1)."\n", 3, "D:/wamp/www/temp/d.log");
	}
}
?>