<?php 

class Controller{

	public function userlogin($email,$password){

		 $email =  base64_decode($email);
		 $password =  base64_decode($password);

		include '../model/model.php';
		$model =  new Model();
		return  $model->userlogin($email,$password);
	}

	public function userlogout($user_id){
		$user_id = base64_decode($user_id);
		include '../model/model.php';
		$model =  new Model();
		return $model->userlogout($user_id);
	}

	public function UserRegister($fullname,$email,$password){
		include '../model/model.php';
		$model =  new Model();
		return $model->UserRegister($fullname,$email,$password);
	}


}


?>