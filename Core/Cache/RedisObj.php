<?php
namespace Core\Cache;

class RedisObj extends Cache{

	public static $options = [
			'host' => '127.0.0.1',
			'port' => 11211
		];

	public function __construct(){
		self::init();
	}

	public static function init(){
		self::$cache_type = 'Redis';
		return self::getInstance();
	}

}
/**
 * usage 
 * Redis::init(); Redis::get(); or 
 * new Redis(); Redis::get();
 */
?>