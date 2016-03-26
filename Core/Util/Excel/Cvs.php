<?php
/**
 * Cvs class
 * Usage:
 *			Cvs::setFileName($name)
 *          Cvs::setCvsHeader()
 *			Cvs::setTitleList($data)
 * 			Cvs::setContentList($data)
 *			Cvs::download()
 */
namespace Util\Excel;

class Cvs{
	const N = "\n";
	private static $html = '';
	private static $filename = '';

	public function __construct(){

	}

	public static function download($is_end = true){
		if($is_end){
			exit(self::$html);
		}else{
			echo self::$html;
		}
	}

	public static function setFileName($filename){
		self::$filename = $filename;
	}

	public static function setCvsHeader(){
		ob_end_clean();
		header('Content-Type: text/cvs');
		header('Content-Disposition: filename=' . self::$filename);
	}

	/**
	 * set cvs title list
	 * @param [] $data [index => val]
	 */
	public static function setCvsTitleList($data){
		if(! is_array($data)){
			return false;
		}
		self::$html = implode(',', $data);
	}

	/**
	 * set cvs content list
	 * @param [] $data [index => [index => val]]
	 */
	public static function setCvsContentList($data){
		if(! is_array($data)){
			return false;
		}
		foreach($data as $ret){
			self::$html .= implode(',', $ret) . self::N;
		}
	}
}