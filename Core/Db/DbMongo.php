<?php
/**
 * mongodb 
 * Usage: \Db\DbMongo::getInstance(); \Db\DbMongo::findOne(foo);
 */
namespace Db;
class DbMongo{
	public static $conn; // connect instance
	public static $host = '127.0.0.1';
	public static $port = 27017;
	public static $db_name;
	public static $table;
	public static $db; // db instance
	public static $collection; // collection instance like sql table but no increment

	public function __construct(){

	}

	/**
	 * instance a db 
	 */
	public static function getInstance(){
		if(is_resource(self::$conn)){
			return self;
		}else{
			self::connect();
			return self;
		}
	}

	public static function connect(){
		return self::$conn = new \MongoClient(self::$host . ':' . self::$port);
	}

	/**
	 * if the db name is not exists it will be created
	 */
	public static function setDbName($name = ''){
		if(! self::$conn){
			self::connect();
		}
		if($name){
			return self::$db = self::$conn->$name;
		}else{
			return self::$db = self::$conn->$db_name;
		}
	}

	/**
	 * if the collection name is not exists it will be created
	 */
	public static function setTable($name = ''){
		if(! self::$db){
			self::setDbName();
		}
		if($name){
			return self::$collection = self::$db->$name;
		}else{
			return self::$collection = self::$db->$table;
		}
	}

	public static function insert($data){
		if(! self::$collection){
			self::setTable();
		}
		return self::$collection->insert($data);
	}

	public static function delete($where){
		if(! self::$collection){
			self::setTable();
		}
		return self::$collection->remove($where);
	}

	public static function update($data, $where){
		if(! self::$collection){
			self::setTable();
		}
		return self::$collection->update($data, $where);
	}

	public static function findOne($where){
		if(! self::$collection){
			self::setTable();
		}
		return self::$collection->findOne($where);
	}

	public static function find($where){
		if(! self::$collection){
			self::setTable();
		}
		return self::$collection->find($where);
	}

	public static function close(){
		self::$conn->close();
	}

}
?>