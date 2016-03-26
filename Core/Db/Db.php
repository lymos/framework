<?php
namespace Db;
/**
 * db_type support mysql oracle sqlite pgsql sybase and so on
 *
 */

class Db{
	public static $instance = null;		// db instance
	public static $config;	// config
	public static $options = [];

	public function __construct(){

	}

	public static function getInstance(){
		self::$config = getConfig();
		if(self::$instance){
			return self;
		}
		return self::connect();
	}

	public static function connect(){
		try{
			if(! extension_loaded('pdo_'.self::$Config['db_type'])){
				throw new Exception('can not loaded extension pdo_'.self::$config['db_type']);
			}
			$db_config = self::$config['db'];
			try{
				self::$instance = self::$conn = PDO($db_config['dsn'], $db_config['user'], $db_config['pwd']);
				return self;
			}catch(PDOException $e){
				throw new Exception('connect failed: '.$e->getMessage());
			}	
		}catch(Exception $e){

		}
	}

	/**
	 * @static
	 * @access public 
	 * @return int affected rows
	 */
	public static function execute($sql = ''){
		if(! self::$conn){
			self::connect();
		}
		return self::$conn->exec($sql);
	}

	public static function query($sql){
		if(! self::$conn){
			self::connect();
		}
		if(! $sql){
			return false;
		}
		return self::$conn->query($sql);
	}

	public static function table($table){
		self::$table = $table;
		return self;
	}

	public static function getFieldsAttr(){
		$sql = 'desc '.self::$table;
		self::$fields_attr = self::query($sql);
	}

	public static function where($where){
		if(is_array($where)){
			if(! self::$fields_attr){
				self::getFieldsAttr();
			}
			foreach($where as $field => $value){
				if(self::$fields_attr[$field] === 'string'){
					self::$options['where'] .= $field.' = "'.addslashes($value).'" and ';
				}else{
					self::$options['where'] .= $field.' = '.$value.' and ';
				}
			}
		}else{
			self::$options['where'] = $where;
		}
		return self;
	}

	public static function fields($fields = '*'){
		if(is_array($fields)){
			self::$options['fields'] = implode(', ', $fields);
		}else{
			self::$options['fields'] = $fields;
		}
		return self;
	}

	public static function groupBy($group){
		if(is_array($group)){
			self::$options['group'] = implode(', ', $group);
		}else{
			self::$options['group'] = $group;
		}
		return self;
	}

	public static function limit($start, $num = ''){
		self::$options['limit'] = 'limit '.$start.' '.$num;
		return self;
	}

	public static function orderBy($orderby){
		self::$options['orderby'] = ' order by ' . $orderby;
		return self;
	}

	public static function count(){
		self::$options['fields'] = 'count(*)';
		return self::findOne();
	}

	public static function findOne(){
		$option = self::$options;
		$sql = 'select '.$option['fields'].' from '.self::$table.' where 1=1 '
			.$option['where']
			.$option['group']
			.' limit 1';
		$statment = self::$conn->prepare($sql);
		if($statment->execute()){
			return $statment->fetch();
		}else{
			return false;
		}
	}

	public static function findAll(){
		$option = self::$options;
		$sql = 'select '.$option['fields'].' from '.self::$table.' where 1=1 '
			.$option['where']
			.$option['group']
			.$option['limit'];
		$statment = self::$conn->prepare($sql);
		if($statment->execute()){
			return $statment->fetchAll();
		}else{
			return false;
		}
	}

	public static function insert($data){
		if(! is_array($data)){
			return false;
		}
		if(! self::$fields_attr){
			self::getFieldsAttr();
		}
		$fields = $values = [];
		foreach($data as $field => $value){
			$fields[] = $field;
			if(self::$fields_attr[$field] === 'string'){
				$values[] = '"'.addslashes($value).'"';
			}else{
				$values[] = $value;
			}
		}
		$fields_str = implode(',', $fields);
		$values_str = implode(',', $values);
		$sql = 'insert into '.self::$table.' ('.$fields_str.') values ('.$values_str.')';
		$statment = self::$conn->prepare($sql);
		return $statment->execute();
	}

	public static function insertMulti($data){
		if(! is_array($data)){
			return false;
		}
		if(! self::$fields_attr){
			self::getFieldsAttr();
		}
		$fields = $values = $temp = [];
		foreach($data as $key => $rs){
			foreach($data as $field => $value){
				$fields[] = $field;
				if(self::$fields_attr[$field] === 'string'){
					$values[] = '"'.addslashes($value).'"';
				}else{
					$values[] = $value;
				}
			}
			if($key == 0){
				$fields_str = implode(',', $fields);
			}
			$values_str = implode(',', $values);
			$temp[] = '('.$values_str.')';
		}
		$sql = 'insert into '.self::$table.' ('.$fields_str.') values '.implode(',' $temp);
		$statment = self::$conn->prepare($sql);
		return $statment->execute();
	}

	public static function update($data){
		if(! $data){
			return false;
		}
		if(! self::$fields_attr){
			self::getFieldsAttr();
		}
		$temp_data = [];
		foreach($data as $field => $value){
			if(self::$fields_attr[$field] == 'string'){
				$temp_data[] = $field.' = "'.addslashes($value).'"';
			}else{
				$temp_data[] = $field.' = '.$value;
			}
		}
		$sql = 'update '.self::$table.' set '.implode(',', $temp_data).self::$options['where'];
		$statment = self::$conn->prepare($sql);
		return $statment->execute();
	}

	public static function delete(){
		$sql = 'delete from '.self::$table.' '.self::$options['where'];
		$statment = self::$conn->prepare($sql);
		return $statment->execute();
	}

	public static function commit(){

	}

	public static function rollBack(){

	}
}
print_r(get_loaded_extensions());
?>