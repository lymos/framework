<?php
/**
 * verify foo type
 * Usage: \Util\VerifyType::isEmail($email);
 */
namespace Util;

class VerifyType{

	public function __construct(){

	}

	public static function isEmail($email, $regexp = false){
		if(! $email){
			return false;
		}
		// regular expression
		if($regexp){
			return false;
		}
		return filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false;
	}

	public static function isMobile($mobile){
		if(! $mobile){
			return false;
		}
	}

	public static function isUrl($url, $regexp = false){
		if(! $url){
			return false;
		}
		if($regexp){
			return false;
		}
		return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
	}

	public static function isIp($ip, $regexp = false){
		if(! $ip){
			return false;
		}
		if($regexp){

		}
		return filter_var($ip, FILTER_VALIDATE_IP) ? true : false;
	}

	/**
	 * verify mac address
	 * @param string $mac
	 * @param boolean $regexp use regexp or not
	 * @return boolean
	 */
	public static function isMac($mac, $regexp = false){
		if(! $mac){
			return false;
		}
		if(! $regexp && version_compare(PHP_VERSION, '5.5.0', '>')){
			return filter_var($mac, FILTER_VALIDATE_MAC) ? true : false;
		}
		$reg = '/^([0-9A-Fa-f]{2}(:|-){1}){5}[0-9A-Fa-f]{2}$/';
		if(preg_match($reg, $mac, $matchs)){
			if(preg_match('/:+.*-+/', $matchs[0]) || preg_match('/-+.*:+/', $matchs[0])){
				return false;
			}
			return true;
		}else{
			return false;
		}
	}
}
?>