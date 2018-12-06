<?php 

class Controller{

	public function userlogin($email,$password){

		$email =  base64_decode($email);
		$password =  base64_decode($password);

		include '../model/model.php';
		$model =  new Model();
		return  $model->userlogin($email,$password);
	}

	public function checkUserDetails($user_id){

		include '../model/model.php';
		$model =  new Model();
		return  $model->checkUserDetails($user_id);
	}

	public function userlogout($user_id){
		$user_id = base64_decode($user_id);
		include '../model/model.php';
		$model =  new Model();
		return $model->userlogout($user_id);
	}

	public function UserRegister($fullname,$email,$password,$mobileno){
		include '../model/model.php';
		$model =  new Model();
		return $model->UserRegister($fullname,$email,$password,$mobileno);
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

	public function deleteUserDoc($user_id,$document_id){
		include '../model/model.php';
		$model =  new Model();
		return $model->deleteUserDoc($user_id,$document_id);
	}

	public function requestForDocumentFromUser($user_id,$document_id,$requested_user_name,$description){
		include '../model/model.php';
		$model =  new Model();
		return $model->requestForDocumentFromUser($user_id,$document_id,$requested_user_name,$description);
	}

	public function myRequestedDocs($user_id){
		include '../model/model.php';
		$model =  new Model();
		return $model->myRequestedDocs($user_id);
	}

	public function myRequestedDocsCount($user_id){
		include '../model/model.php';
		$model =  new Model();
		return $model->myRequestedDocsCount($user_id);
	}

	public function requestedDocument($user_id){
		include '../model/model.php';
		$model =  new Model();
		return $model->requestedDocument($user_id);
	}

	public function sendRequestedDocViaEmailToUser($id,$user_id){

		include '../model/model.php';
		$model =  new Model();
		return $model->sendRequestedDocViaEmailToUser($id,$user_id);

	}

	public function checkUserPassword($user_id,$oldPass){

		include '../model/model.php';
		$model =  new Model();
		return $model->checkUserPassword($user_id,$oldPass);

	}

	public function changePassword($user_id,$newPass){
		include '../model/model.php';
		$model =  new Model();
		return $model->changePassword($user_id,$newPass);
	}

	public function myNotificationForDocs($user_id){
		include '../model/model.php';
		$model =  new Model();
		return $model->myNotificationForDocs($user_id);
	}

		public function	verifyUserIpin($user_id,$ipin){
		include '../model/model.php';
		$model =  new Model();
		return $model->verifyUserIpin($user_id,$ipin);
	}


}


?>