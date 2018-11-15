<?php 

class Model{

	public function userlogin($email,$password){

		include_once '../dbconfig/db.php';

		/* Creating DB Class Object */

		$db = new Db();

		/* Passing DB Name To Connection
		   @param DB Name
		*/
		
		$conn = $db->connect('Admin'); 
		$conn->autocommit(FALSE);

	 	$salt_string  =  md5($email);
	 	$salt_string1 =  md5($password);
	 	$salt_string  = $salt_string."$".$salt_string1;

	 	$stmt = $conn->prepare("SELECT *,count(*) as count FROM registration where email=? and password=? and salt_string=?");
	 	$stmt->bind_param("sss",$email, $password,$salt_string);
	 	$stmt->execute();
	 	$result  = $stmt->get_result();
		$result  = $result->fetch_assoc();

	 	$count   = $result['count'];
	 	$user_id = $result['user_id'];
	 	$fullname  = $result['fullname'];
	 	$reg_date  = $result['reg_date'];
	 	$reg_date  = explode(' ', $reg_date);
	 	$reg_date  = $reg_date[0];
	 	$reg_date = date("M jS, Y", strtotime($reg_date));

     if($count > 0){
         $access_token = md5(microtime()); 
         $stmt = $conn->prepare("INSERT INTO `user_token`(`user_id`,`access_token`) VALUES (?, ?) on duplicate key update access_token=? ");
         $stmt->bind_param("sss", $user_id, $access_token,$access_token);
		 $stmt->execute();
		 $conn->autocommit(TRUE);
         $conn->close();
         $result =array("user_id"=>$user_id,"email"=>$email, "msg"=>"Logged In Successfully","status"=>"200","flag"=>true,'fullname'=>$fullname,'reg_date'=>$reg_date,'access_token'=>$access_token);
         $json = json_encode($result);
         return $json ;
     }else{
         $result =array("msg"=>"Invalid Credentials","status"=>"404","flag"=>false);
         $json = json_encode($result);
         return $json ;
     }

	}

	public function userlogout($user_id){
		
		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');
		$stmt = $conn->prepare("DELETE from user_token where user_id=?");
        $stmt->bind_param("s", $user_id);
        $result = $stmt->execute(); 
		$conn->close();

		$result =array("msg"=>"Deleted Successfully","status"=>"200","flag"=>true);
        $json = json_encode($result);
         return $json ;
	}

