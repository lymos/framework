<?php
/**
 * html5 usage: 
 * var ws = new WebSocker("ws://host:port"); 
 * ws.onopen = function(res){}
 * ws.onmessage = function(res){}
 * ws.send(your data)
 * ws.onclose = function(res){}
 *
 *
 */
namespace d;

class webSocketServer{

	const CRLF = "\r\n";
	public static $instance = null;
	public static $host = '0.0.0.0';
	private static $_socket = null;
	const HANDSHAKE_KEY = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';	// handshake key 
	public static $port = 5566;
	public static $socket_config = [
								'domain' => AF_INET,
								'type' => SOCK_STREAM,
								'protocol' => SOL_TCP
							];
	private static $_clients = [];	// [client_id => [socket => val, handshake => false]]
	private static $_names;

	public function __construct(){
		
	}

	public static function getInstance(){
		if(! self::$instance){
			return self::$instance = new self();
		}else{
			return self::$instance;
		}
	}

	public static function init(){
		if(php_sapi_name() !== 'cli'){
			echo 'Please Use Cli.';
			return ;
		}
		ob_implicit_flush(true);
		self::_run();
	}

	private static function _run(){
		self::_socketCreate();
		self::_socketBind();	// bind
		self::_listen();		// listen
		self::_loop();	
	}

	private static function _loop(){
		while(true){
			self::_acceptConnection();
		}
	}

	private static function _listen(){
		try{
			$status = socket_listen(self::$_socket);
			if(! $status){
				throw new \Exception(self::getError(self::$_socket));
			}
		}catch(\Exception $e){
			echo $e->getMessage();
		}
	}

	/**
	 * server client handshake
	 *
	 */
	private static function _handshake($accept_socket, $buffer){
		$find_key_text = substr($buffer, strpos($buffer, 'Sec-WebSocket-Key:') + 18);
		$ws_key = trim(substr($find_key_text, 0, strpos($find_key_text, "\r\n")));
		$new_ws_key = base64_encode(sha1($ws_key . self::HANDSHAKE_KEY, true));

		$header = 'HTTP/1.1 101 Switching Protocol' . self::CRLF;
		$header .= 'Upgrade: websocket' . self::CRLF;
		$header .= 'Connection: Upgrade' . self::CRLF;
		$header .= 'Sec-WebSocket-Version: 13' . self::CRLF;
		$header .= 'Sec-WebSocket-Accept: ' . $new_ws_key . self::CRLF . self::CRLF;

		if(self::_writeData($accept_socket, $header)){

			
			self::$_clients[] = array('socket' => $accept_socket, 'handshake' => true);
			self::writeToClients();
		}

	}

	public static function writeToClients(){
		if(! self::$_clients){
			return false;
		}
		error_log(print_r(self::$_clients, 1)."\n", 3, "D:/wamp/www/temp/d.log");

		foreach(self::$_clients as $key => $rs){

			self::_writeData($rs['socket'], self::_encode('Hello MAND'));
		}
	}

	private static function _uncode($string){
	    $mask = array();  
	    $data = '';  
	    $msg = unpack('H*', $string);  
	    $head = substr($msg[1], 0, 2);  
	    if (hexdec($head{1}) === 8) {  
	       $data = false;  
	    }else if (hexdec($head{1}) === 1){  
	       $mask[] = hexdec(substr($msg[1],4,2));
	       $mask[] = hexdec(substr($msg[1],6,2));
	       $mask[] = hexdec(substr($msg[1],8,2));
	       $mask[] = hexdec(substr($msg[1],10,2));
	       $s = 12;  
	       $e = strlen($msg[1])-2;  
	       $n = 0;  
	       for ($i=$s; $i<= $e; $i+= 2) {  
	         $data .= chr($mask[$n%4]^hexdec(substr($msg[1],$i,2)));  
	         $n++;  
	       }  
	    }  
	    return $data;
  	}

	/**
	 * encode websocket data frame
	 *
	 */
	private static function _encode($string){
		$message = preg_replace(['/\r$/', '/\n$/', '/\r\n$/'], '', $string);
		$frame = [];
		$frame[0] = 81;
		$length = strlen($message);
		$frame[1] = $length < 16 ? '0' . dechex($length) : dechex($length);		// length 10 => 16hex
      	$frame[2] = self::_ordHex($message);
      	$data = implode('', $frame);
      	return pack("H*", $data);		// pack data into binary string
    }

    /**
     * return ord hex string
     *
     */
    private static function _ordHex($string){  
    	$message = '';
    	$length = strlen($string);
    	for($i = 0; $i < $length; $i++){
    		$message .= dechex(ord($string{$i}));
    	}
    	return $message;
    }

	private static function _socketCreate(){
		try{
			self::$_socket = socket_create(self::$socket_config['domain'], self::$socket_config['type'], self::$socket_config['protocol']);
			if(! self::$_socket){
				throw new \Exception(self::getError());
			}
			// socket_set_nonblock(self::$_socket);
			// socket_set_block(self::$_socket);
		}catch(\Exception $e){	
			echo $e->getMessage();
		}
	}

	private static function _socketBind(){
		if(! self::$_socket){
			self::_socketCreate();
		}
		try{
			$status = socket_bind(self::$_socket, self::$host, self::$port);
			if(! $status){
				throw new \Exception(self::getError());
			}
		}catch(\Exception $e){
			echo $e->getMessage();
		}
	}

	private static function _findClient($socket){
		if(! self::$_clients){
			return false;
		}
		foreach(self::$_clients as $key => $rs){
			if($rs['socket'] === $socket){
				return $key;
			}
		}
		return false;
	}

	private static function _acceptConnection(){

		$accept_socket = socket_accept(self::$_socket);

		if($accept_socket !== false){
			$response = socket_read($accept_socket, 8192);
			//error_log(print_r($response, 1)."\n", 3, "D:/wamp/www/temp/d.log");

			// no handshake
			if(self::_findClient($accept_socket) === false){
				self::_handshake($accept_socket, $response);
			}else{

			}

		}
	}

	/**
	 * write data to client
	 *
	 */
	private static function _writeData($accept_socket, $data){
		if(! is_resource($accept_socket)){
			return false;
		}
		try{
			$len = socket_write($accept_socket, $data);
			//error_log(print_r($len, 1)."\n", 3, "D:/wamp/www/temp/d.log");

			if(! $len){
				throw new \Exception(self::getError());
			}
			return $len;
		}catch(\Exception $e){
			echo $e->getMessage();
		}
		
	}

	private static function _connect(){
		try{
			$status = socket_connect(self::$_socket, self::$host, self::$port);
			if(! $status){
				throw new \Exception(self::getError());
			}
		}catch(\Exception $e){
			echo $e->getMessage();
		}
	}

	public static function getError($socket = false){
		if($socket){
			$error_code = socket_last_error($socket);
		}else{
			$error_code = socket_last_error();
		}
		return 'Error Code: ' . $error_code . ' Error Message: ' . socket_strerror($error_code); 
	}

	private static function _close($socket){
		if(! is_resource($socket)){
			return false;
		}
		socket_close($socket);
	}
}
\d\webSocketServer::init();

