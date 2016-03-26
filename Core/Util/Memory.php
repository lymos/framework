<?php
/**
 * About Memory
 */
namespace Util;

class Memory{

	public function __construct(){

	}

	/**
	 * Get Memory used
	 * @return string like 8MB
	 */
	public static function getMemoryUsed($digits = 2){
		return self::byteConvert(memory_get_usage(), $digits);
	}

	/**
	 * Byte To Convert
	 * @param int $byte
	 * @param int $digits decimal digits
	 * @return string 
	 */
	public static function byteConvert($byte, $digits = 2){
		$grade = ['', 'K', 'M', 'G', 'T', 'P'];
		$base = 1024;
		$i = floor(log($byte, $base));	// grade
		$n = count($grade);
		if($i >= $n){
			$i = $n - 1;	// last use 'P' grade
		}
		return round(($byte / pow($base, $i)), $digits) . $grade[$i] . 'B';
	}

}
?>