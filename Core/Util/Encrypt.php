<?php
/**
 * Encrypt hash md5
 * extension: hash and mcrypt
 */
class Encrypt{

	public static $mcrypt_mode = 'ecb';
	public static $mcrypt_algorithms = 'des';	// mcrypt_list_algorithms() can see
	public static $algorithms_list;	
	public static $mcrypt_mode_list;
	public static $iv_mode = MCRYPT_RAND;
	private static $td;
	private static $iv; 

	public function __construct(){

	}

	/**
	 * Encrypt A File With Hash
	 */
	public static function hashFile($file, $type = 'md5', $raw_output = false, $key = '', $hmac = false){
		if($hamc){
			return hash_hmac_file($type, $file, $key, $raw_output);
		}
		return hash_file($type, $file, $raw_output);
	}

	/**
	 * Encrypt A String With Hash
	 */
	public static function hashString($str, $type = 'md5', $raw_output = false, $key = '', $hamc = false){
		if($hmac){
			return hash_hmac($type, $str, $key, $raw_output);
		}
		return hash($type, $str, $raw_output);
	}

	/**
	 * All can use algorithms
	 */
	public static function getAlgorithmsList(){
		return self::$algorithms_list = mcrypt_list_algorithms();
	}

	public static function getMcryptMode(){
		return self::$mcrypt_mode_list = mcrypt_list_modes();
	}

	public static function setMcryptAlg($algorithm){
		self::$algorithm = $algorithm;
	}

	public static function setMcryptMode($mode){
		self::$mcrypt_mode = $mode;
	}

	public static function mcryptInit(){
		self::$td = mcrypt_module_open(self::$algorithm, '', self::$mcrypt_mode, '');
		$vi_size = mcrypt_enc_get_iv_size($td);
		self::$iv = mcrypt_create_iv($iv_size, self::$iv_mode);
	}

	/**
	 * encrypt
	 */
	public static function enCrypt($data, $key){
		if(! $data){
			return false;
		}
		if(! self::$td || ! self::$iv){
			self::mcryptInit();
		}
		mcrypt_generic_init(self::$td, $key, self::$iv);
		$encrypt_data = mcrypt_generic(self::$td, $key);
		mcrypt_generic_deinit(self::$td);
		mcrypt_module_close(self::$td);
		return $encrypt_data;
	}

	/**
	 * decrypt 
	 */
	public static function deCrypt($data, $key){
		if(! $data){
			return false;
		}
		if(! self::$td || ! self::$iv){
			self::mcryptInit();
		}
		mcrypt_generic_init(self::$td, $key, self::$iv);
		$encrypt_data = mdecrypt_generic(self::$td, $key);
		mcrypt_generic_deinit(self::$td);
		mcrypt_module_close(self::$td);
		return $encrypt_data;
	}

}
?>