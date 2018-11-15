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

	public function uploadDocs($files,$user_id,$document_id )
	{
		$resultImage =  $this->uploadImage($files);
		include '../model/model.php';
		$model =  new Model();
		return $model->uploadDocs($resultImage,$user_id,$document_id);
	}

	public function uploadImage($files){

		$str  = microtime();
		$str  = str_replace(' ','',$str);
		$name = str_replace('.','',$str);
		$extension = pathinfo($files["myFile"]["name"], PATHINFO_EXTENSION);
		$imageName = $name;
		$result =	move_uploaded_file($files["myFile"]["tmp_name"],"../images/".$imageName.".".$extension);
		return $imageName.".".$extension;

	}

	public function addCategory($category)
	{
		include '../model/model.php';
		$model =  new Model();
		return $model->addCategory($category);
	}

	public function getAllCategory(){
		include '../model/model.php';
		$model =  new Model();
		return $model->getAllCategory();
	}

	public function getAllDocs($user_id){
		include '../model/model.php';
		$model =  new Model();
		return $model->getAllDocs($user_id);
	}

	public function getAllUser($user_id){
		include '../model/model.php';
		$model =  new Model();
		return $model->getAllUser($user_id);
	}

	public function getUserApprovedDocs($user_id){
		include '../model/model.php';
		$model =  new Model();
		return $model->getUserApprovedDocs($user_id);
	}

	public function shareUserDocuments($user_id,$ipin,$selected_document,$selected_users){
		include '../model/model.php';
		$model =  new Model();
		return $model->shareUserDocuments($user_id,$ipin,$selected_document,$selected_users);
	}

	public function getUserIpin($user_id){
		include '../model/model.php';
		$model =  new Model();
		return $model->getUserIpin($user_id);
	}

	public function setUserIpin($otp,$ipin,$user_id){
		include '../model/model.php';
		$model =  new Model();
		return $model->setUserIpin($otp,$ipin,$user_id);
	}

	public function sendOtp($user_id,$otp){
		include '../model/model.php';
		$model =  new Model();
		return $model->sendOtp($user_id,$otp);
	}

	public function getAllSharedDocsList($user_id){
		include '../model/model.php';
		$model =  new Model();
		return $model->getAllSharedDocsList($user_id);
	}


}


?>