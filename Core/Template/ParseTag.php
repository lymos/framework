<?php
namespace Template;

class ParseTag{

	private static $tags = [
						'php' => [
								'attr' => ''
								],
						'foreach' => [
									'attr' => 'item,key,id'
								],
						'if' => [
								'attr' => ''
								],
						'elseif' => [
									'attr' => ''
								],
						'else' => [
								'attr' => ''
								]
						];

	public function __construct(){

	}

	private static function _php($tag_name, $content){

	}

	private static function _foreach($tag_name, $content){

	}

	private static function _if($tag_name, $content){

	}

	private static function _elseif($tag_name, $content){

	}
	
	private static function _else($tag_name, $content){

	}
}
?>