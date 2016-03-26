<?php
/**
 * http webServer
 * usage:  setOptions(['root' => 'you document root dir', 'host' => 'host', 'port' => 'port']);  
 * 		 \Server\webHttpServer::init();
 *
 */
namespace Server;
class webHttpServer{
	protected static $_socket = null;
	const CRLF = "\r\n";
	public static $root = 'D:/wamp';
	public static $host = 'tcp://0.0.0.0';
	public static $port = 5566;

	public static function init(){
		self::startServer();
		self::accpetConnection();
	}

	public static function startServer(){
		self::$_socket = stream_socket_server(self::$host . ':' . self::$port, $errno, $errstr);
		if(! self::$_socket){
			echo $errstr . '(' . $errno . ')' . "\n";
		}
		stream_set_blocking(self::$_socket, 0); // No IO Block
	}

	/**
	 * set options like setOptions(['root' => '/usr/local/www']);
	 * @param [] $options
	 */
	public static function setOptions($options = array()){
		if(! $options){
			return false;
		}
		foreach($options as $option => $option_val){
			self::${$option} = $option_val;
		}
	}

	/**
	 * accept the browers or clients connection 
	 */
	public static function accpetConnection(){
		if(! self::$_socket){
			self::startServer();
		}	
		while(true){
			$accept_socket = stream_socket_accept(self::$_socket, -1);
			if(! $accept_socket){
				return false;
			}
			self::event('self::actionResponse', $accept_socket);
			self::close($accept_socket);
		}
	}

	/**
	 * event trigger
	 *
	 */
	public static function event($event_name, $params){
		call_user_func($event_name, $params);
	}

	/**
	 * action the server response
	 */
	public static function actionResponse($accept_socket){
		$file_content = self::parseResponse(self::readData($accept_socket));
		self::writeData($accept_socket, $file_content);
	}

	/**
	 * parse the response content
	 *
	 */
	public static function parseResponse($response){
		$query_arr = explode(' ', $response);			
		$query_filename = self::$root . $query_arr[1];
		if(file_exists($query_filename)){
			$fileinfo = pathinfo($query_filename);
			$extension = $fileinfo['extension'];
			if(! $extension){
				// load index.html
				if(file_exists($query_filename . 'index.html')){
					$file_content = file_get_contents($query_filename . 'index.html');
				}else{
					$file_content = 'This Is A Dir';
				}
				
			}else{
				if($extension === 'php'){
					ob_start();
					require $query_filename;
					$file_content = ob_get_clean();
				}else{
					$file_content = file_get_contents($query_filename);
				}
				
			}
		}else{
			$file_content = self::notFound();
		}
		return $file_content;
	}

	/**
	 * write data
	 */
	public static function writeData($accept_socket, $content){
		$header = 'HTTP/1.1 200 OK' . self::CRLF;
		$header .= 'Content-Type: text/html;charset=utf-8' . self::CRLF;
		$header .= 'Connection: keep-alive' . self::CRLF;
		$header .= 'Server: MyServer/1.0' . self::CRLF;
		$header .= 'Content-Length: ' . strlen($content) . self::CRLF . self::CRLF;
		fwrite($accept_socket, $header . $content);
	}

	/**
	 * read data
	 */
	public static function readData($accept_socket){
		$request = '';
		while(true){
			$result = fread($accept_socket, 65535);
			if($result === '' || $result === false){
				break;
			}
			$request .= $result;
		}
		return $request;
	}

	/**
	 * page not found
	 *
	 */
	private static function notFound($templete_file = ''){
		if($templete_file && file_exists($templete_file)){
			return file_get_contents($templete_file);
		}
		return 'Not Found';
	}

	/**
	 * close the server
	 */
	public static function close($accept_socket){
		if(! is_resource($accept_socket)){
			return false;
		}
		@fclose($accept_socket);
	}
}
 \Server\webHttpServer::init();
