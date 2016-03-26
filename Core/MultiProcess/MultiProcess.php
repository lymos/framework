<?php
/**
 * multi process class
 * Usage:
 * \MultiProcess\MultiProcess::pcntlCall();	
 */
namespace MultiProcess;

class MultiProcess{

	public function __construct(){

	}

	/**
	 * @param int $process_num 创建的进程数
	 * @param string $callback 回调函数(处理自己的逻辑业务)
	 * @param [] $params $callback的参数
	 * @return [] $data
	 */
	public static function pcntlCall($process_num = 5, $callback, $params = []){
		$pids_arr = [];
		// 多进程开始
		for($i = 0; $i < $process_num; $i++){
			$pids_arr[$i] = $pid = pcntl_fork();

			switch($pid){
				case -1:
					echo 'fork failed';
					exit;
					break;
				case 0:
					if($callback){
						$data = call_user_func($callback, $params);
						$key = ftok(__FILE__, 't') . getmypid();
						self::_setDataToMemory($key, $data);
					}
					exit;	// 退出子进程
				default:
					break;
			}
		}
		// 多进程结束

		// 上述子进程完后 回归主进程 取出多进程中取到的数据
		$data = [];
		foreach($pids_arr as $pid_val){
			pcntl_waitpid($pid_val, $status);	// 结束进程
			$key = ftok(__FILE__, 't') . $pid_val;
			$ret = self::_getDataFromMemory($key);
			$data = array_merge($data, $ret);
		}
		return $data;
	}

	/**
 	 * save data to memory
	 */
	private static function _setDataToMemory($shm_key, $data){
		if(is_array($data)){
			$data = json_encode($data);
		}
		$shm_id = shmop_open($shm_key, 'c', 0777, strlen($data));
		$status = shmop_write($shm_id, $data, 0);
		shmop_close($shm_id);
		return $status;
	}

	/**
	 * get data from memory
	 */
	private static function _getDataFromMemory($shm_key){
		if(! $shm_key){
			return false;
		}
		$shm_id = shmop_open($shm_key, 'w', 0, 0);
		$ret = shmop_read($shm_id, 0, shmop_size($shm_id));
		shmop_close($shm_id);
		shmop_delete($shm_id);
		return json_decode($ret, true);
	}
}
//\MultiProcess\MultiProcess::pcntlCall();
