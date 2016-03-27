<?php
/**
 * Excel class
 * Usage:   Excel::setFileName()
 * 			Excel::setHeaderExcel()
 *			Excel::setTitleList($title_data)
 *			Excel::setContentList($data)
 *			Excel::download()
 */
namespace Util\Excel;

class Excel{
	const T = "\t";
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

	/**
	 * set download filename
	 * @param string $filename
	 * @return void
	 */
	public static function setFileName($filename){
		self::$filename = $filename;
	}

	public static function setHeaderExcel(){
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: filename=' . self::$filename);
	}

	/**
	 * set excel title
	 * @param [] $data [index => val]
	 *
	 */
	public static function setTitleList($data){
		if(! is_array($data)){
			return false;
		}
		$html = '';
		foreach($data as $val){
			$html .= $val . self::T;
		}

		return self::$html = $html;
	}

	/**
	 * set excel list
	 * @param [] $data [index => [index => val]]
	 *
	 */
	public static function setContentList($data){
		if(! is_array($data)){
			return false;
		}
		if(self::$html){
			$html = self::$html . self::N;
		}else{
			$html = '';
		}
		foreach($data as $ret){
			foreach($ret as $val){
				$html .= $val . self::T;
			}
			$html .= self::N;
		}
		return self::$html = $html;
	}

}
?>