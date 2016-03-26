<?php
/**
 * mail pop3 protorl receive mail
 * usage: \Net\Pop3::getInstance(); $mail_content = \Net\Pop3::getOneMail(4);
 *	or $obj = new \Net\Pop3;	$mail_content = $obj::getOneMail(4);
 *
 */
namespace Net;

class Pop3{

	const CRLF = "\r\n";
	public static $conn;
	public static $host = 'pop3.163.com';
	public static $port = 110;
	public static $user = '';
	public static $pwd = '';
	public static $timeout = 30;
	public static $current_time;

	public function __construct(){
		self::getInstance();
	}

	public static function getInstance(){
		if(! self::$conn){
			self::open();
		}
		self::autoLogin();
		self::$current_time = date('Y-m-d H:i:s');
	}

	public static function open(){
		self::$conn = stream_socket_client(self::$host.':'.self::$port, $errno, $errstr, self::$timeout);
		$response = self::getResponse();
		if(preg_match('/^\-ERR/', $response)){
			self::showError(self::$current_time.'  Connect Failed. Response: '.$response);
		}
		self::log(self::$current_time.'  Connect Failed Response: '.$response);
	}

	private static function sendCommand($action, $command){
		self::send($command.self::CRLF);
		$response = self::getResponse($command);
		if(preg_match('/^\-ERR/', $response)){
			self::showError(self::$current_time.'  '.$action.' failed. Response: '.$response);
		}
		self::log(self::$current_time.'  '.$action.'. Response: '.$response);
		return $response;
	}

	private static function send($data){
		if(! is_resource(self::$conn)){
			return false;
		}
		return fwrite(self::$conn, $data);
	}

	private static function getResponse($command = ''){
		if(! is_resource(self::$conn)){
			return false;
		}
		$response = '';
		if(preg_match('/^LIST|RETR.*/', $command)){
			while(true){
				$str = fgets(self::$conn, 128);
				if(preg_match('/^\./', $str) || ! $str){
					break;
				}
				$response .= $str;
			}
		}else{
			$response .= fgets(self::$conn, 8192); 	// read one line first
		}
	
		/*
		while(is_resource(self::$conn) && ! feof(self::$conn)){
			$str = fgets(self::$conn, 8192);
			$response .= $str;
			// the 4th char is space  break
			if(isset($str[3]) && $str[4] == ' '){
				break;
			}
			$meta_info = stream_get_meta_data(self::$conn); 
		}
		*/
		return $response;
	}

	public function __get($name){
		return self::$name;
	}

	public function __set($name, $value){
		return self::$name = $value;
	}

	public function __call($func, $params){
		throw new Exception('No This Function: '.$func);
	}

	private static function autoLogin(){
		self::sendCommand('Enter User', 'USER '.self::$user);
		self::sendCommand('Enter Password', 'PASS '.self::$pwd);
	}

	/**
	* get one mail content
	*
	*/
	public static function getOneMail($num = 1){
		$data = self::sendCommand('Get One Mail', 'RETR '.$num);
		return $data;
	}

	public static function deleteOneMail($num){
		$data = self::sendCommand('Delete One Mail', 'DELE '.$num);
		return $data;
	}

	/**
	* get all mail list [number => byte]
	*/
	public static function getMailList($num = 1){
		$data = self::sendCommand('Get Mail List', 'LIST');
		return $data;
	}

	/**
	* get mail number
	*
	*/
	public static function getMailNum(){
		$string = self::sendCommand('Get Mail Num', 'STAT');
		$data = explode(' ', $string);
		if($data){
			return ['mail_num' => $data[1], 'mail_byte' => $data[2]];
		}else{
			return [];
		}
	}

	/**
	* end of connect
	*/
	public static function disConnect(){
		self::sendCommand('Exit Server', 'QUIT');
		return fclose(self::$conn);
	}

	private static function showError($errmsg){

	}

	private static function log($msg){

	}
}
?>