	public function UserRegister($fullname,$email,$password){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');

		$time    = microtime(); 
		$time2   = time();  
		$result  =  ceil(str_replace('.','',$time));
		$user_id = $result + $time2;

		$salt_string  =  md5($email);
		$salt_string1 =  md5($password);
		$salt_string  = $salt_string."$".$salt_string1;

		$conn->autocommit(FALSE);

		$count  = $this->checkUserAvailable($conn,$email);
		
		if($count > 0){
			$result =array("msg"=>"Email id already in use","status"=>"300");
			$json = json_encode($result);
			echo $json ;
			return;
		}

		$stmt = $conn->prepare("INSERT INTO `registration` (`user_id`,`fullname`, `email`, `password`,`salt_string`) 
		VALUES (?,?,?,?,?) ");
		$stmt->bind_param("sssss",$user_id,$fullname,$email,$password,$salt_string);
		$exec = $stmt->execute();

		$stmt1 = $conn->prepare("INSERT INTO `user_details` (`user_id`) 
		VALUES (?) ");
		$stmt1->bind_param("s",$user_id);
		$exec1 = $stmt1->execute();

		$stmt2 = $conn->prepare("INSERT INTO `expenses` (`user_id`) 
		VALUES (?) ");
		$stmt2->bind_param("s",$user_id);
		$exec2 = $stmt2->execute();

		$stmt3 = $conn->prepare("INSERT INTO `user_ipin` (`user_id`) 
		VALUES (?) ");
		$stmt3->bind_param("s",$user_id);
		$exec3 = $stmt3->execute();


        if( $exec && $exec1 &&  $exec2 && $exec3){
		$conn->autocommit(TRUE);
		}

		if( $exec && $exec1 &&  $exec2 )
		{

		$result =array("msg"=>"Account Created Succesfully","status"=>"200");
		$json = json_encode($result);
		return $json ;
		$conn->close();

		}else{
		$result =array("msg"=>"Unbale To Registered Users","status"=>"400");
		$json = json_encode($result);
		return $json ;
		}

	}

	public function checkUserAvailable($conn,$email){

		$stmt = $conn->prepare("Select count(*) as count from `registration` where email=? ");
		$stmt->bind_param("s",$email);
		$stmt->execute();
	 	$result  = $stmt->get_result();
		$result  = $result->fetch_assoc();
		$count  = $result['count'];
		return $count;
	}

	public function uploadDocs($resultImage,$user_id,$document_id){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');
		$conn->autocommit(FALSE);
		
		$stmt = $conn->prepare("Select count(*) as count from `user_docs` where document_id=? and user_id=? ");
		$stmt->bind_param("ss",$document_id,$user_id);
		$stmt->execute();
	 	$result  = $stmt->get_result();
		$result  = $result->fetch_assoc();
		$count  = $result['count'];

		if($count > 0){

			$result =array("msg"=>"Document Is Already Added","status"=>"300");
			$json = json_encode($result);
			return $json;
		}


		$stmt2 = $conn->prepare("INSERT INTO `user_docs` (`user_id`,`document_id`,`document_image`) 
		VALUES (?,?,?) ");
		$stmt2->bind_param("sss",$user_id,$document_id,$resultImage);
		$exec2 = $stmt2->execute();
		
		if($exec2){
			$conn->autocommit(TRUE);
			$result =array("msg"=>"Your Document Is Added","status"=>"200");
			$json = json_encode($result);
			return $json;
		}else{
			$conn->autocommit(FALSE);
			$result =array("msg"=>"Unable To Add Document","status"=>"500");
			$json = json_encode($result);
			return $json;
		}

	}

	public function addCategory($category){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');

		$conn->autocommit(FALSE);


		$stmt = $conn->prepare("Select count(*) as count from `document_category` where document_name=? ");
		$stmt->bind_param("s",$category);
		$stmt->execute();
	 	$result = $stmt->get_result();
		$result = $result->fetch_assoc();
		$count  = $result['count'];

		if($count > 0){
			$result =array("msg"=>"Category Already Added","status"=>"300");
			$json = json_encode($result);
			echo  $json;
			return;
		}
		
		$stmt2 = $conn->prepare("INSERT INTO `document_category` (`document_name`) 
		VALUES (?) ");
		$stmt2->bind_param("s",$category);
		$exec2 = $stmt2->execute();

		if($exec2){
			$conn->autocommit(TRUE);
			$result =array("msg"=>"Category Added Succesfully","status"=>"200");
			$json = json_encode($result);
		}else{
			$conn->autocommit(TRUE);
			$result =array("msg"=>"Unable To Add Category","status"=>"500");
			$json = json_encode($result);
	
		}

		$conn->close();
		return $json ;
	}

	public function getAllCategory(){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');

		$stmt = $conn->prepare("SELECT id,document_name from document_category");
	 	$stmt->execute();
		$result  = $stmt->get_result();
		$cat = array();

		while($row = $result->fetch_assoc()) {
			$cat[] = $row;
		}
		echo  json_encode($cat);
		die;
	}
	
	
	public function getAllDocs($user_id){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');
		$stmt = $conn->prepare("SELECT user_docs.id,isApproved,document_id,document_image,image_url,document_name from user_docs left JOIN document_category on user_docs.document_id=document_category.id where user_id=?");
		$stmt->bind_param("s",$user_id);
	 	$stmt->execute();
		$result  = $stmt->get_result();
		$cat = array();

		while($row = $result->fetch_assoc()) { 
			$cat[] = $row;
		}
		echo json_encode($cat);
		
	}

	public function getAllUser($user_id){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');
		$stmt = $conn->prepare("SELECT fullname,id,user_id from registration where user_id!=?");
		$stmt->bind_param("s",$user_id);
	 	$stmt->execute();
		$result  = $stmt->get_result();
		$cat = array();

		while($row = $result->fetch_assoc()) { 
			$cat[] = $row;
		}
		echo json_encode($cat);

	}

	public function getUserApprovedDocs($user_id){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');
		$stmt = $conn->prepare("SELECT user_docs.id,isApproved,document_id,document_image,image_url,document_name from user_docs left JOIN document_category on user_docs.document_id=document_category.id where user_id=? and isApproved=1");
		$stmt->bind_param("s",$user_id);
	 	$stmt->execute();
		$result  = $stmt->get_result();
		$cat = array();

		while($row = $result->fetch_assoc()) { 
			$cat[] = $row;
		}
		echo json_encode($cat);
	}

	public function shareUserDocuments($user_id,$ipin,$selected_document,$selected_users){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');

		$document_id = implode(',' , $selected_document );
		$share_id    = implode(',' , $selected_users );

		$stmt = $conn->prepare("SELECT count(user_ipin) as count from user_ipin where user_ipin=? and user_id=?");
		$stmt->bind_param("ss",$ipin,$user_id);
	 	$stmt->execute();
		$result  = $stmt->get_result();
		$row = $result->fetch_assoc();

		if($row['count']==0 || $row['count']=='0'){
			$result =array("msg"=>"Invalid IPIN","status"=>"402");
			$json = json_encode($result);
			return $json;
		}

		$transaction_id =  microtime();
		$transaction_id = str_replace('.','',$transaction_id);
		$transaction_id = str_replace(' ','',$transaction_id);

		$stmt1 = $conn->prepare("INSERT INTO `share_document` (`user_id`,`share_with`,`document_id`,`transaction_id`) 
		VALUES (?,?,?,?) ");
		$stmt1->bind_param("ssss",$user_id,$share_id,$document_id,$transaction_id);
		$exec2 = $stmt1->execute();

		if($exec2){
			$result =array("msg"=>"Document Has Been Shared","status"=>"200");
			$json = json_encode($result);
			return $json;
		}else{
			$result =array("msg"=>"Unable to share document","status"=>"500");
			$json = json_encode($result);
			return $json;
		}
	}

	public function getUserIpin($user_id){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');
		$stmt = $conn->prepare("SELECT user_ipin from user_ipin where user_id=?");
		$stmt->bind_param("s",$user_id);
		$exec2 = $stmt->execute();
		$result    = $stmt->get_result();
		$row       = $result->fetch_assoc(); 
		$user_ipin = $row['user_ipin'];

		if(empty($user_ipin)  )
		{
			$result =array("msg"=>"Ipin Not Found","status"=>"500");
			$json = json_encode($result);
			return $json;
		}else{
			$result =array("msg"=>"Ipin Found","status"=>"200");
			$json = json_encode($result);
			return $json;
		}
	}

	public function setUserIpin($otp,$ipin,$user_id){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');
		$conn->autocommit(FALSE);

		$stmt = $conn->prepare("SELECT count(*) as count  from user_otp where otp=?");
		$stmt->bind_param("s",$otp);
		$exec2 = $stmt->execute();
		$result    = $stmt->get_result();
		$row       = $result->fetch_assoc(); 
		$count     = $row['count'];

		if($count > 0){

		$stmt1 = $conn->prepare("UPDATE user_ipin set user_ipin=? where user_id=?");
		$stmt1->bind_param("ss",$ipin,$user_id);
		$exec2 = $stmt1->execute();

		if($exec2){
			$conn->autocommit(TRUE);
			$result =array("status"=>"200");
			$json = json_encode($result);
			return  $json;

		}else{
			$result =array("msg"=>"Unable To Set Ipin","status"=>"500");
			$json = json_encode($result);
			return  $json;
		}

		}else{
			$result =array("status"=>"505");
			$json = json_encode($result);
			return  $json;
		}
	}

	public function sendOtp($user_id,$otp){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');

		$stmt = $conn->prepare("INSERT INTO `user_otp`(`user_id`,`otp`) VALUES (?, ?) on duplicate key update otp = ?");
		$stmt->bind_param("sss", $user_id,$otp,$otp);
		$exec = $stmt->execute();

		if($exec){

			$conn->autocommit(TRUE);
			$result =array("msg"=>"OTP set Successfully","status"=>"200");
			$json = json_encode($result);
			return  $json;

		}else{

			$result =array("msg"=>"Unable To Set OTP","status"=>"500");
			$json = json_encode($result);
			return  $json;
		}

	}

	public function getAllSharedDocsList($user_id){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');

		$stmt = $conn->prepare("SELECT share_with,document_id from share_document where user_id=?");
		$stmt->bind_param("s",$user_id);
		$exec2 = $stmt->execute();
		$result = $stmt->get_result();
		
		$list      = array();
		$user_list = array();
		$user_parent_id = array();

		while($row  = $result->fetch_assoc()){
              $list[]  = $row;
		} 

		for( $i=0; $i<count($list); $i++){
		$user_parent_id[] = explode(",",$list[$i]['share_with']);	
		}

		$singleArray = array();
		$singleArray1 = array();

    foreach ($list as $key => $value){
        $singleArray[$key] = $value['share_with'];
	}

	// foreach ($user_parent_id as $key => $value){
    //     $singleArray1[$key] = $value['share_with'];
    // }




		// for( $j = 0; $j < count($user_parent_id[$i]); $j++ ){

		// $stmt = $conn->prepare("SELECT fullname,email  from registration where id=?");
		// $stmt->bind_param("s",$list[$i]['share_with']);
		// $exec2 = $stmt->execute();
		// $result    = $stmt->get_result();
        // while($row         = $result->fetch_assoc()){
        //       $user_list[] = $row;
		// }
   
		// }

		echo  '<pre>';print_r($user_parent_id);die;

echo  '<pre>';print_r($singleArray);
echo  '--------------';
echo  '<pre>';print_r($singleArray1);

die;
		



	}

		

}

?>