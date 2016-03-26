<?php
/**
 * weixin login 
 */
namespace Api\Weixin;

class WeixinLogin{
	public static $app_id = '';
	public static $app_secret = '';
	public static $redirect_uri = 'WeixinLoginCallback.php';
	public static $response_type = 'code';
	public static $scope = 'snsapi_login';
	public static $state = '';
	public static $url = 'https://open.weixin.qq.com/connect/qrconnect';

	public function __construct(){

	}

	/**
 	 * generate login url for user
	 */
	public static function genLoginUrl($state = ''){
		if($state){
			self::$state = $state;
		}
		return self::$url . '?' 
			. 'appid=' . self::$app_id 
			. '&redirect_uri=' . self::$redirect_uri
			. '&response_type=' . self::$response_type
			. '&scope=' . self::$scope
			. '&state=' . self::$state;
	}

	/**
 	 * send post or get request
	 */
	private static function _send($data, $type = 'post'){
		$ch = curl_init();
		if(strtoupper($type) === 'POST'){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$url = self::$url;
		}else{
			$url = self::$url . '?' . $data;
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	public static function getAccessToken(){

	}
}

