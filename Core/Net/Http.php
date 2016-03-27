<?php
namespace Net;

class Http{

	private static $timeout = 30;
	private static $port = 80;
	public function __construct(){

	}

	public static function curlSend($url, $type = 'get', $data = ''){
		if(! extension_loaded('curl')){
			throw new Execption('curl extension is not loaded');
			return false;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeout);
		curl_setopt($ch, CURLOPT_PORT, self::$port);
		if(strtolower($type) == 'post'){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_exec($ch);
		if(curl_errno($ch)){
			return 'has an error: '.curl_error($ch);
		}
		$response = curl_getinfo($ch);
		curl_close($ch);
		return $response;
	}

	public static function fsockSend($host, $port = 80, $data = ''){
		if(! extension_loaded('sockets')){
			// throw new Exception('socket extension is not loaded.');
			return false;
		}
		$fp = fsockopen($host, $port, $errno, $errstr, self::$timeout);
		if(! $fp){
			//throw new Execption('connect host '.$host.' failed. errno: '.$errno.'. error string: '.$errstr);
			return false;
		}
		$data && fwrite($fp, $data);
		$response = '';
		while(! feof($fp)){
			$response .= fgets($fp, 8192);
		}
		fclose($fp);
		return $response;
	}

	public static function socketSend($host, $port = 80, $data = ''){
		if(! extension_loaded('sockets')){
			// throw new Exception('socket extension is not loaded.');
			return false;
		}
		$fp = stream_socket_client($host.':'.$port, $errno, $errstr, self::$timeout);
		if(! $fp){
			//throw new Execption('connect host '.$host.' failed. errno: '.$errno.'. error string: '.$errstr);
			return false;
		}
		if($data){
			fwrite($fp, $data);
		}
		$response = '';
		while(! feof($fp)){
			$response .= fgets($fp, 8192);
		}
		return $response;
	}
}
?>