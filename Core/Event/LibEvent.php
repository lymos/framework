<?php
/**
 * libEvent class
 * Usage:
 * \Event\LibEvent::addEvent(['max_loop_num' => 5, 'callback' => 'self::setName']);
 * callback your callback function
 * \Event\LibEvent::addTimer('self::setN', 5);
 */
namespace Event;

class LibEvent{
	/*
	public static function setName($args){
		print_r($args);
	}
	public static function setN(){
		echo 'test';
	}
	*/

	public function __construct(){

	}

	/**
	 * add event for loop
	 * @param [] $params ['max_loop_num' => 10, ...] 如果没有max_loop_num 则事件会一直存在并执行
 	 * @param string your callback function
	 * @return void
	 */
	public static function addEvent($params, $callback = false){
		$base = event_base_new();	// create event base
		$event = event_new();	// create a new event
		// EV_PERSIST 事件一直存在，即会一直调用回调函数 when event_del() is called it will end
		$args = [
			'event' => $event,
			'base' => $base,
			];
		if(! is_array($params)){
			$params = [$params];
		}
		$args = array_merge($args, $params);

	//	$fd = STDIN; // STDIN常量 文件句柄 需要获取文件内容时
		$fd = 0;
		event_set($event, $fd, EV_READ | EV_PERSIST, 'self::_loop', $args);
		event_base_set($event, $base);
		
		event_add($event, 100);
		event_base_loop($base);
	}
	
	/**
	 * loop function 
	 * @param source $fd 句柄
	 * @param int $events
	 * @param [] $args 
	 * @return void
	 */
	private static function _loop($fd, $events, $args){
		static $loop_num = 0;
		$loop_num++;
		if(isset($args['max_loop_num']) && $loop_num >= $args['max_loop_num']){
			event_del($args['event']);
		}
		if(isset($args['callback'])){
			call_user_func($args['callback'], $args);
		}
	}

	/**
	 * 定时执行 
	 * @param string $callback function
	 * @param float $time_delay second
	 * @return void
	 */
	public static function addTimer($callback, $time_delay = '60'){
		$base = event_base_new();
		$event = event_new();

		event_set($event, 0, EV_TIMEOUT, $callback);
		event_base_set($event, $base);

		event_add($event, $time_delay * 1000000);
		event_base_loop($base);
	}
	
}
// \Event\LibEvent::addEvent(['max_loop_num' => 5, 'callback' => 'self::setName']);
// \Event\LibEvent::addTimer('self::setN', 5);

