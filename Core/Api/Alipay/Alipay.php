<?php
/**
 *
 */
namespace Api\Alipay;

class Alipay{
	public static $gateway_url = 'https://openapi.alipay.com/gateway.do';
	public static $api_type = 'openapi';	// or mapi => https://mapi.alipay.com/gateway.do
	public static $app_id = '2016032001227754';
	pubiic static $charset = 'utf-8';
	public static $sign_type = 'rsa';	// or md5
	public static $api_version = '1.0';
	public static $private_key_file = 'rsa_private_key.pem';
	public static $public_key_file = 'rsa_public_key.pem';


	public function __construct(){
	
	}

	/**
	 * bind public params
	 */
	private static function _bindPublicParams(){
		if(! self::$app_id){
			self::$app_id = ALIPAY_APPID;
		}
		return [
			'app_id' => self::$app_id,
			'charset' => self::$charset,
			'sign_type' => self::$sign_type,
			'timestamp' => date('Y-m-d H:i:s'),
			'version' => self::$api_version
			];
	}

	private static function _signature($params = []){
	
		unset($params['sign']);
		if(self::$api_type === 'openapi'){
			unset($params['sign_type']);
		}
		ksort($params);	// sort by key
		$sign_str = implode('&', $params);
		return self::_encode(self::_sign($sign_str), 'base64');
	}

	private static function _sign($data){
		$private_key_id = openssl_get_privatekey(self::$private_key_file);
		openssl_sign($data, $signature, $private_key_id);
		openssl_free_key($private_key_id);
		return $signature;
	}

	private static function _verify($data, $signature){
		$public_key_id = openssl_get_publickey(self::$public_key_file);
		$status = openssl_verify($data, $signature, $public_key_id);
		openssl_free_key($public_key_id);
		return $status;
	}

	private static function _encode($data, $code = 'base64'){
		$code = strtoupper($code);
		switch($code){
			case 'BASE64':
				$data = base64_encode($data);
			break;
		}
		return $data;
	}

	private static function _encrypt(){

	}

	private static function _decrypt(){

	}

	/**
 	 * alipay trade create
	 * $biz_content_arr = [
	 *		'out_trade_no' => order number,
	 *		'total_amount' => price,
	 *		'royalty_detail_infos' => []
	 *		...
	 *		]
	 * link https://doc.open.alipay.com/doc2/apiDetail.htm?apiId=1046&docType=4 
	 */
	public static function alipayTradeCreate($biz_content_arr = []){
		$params = self::_bindPublicParams();
		$params['biz_content'] = json_encode($biz_content_arr);
		$params['method'] = 'alipay.trade.create';
		$signature = self::_signature($params);
		$params['sign'] = $signature;
		return self::_send($params);	
	}

	/**
	 * send post or get request
	 *
	 */
	private static function _send($data, $type = 'post'){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		if(strtoupper($type) === 'POST'){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$url = self::$gateway_url;
		}else{
			$url = self::$gateway_url . '?' . $data;
		}	
		curl_setopt($ch, CURLOPT_URL, $url);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
}

