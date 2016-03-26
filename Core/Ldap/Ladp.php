<?php
/**
 * ladp class 
 * Usage:
 * $obj = new \Ldap\Ldap('localhost');
 * $obj::add('cn=company,ou=admin,dc=us', ['dc' => 'admin']);
 * Or \Ldap\Ldap::init(); \Ldap\Ldap::getValues();
 */
namespace Ldap;

class Ldap{
	public static $instance = null;
	private static $_conn;

	public function __construct($host, $port = 0){
		return $this->init($host, $port);
	}

	/**
	 * Init The Host
	 * @param string $host
	 * @param int $port 
	 * @return mixed
	 */
	public static function init($host, $port = 0){
		if(self::$instance){
			return self::$instance;
		}else{
			return self::connect($host, $port);
		}
	}

	/**
	 * Connect To Host
	 */
	public static function connect($host, $port = 0){
		try{
			self::$_conn = ldap_connect($host, $port);
			if(is_resource(self::$_conn)){
				self::$instance = true;
				return $this;
			}
		}catch(\Exception $e){

		}
	}

	/**
	 * Set Options For Ldap
	 * @param array or string $option_name
	 * @param mixed $value
	 * @return void
	 */
	public static function setOptions($option_name, $value = null){
		if(is_array($option_name)){
			foreach($option_name as $option => $val){
				ldap_set_option(self::$_conn, $option, $val);
			}
		}else{
			ldap_set_option(self::$_conn, $option_name, $value);
		}
	}

	public function __get($name){
		return self::$name;
	}

	public function __set($name, $value){
		self::$name = $value;
	}

	/**
	 * @param string $dn like 'cn=company,ou=admin,dc=us'
	 * @param array $entry like ['dc' => 'admin']
	 * @return object
	 */
	public static function add($dn, $entry){
		if(! $dn){
			return false;
		}
		return ldap_add(self::$_conn, $dn, $entry);
	}

	/** 
	 * Delete Dn
	 */
	public static function delete($dn){
		if(! $dn){
			return false;
		}
		return ldap_delete(self::$_conn, $dn);
	}

	public static function bind($ldap_rdn = '', $ldap_pass = ''){
		if(! $ldap_rdn && ! $ldap_pass){
			return ldap_bind(self::$_conn);
		}
		return ldap_bind(self::$_conn, $ldap_rdn, $ldap_pass);
	}

	/**
	 * Close Host
	 */
	public static function unbind(){
		return ldap_unbind(self::$_conn);
	}

	/**
	 * Get Entry
	 */
	public static function getEntry($dn, $key){
		return self::search($dn, $key);
	}

	/**
	 * Search For A Result Return Entry
	 *
	 */
	public static function search($dn, $filter, $attr = []){
		$sr = ldap_search(self::$_conn, $dn, $filter, $attr);
		return ldap_get_entries(self::$_conn, $sr);
	}

	/**
	 * Get Values
	 */
	public static function getValues($dn, $attr){
		return ldap_get_values(self::$_conn, $dn, $attr);
	}
}

?>