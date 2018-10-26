<?php 

class Controller{
	public function __construct(){

	}
	
	public function userlogin(){
		$model = new Model();
		return $model->userlogin();
	}
}


?>