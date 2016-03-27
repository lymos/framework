<?php
namespace CommonException;

class CommonException extends \Exception{

	public function errorMessage(){
		$message = $this->getMessage();
		return 'This Has An Error: '.$message;
	}
}
?>