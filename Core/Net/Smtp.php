<?php
/**
 * smtp protocol class
 */
namespace Net;

class Smtp{

	const CRLF = "\r\n";
	public static $conn;
	public static $timeout = 30;
	public static $user = 'lymoslymos@163.com';
	public static $password = '';
	public static $from;
	public static $to;
	public static $cc;
	public static $bcc;
	public static $attachment;
	public static $charset = 'utf8';
	public static $header;
	public static $debug = false;

	public function __construct(){

	}

	public static function open($host, $port, $timeout){
		self::$conn = fsockopen($host, $port, $errno, $errstr, $timeout);
		$response = self::getResponse();
		if(! self::$conn){
			$err_msg = 'Connect the host failed. errno: '.$errno.'. errstr: '.$errstr.'. Response: '.$response;
			self::log($err_msg);
			self::showError($err_msg);
		}
		self::init();
	}

	private static function init(){
		self::helo('say something');
		self::authLogin();
		self::mailForm();
		self::rcptTo();
		self::startData();
	}

	private static function sendCommand($action, $command, $code){
		self::send($command.self::CRLF);
		$response = self::getResponse();
		self::log($response);
		if(! strpos($response, $code)){
			return self::showError($action.' failed. The response is '.$response);
		}
		// send mail successful
		if($code == 250 && $command == '.'){
			return true;
		}
	}

	private static function send($string){
		if(! is_resource(self::$conn)){
			return false;
		}
		return fwrite(self::$conn, $string);
	}

	private static function getResponse(){
		$data = '';
		if(! is_resource(self::$conn)){
			return false;
		}
		stream_set_timeout(self::$conn, self::$timeout);
		//while(! feof(self::$conn)){
			$str = fgets(self::$conn, 8192);
			$data .= $str;
		//}
			error_log(print_r($data, 1)."\n", 3, "D:/wamp/www/temp/d.log");
		return $data;
	}

	private static function helo($msg){
		self::sendCommand('Say Hello To Host', 'HELO '.$msg, 250);
	}

	private static function authLogin(){
		self::sendCommand('Auth Login', 'AUTH LOGIN', 334);
		self::sendCommand('Enter User Name', self::base64Code(self::$user), 334);
		self::sendCommand('Enter Password', self::base64Code(self::$password), 235);
	}

	/** 
	 * mail from 
	 */
	private static function mailFrom(){
		self::sendCommand('Set Email From', 'MAIL FROM: <'.self::$from.'>', 250);
	}

	/**
	 * mail to
	 */
	private static function rcptTo(){
		self::sendCommand('Set Email To', 'RCPT TO: <'.self::$to.'>', 250);
	}

	/**
	 * start to send mail data
	 *
	 */
	private static function startData(){
		self::sendCommand('Start Send Email Data', 'DATA', 354);
	}

	private static function sendData(){
		if(! self::$header){
			self::setHeader();
		}
		return self::send(self::$header);
	}

	private static function endData(){
		self::sendCommand('End Data', '.', 250);
	}

	/**
	 * 
	 * @param string $cc copy to other
	 * @param string $bcc blind carbon copy
	 */
	public static function sendMail($to, $subject, $body, $cc = '', $bcc = '', $attachment = '', $is_html = false){
		self::$to = $to;
		self::$subject = $subject;
		self::$body = $body;
		self::$cc = $cc;
		self::$bcc = $bcc;
		self::$attachment = $attachment;
		self::$is_html = $is_html;
		self::sendData();
		if(self::endData())
			return true;
		else
			return false;
	}

	public static function sendMailDemo(){
		$data = "From: lymoslymos@163.com\r\n".
				"To: 857296599\r\n".
				"Subject: xls-file\r\n".
				"Date: ".date('Y-m-d H:i:s')."\r\n".

				//"X-Mailer: shadowstar's mailer\r\n".
				"MIME-Version: 1.0\r\n".
				"Content-type: multipart/mixed;\r\n".  // 带附件需声明此头部信息
				// "Content-Type: multipart/related;\r\n"	// body有图片
				// "Content-Type: multipart/alternative;\r\n" // html or text
				"\tboundary='#BOUNDARY#'\r\n".  // 邮件各部分分隔符
				// 正文
				"\r\n".
				"--#BOUNDARY#\r\n".
				"Content-Type: text/plain; charset=gb2312\r\n".
				"Content-Transfer-Encoding: quoted-printable\r\n".
				"\r\n".
				"hello hello\r\n".
				"--#BOUNDARY#\r\n".

				// 附件
				"--#BOUNDARY#\r\n".
				"Content-Type: application/octet-stream; name=test.xls\r\n".
				"Content-Disposition: attachment; filename=test.xls\r\n".
				"Content-Transfer-Encoding: base64\r\n".
				"\r\n".
				self::base64Code(file_get_contents('D:/wamp/www/temp/a.xls')).
				"--#BOUNDARY#\r\n".
				"\r\n.";
		self::sendCommand($data);
	}

	private static function setHeader(){
		$header = 'From: '.self::$from.self::CRLF;
		$header .= 'To: '.self::$to.self::CRLF;
		$header .= 'Subject: '.self::$subject.self::CRLF;
		$header .= 'Data: '.date('Y-m-d H:i:s').self::CRLF;
		$header .= 'MIME-Version: 1.0'.self::CRLF;	// set the mime
		$header .= "\t".'boundary = "#boundary#'.self::CRLF;	// set the boundary in the mail port

		if(self::$attachment){
			// send with attachment
			$header .= 'Content-Type: multiport/mixed;'.self::CRLF;
		}else{
			// is html or only text
			$header .= 'Content-Type: multiport/alternative;'.self::CRLF;
		}


		// body skip a line
		$header .= self::CRLF;
		$header .= '--#boundary#'.self::CRLF;
		$header .= 'Content-Type: text/plain; charset='.self::$charset.self::CRLF;
		$header .= 'Content-Transfer-Encoding: quoted-printable'.self::CRLF;
		$header .= self::CRLF;
		$header .= self::$body.self::CRLF;
		$header .= '--#boundary#'.self::CRLF;

		// attachment
		if(self::$attachment){
			$header .= '--#boundary#'.self::CRLF;
			$header .= 'Content-Type: application/octet-stream; name='.self::$file_name.self::CRLF;
			$header .= 'Content-Disposition: attachment; filename='.self::$file_name.self::CRLF;
			$header .= 'Content-Transfer-Encoding: base64'.self::CRLF;
			$header .= self::CRLF;
			$header .= self::$file_content.self::CRLF;
			$header .= '--#boundary#'.self::CRLF;
			$header .= self::CRLF;
		}
		return self::$header = $header;
	}

	private static function base64Code($data){
		return base64_encode($data);
	}

	private static function showError($error){
		if(self::$debug){
			exit($error);
		}
	}

	private static function log($message){
		// record the mail sent logs
	}
}

/**
 * Usage: \Net\Smtp::open(); \Net\Smtp::sendMail($params...);
 */
?>