<?php
namespace Core\Cache;

class Cache{
	public static $instance = null;
	public static $cache_type = 'Memcache';

	public static __construct(){

	}

	public static getInstance(){
		if(self::$instance){
			return self::$instance;
		}else{
			return self::connect();
		}
	}

	public static function connect(){
		$type = strtoupper(self::$cache_type);
		$options = self::$options;
		extract($options);
		switch($type){
			case 'MEMCACHE':
				$instance = new Memcache();
				if(! isset($persistent)){
					$persistent = true;	// connect a long time
				}
				if(! isset($weight)){
					$weight = 1;
				}
				if(! isset($timeout)){
					$timeout = 30;
				}
				$instance->addServer($host, $port, $persistent, $weight, $timeout);
				break;
			case 'MEMCACHED':
				$instance = new Memcached();
				$instance->addServer($host, $port, $weight);
				break;
			case 'REDIS':
				$instance = new redis();
				$instance->connect($host, $port);
				break;
			default:
				return false;
		}
		return self::$instance = $instance;
	}

	public static function set($key, $value, $expire = 0){
		return self::$instance->set($key, $value, $expire);
	}

	public static function get($key){
		return self::$instance->get($key);
	}

	public static function delete($key){
		return self::$instance->delete($key);
	}

	public static function close(){
		if(is_resource(self::$instance)){
			$instance = self::$instance;
			$type = strtoupper(self::$cache_type);
			switch($type){
				case 'MEMCACHED':
				case 'MEMCACHE':
					$instance->close();
					break;
				case 'REDIES':

					break;
			}
		}
	}
}
?>