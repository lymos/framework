<?php
/** 
 * office word class
 * '<html xmlns:o="urn:schemas-microsoft-com:office:office"
 * xmlns:w="urn:schemas-microsoft-com:office:word"
 * xmlns="http://www.w3.org/TR/REC-html40">
 * Usage:
 * 		\Util\Word\Word::saveWord('<html><h2>Hello</h2></html>', 'word.doc');
 */
namespace Util\Word;

class Word {

	public function __construct(){

	}

	/**
	 * save content to office word
	 * @param string $html
	 * @param string $file
	 * @return mixed
	 */
	public static function saveWord($html, $file){
		self::_start();
		echo $html;
		$data = self::_getContent();
		self::_end();
		return self::_writeFile($data, $file);
	}

	private static function _start(){
		ob_start();
	}

	/**
	 * get the buffer content
	 */
	private static  function _getContent(){
		return ob_get_contents();
	}

	private static function _end(){
		ob_end_clean();
		ob_flush();
		flush();
	}

	/**
	 * write a file
	 */
	private static function _writeFile($data, $file){
		$fp = fopen($file, 'wb');
		$ret = fwrite($fp, $data);
		fclose($fp);
		return $ret;
	}

}

// \Util\Word\Word::saveWord('<html><h2>Hello</h2></html>', 'word.doc');