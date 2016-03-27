<?php
/**
* Model class
* Usage \Model::init($table) or new \Model($table)
*/
namespace Model;

use Db;

class Model{
	public static $table;

	public function __construct($table){
		self::init($table);
	}

	public static function init($table){
		self::setTable($table);
		self::$db = \Db::getInstance();
		self::$db::table(self::$table);
	}

	public static function setTable($table){
		self::$table = $table;
	}

	public static function getTable(){
		return self::$table;
	}

	public static function add($data){
		return self::$db::insert($data);
	}

	public static function addMulti($data){
		return self::$db::insertMulti($data);
	}

	/**
	* Find one Record
	* @param array $options 
	* @return mixed
	*/
	public static function findOne($options){
		if(! is_array($options)){
			return false;
		}
		if(isset($options['fields'])){
			self::fields($options['fields']);
		}
		if(isset($options['where'])){
			self::$db->where($options['where']);
		}
		return self::$db->limit(1)->findOne();
	}

	/**
	* Find Multi Record
	* @static
	* @access public
	* @param array $options
	* @param mixed
	*/
	public static function find($options){
		if(! is_array($options)){
			return false;
		}
		if(isset($options['fields'])){
			self::fields($options['fields']);
		}
		if(isset($options['where'])){
			self::$db->where($options['where']);
		}
		if(isset($options['group'])){
			self::$db->group($options['group']);
		}
		if(isset($options['orderby'])){
			self::$db->orderBy($options['orderby']);
		}
		if(isset($options['limit'])){
			if(strpos($options['limit'], ',') > 0){
				$limit_ar = explode(',', $options['limit']);
				self::$db->limit($limit_ar[0], $limit_ar[1]);
			}else{
				self::$db->limit($options['limit']);
			}
		}
		return self::$db->findAll();
	}

	public static function update($data, $where){
		if(! $where){
			return false;
		}
		return self::$db->where($where)->update($data);
	}

	public static function delete($where){
		if(! $where){
			return false;
		}
		return self::$db->where($where)->delete();
	}
}
?>