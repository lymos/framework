<?php
namespace File;

class File{
	private static $name;
	private static $file_path;

	public function __construct(){

	}

	/**
	 * write a file
	 * @param string $file
	 * @param string $content
	 * @param string $operate_type r w r+ a+ and so on
	 * @return boolean
	 */
	public static function write($file, $content, $operate_type = 'a+'){
		if(! self::checkFile($file)){
			return false;
		}
		$fp = fopen($file, $operate_type);
		$status = fwrite($fp, $content);
		fclose($fp);
		return $status;
	}

	public static function read($file, $operate_type = 'r'){
		if(! self::checkFile($file)){
			return false;
		}
		$fp = fopen($file, $operate_type);
		$content = '';
		while(! feof($fp)){
			$content .= fread($fp, filesize($file));
		}
		return $content;
	}

	public static function copy($file_origin, $file_to){
		if(! self::checkFile($file_origin)){
			return false;
		}
		return copy($file_origin, $file_to);
	}

	/**
	 * cut a file
	 * @static
	 * @access public 
	 * @param string $file_origin
	 * @param string $file_to
	 * @return boolean
	 */
	public static function cut($file_origin, $file_to){
		if(! self::checkFile($file_origin)){
			return false;
		}
		return rename($file_origin, $file_to);
	}

	private static function checkFile($file){
		if(! $file || ! is_file($file) || ! file_exists($file)){
			throw new Execption('not a file or file: '.$file.' is not found!');
			return false;
		}
		return true;
	}

	public static function delete($file){
		if(self::checkFile($file))
			return unlink($file);
		else
			return false;
	}

	public static function uploadFile($files, $path, $name = ''){
		if(! is_array($files) || ! is_uploaded_file($files)){
			return false;
		}
		$name ? $file_name = $name : $file_name = $files['tmp_name'];
		$status = move_uploaded_file($files, $path.$file_name);
		return ['name' => $file_name, 
				'type' => $files['type'], 
				'size' => $files['size']
				];
	}

	/**
	 * Loop The Directory And Files
	 * @param string $dir
	 */
	public static function loopDir($dir){
		if(! $dir){
			return false;
		}
		$data = [];
		if(is_file($dir)){
			return $data[] = $dir;
		}
		$handle = opendir($dir);
		if(! is_resource($handle)){
			return false;
		}
	
		while(false !== ($file = readdir($handle))){
			if($file == '.' || $file == '..'){
				continue;
			}
			$file_path = $dir . DIRECTORY_SEPARATOR . $file;
			if(is_dir($file_path)){
				$data['dir-' . $file] = loopDir($file_path);
			}else{
				$data[] = $file_path;
			}
		}
		closedir($handle);
		return $data;
	}
}