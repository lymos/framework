<?php
namespace Net;
use Net\Smtp; 

class Mail{
	public static $mail = null;
	public static $host_list = [
								'{imap.qq.com:993/imap2/ssl}INBOX',
								'{imap.aliyun.com:993}/ssl}INBOX'
							];
	public static $host = '{imap.qq.com:993/imap2/ssl}INBOX';
	public static $port = 25;
	public static $user = '2759105440@qq.com';
	public static $pwd = '111111';
	public static $smtp_port = 25;
	public static $smtp_host = 'smtp.163.com';
	public static $timeout = 30;
	public static $protocol = 'smtp';

	public function __construct(){

	}

	public static function getInstance(){
		if(self::$mail){
			return self::$mail;
		}else{
			return self::open();
		}
	}

	public static function open(){
		switch(self::$protocol){
			case 'smtp':
				self::$mail = Smtp::open(self::$smtp_host, self::$smtp_port, self::$timeout);
				break;
			case 'pop3':
				break;
			default:
				break;
		}
		//self::$mail = imap_open(self::$host, self::$user, self::$pwd);
		return self::$mail;
	}

	public static function getMail(){
		// use imap pop3 
	}

	public static function sendMail(){
		// use smtp   fsockopen() socket to send
		$fp = fsockopen(self::$smtp_host, self::$smtp_port, $errno, $errstr, self::$timeout);

	}
}
?>