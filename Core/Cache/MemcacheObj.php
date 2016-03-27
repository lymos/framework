<?php
namespace Core\Cache;

class MemcacheObj extends Cache{

	public static $options = [
			'host' => '127.0.0.1',
			'port' => 11211,
			'weight' => 63
		];

	public function __construct(){
		self::init();
	}

	public static function init(){
		self::$cache_type = 'Memcached';
		return self::getInstance();
	}

}
/**
 * usage 
 * Memcache::init(); Memcache::get(); or 
 * new Memcache(); Memcache::get();
 */
?>