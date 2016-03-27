<?php
/**
 * Web Page Spider get web page content
 */
namespace Util;

class Spiders{

	public function __construct(){

	}

	/**
	 * get web page cont
	 * @param string $url 
	 * @param array $options ['cookies' => string, 'useragent' => string, 'timeout' => integer, ...]
	 */
	public static function getCont($url, $options = []){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 	// return page not the output
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);	// forbidden ssl
		curl_setopt($ch, CURLOPT_URL, $url);
		if($options){
			foreach($options as $key => $val){
				switch($key){
					case 'cookies':
						$curl_options[CURLOPT_COOKIE] = $val;
						break;
					case 'useragent':
						$curl_options[CURLOPT_USERAGENT] = $val;
						break;
					case 'is_post':
						$curl_options[CURLOPT_POST] = $val;
						break;
					case 'post_data':
						$curl_options[CURLOPT_POSTFIELDS] = $val;
						break;
					case 'timeout':
						$curl_options[CURLOPT_TIMEOUT] = $val;
						break;
					case 'http_header':
						$curl_options[CURLOPT_HTTPHEADER] = $val;
				}
			}	
			curl_setopt_array($ch, $curl_options);
		}
		$cont = curl_exec($ch);
		$error = curl_error($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		$data = compact('cont', 'error', 'errno');
		return $data;
	}

	/**
	 * once get multi url content
	 * @param array $urls [0 => url1, 1 => url2]
	 * @param array $options ['cookies' => string, 'useragent' => string, 'timeout' => integer, ...]
	 */
	public static function multiGetCont($urls, $options = []){
		if(! is_array($urls)){
			$urls = [$urls];
		}
		$mh = curl_multi_init();
		if($options){
			extract($options);
		}
		foreach($urls as $url){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);	// return all url page content
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // forbidden ssl verify
			if(isset($cookies))
				curl_setopt($ch, CURLOPT_COOKIE, $cookies);
			if(isset($useragent))
				curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
			if(isset($cookiesession))
				curl_setopt($ch, CURLOPT_COOKIESESSION, $cookiesession);
			if(isset($is_post))
				curl_setopt($ch, CURLOPT_POST, $is_post);
			if(isset($timeout))
				curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			if(isset($cookiefile))
				curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
			if(isset($post_data))
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			if(isset($http_header))
				curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);

			curl_multi_add_handle($mh, $ch);
		}
		// curl_multi_setopt($mh, option, value); // for php version >= 5.5.0
		$running = null;
		do{
			curl_multi_exec($mh, $running);
		}while($running > 0);
		// get message
		$curl_info = $cont = $error = $errno = $data = [];
		while($info = curl_multi_info_read($mh)){
			$handle = $info['handle'];
			$curl_info = curl_getinfo($handle);
			$url = $curl_info['url'];
			$result_info[] = $curl_info;
			$cont[$url][] = curl_multi_getcontent($handle);
			$error[$url][] = curl_error($handle);
			$errno[$url][] = curl_errno($handle);
		}
		curl_multi_close($mh);
		$data = compact('result_info', 'cont', 'error', 'errno');
		return $data;
	}

	/**
	 * get content a link
	 * @param string $content
	 * @return $array
	 */
	public static function getALink($content){
		//preg_match_all("/<a[^<>]+href *\= *[\"']?(http\:\/\/[^ '\"]+)/i", $body, $body_links, PREG_SET_ORDER);
		$ret = preg_match_all("/<a[^>]+href *\= *[\"]?([^ '\"]+)/i", $content, $matchs);
		$link = [];
		if($ret){
			$link = $matchs[1];
		}
		return $link;
	}

	/**
	 * get content img link
	 * @param string $content
	 * @return $array
	 */
	public static function getImgLink($content){
		$ret = preg_match_all("/<img[^>]+src *\= *[\"]?([^ '\"]+)/i", $content, $matchs);
		$img_link = [];
		print_r($matchs);
		if($ret){
			$img_link = $matchs[1];
		}
		return $img_link;
	}
}
?>
