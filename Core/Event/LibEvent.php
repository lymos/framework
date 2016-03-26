<?php
/**
 * libEvent class
 *
 */
namespace Event;

class LibEvent{

	public function __construct(){

	}

	public static function init(){
		$new = event_base_new();
		print_r($new);
	}
}
\Event\LibEvent::init();

