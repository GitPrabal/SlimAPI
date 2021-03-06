<?php 

class Model{

	public function __construct(){
		date_default_timezone_set('Asia/Kolkata');
	}

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

	public function checkUserDetails($user_id){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');

		$stmt = $conn->prepare("Select fullname,email,mobile_no from `registration` where user_id=? ");
		$stmt->bind_param("s",$user_id);
		$stmt->execute();
	 	$result  = $stmt->get_result();
		$result  = $result->fetch_assoc();
		$json    = json_encode($result);

		return $json;


	}

	public function UserRegister($fullname,$email,$password,$mobileno){

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

		$stmt = $conn->prepare("INSERT INTO `registration` (`user_id`,`fullname`,`mobile_no`,`email`, `password`,`salt_string`) 
		VALUES (?,?,?,?,?,?) ");
		$stmt->bind_param("ssssss",$user_id,$fullname,$mobileno,$email,$password,$salt_string);
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
		$stmt = $conn->prepare("SELECT fullname,id,email,user_id from registration where user_id!=?");
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

		$stmt1 = $conn->prepare("SELECT document_id,document_image,image_url from user_docs where document_id = ? and user_id=?");
		$stmt1->bind_param("ss",$document_id,$user_id);
	 	$stmt1->execute();
		$result  = $stmt1->get_result();
		$row = $result->fetch_assoc();

		$image_url             = $row['image_url'];
		$loaded_document_image = $row['document_image'];
		
		$document_image = $image_url.$loaded_document_image;


		$transaction_id =  microtime();
		$transaction_id = str_replace('.','',$transaction_id);
		$transaction_id = str_replace(' ','',$transaction_id);

		$transaction_date =  date("d-m-Y");
		$transaction_time = date("h:i:s a"); 


		$stmt1 = $conn->prepare("INSERT INTO `share_document` (`user_id`,`share_with`,`document_id`,`document_image`,`transaction_id`,`transaction_date`,`transaction_time`) 
		VALUES (?,?,?,?,?,?,?) ");
		$stmt1->bind_param("sssssss",$user_id,$share_id,$document_id,$document_image,$transaction_id,$transaction_date,$transaction_time);
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

		$stmt1 = $conn->prepare("SELECT mobile_no from registration where user_id=?");
		$stmt1->bind_param("s",$user_id);
		$exec2     = $stmt1->execute();
		$result    = $stmt1->get_result();
		$row       = $result->fetch_assoc(); 
		$mobile_no = $row['mobile_no'];

		$stmt = $conn->prepare("INSERT INTO `user_otp`(`user_id`,`otp`) VALUES (?, ?) on duplicate key update otp = ?");
		$stmt->bind_param("sss", $user_id,$otp,$otp);
		$exec = $stmt->execute();
		
		$message = 'Your OTP for set ipin is ';
		
		if($exec){
			
			/* Below Function is used to send OTP Via SMS */
			// $sendOtp = $this->sendMessage($mobile_no,$otp,$message);
			$conn->autocommit(TRUE);
			$result =array("msg"=>"OTP set Successfully","status"=>"200");
			$json = json_encode($result);
			echo  $json;

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
		
		$stmt = $conn->prepare("select document_category.document_name,share_document.document_image,share_document.transaction_date,share_document.transaction_time,registration.fullname,registration.email,share_document.note,share_document.transaction_date from registration INNER join share_document on registration.user_id = share_document.share_with INNER JOIN document_category on share_document.document_id = document_category.id where share_document.user_id = ?");
		$stmt->bind_param("s",$user_id);
		$exec2 = $stmt->execute();
		$result = $stmt->get_result();
		$list = array();
		while($row = $result->fetch_assoc()) 
		{
			$list[] = $row;
		}

        echo  json_encode($list);die;

		
	}

	public function deleteUserDoc($user_id,$document_id){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');

		$stmt = $conn->prepare("delete from user_docs where user_id=? and id=?");
		$stmt->bind_param("ss",$user_id,$document_id);
		$exec = $stmt->execute();

		if($exec){
			$result =array("msg"=>"Deleted Successfully","status"=>"200");
			$json = json_encode($result);
			echo   $json;
		}else{
			$result =array("msg"=>"Unable To Delet","status"=>"500");
			$json = json_encode($result);
			echo   $json;
		}
	}

	public function requestForDocumentFromUser($user_id,$document_id,$requested_user_name,$description){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');
		
		/* @param 
		   $user_id             = logged in user id
		   $requested_user_name = By whom a logged in user making a request
		
		  */
		$approved = 1;

		$stmt = $conn->prepare("select count(*) as count from user_docs where user_id=? and document_id = ? and isApproved = ? ");
		$stmt->bind_param("sss",$requested_user_name,$document_id,$approved);
		$exec = $stmt->execute();
		$result = $stmt->get_result();
		$list  = array();
		$row   = $result->fetch_assoc();
		$count = $row['count'];

		if($count > 0){
		   $status ="200";
		}else{
		   $status ="500";
		}

		$requested_date =  date("d-m-Y");
		$requested_time = date("h:i:s a");
		$approved = 0;
        $requested_with =  $requested_user_name;

		$stmt3 = $conn->prepare("select count(*) as count from user_request where requested_by=? and requested_for=? and requested_with=? and approved=?");
		$stmt3->bind_param("ssss",$user_id,$document_id,$requested_with,$approved);
		$exec = $stmt3->execute();
		$result = $stmt3->get_result();
		$row   = $result->fetch_assoc();
		$doc_count = $row['count']; 

		if($doc_count > 0){

		$result =array("msg"=>"Duplicate Request Found","status"=>"400");
		$json = json_encode($result);
		return $json;
		}
        $isSeen=0;
		$stmt1 = $conn->prepare("INSERT INTO `user_request`( `requested_by`,`requested_for`,`requested_with`,`description`,`approved`, `isSeen` ,`requested_date`,`requested_time` ) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?) ");
		$stmt1->bind_param("ssssssss", $user_id,$document_id,$requested_user_name,$description,$approved,$isSeen,$requested_date,$requested_time);
		$exec = $stmt1->execute();

		$stmt4 = $conn->prepare("select fullname,mobile_no from registration where user_id = ?");
		$stmt4->bind_param("s",$user_id);
		$exec = $stmt4->execute();
		$result = $stmt4->get_result();
		$row   = $result->fetch_assoc();
		$mobile_no = $row['mobile_no']; 
		$fullname = $row['fullname']; 


		$stmt4 = $conn->prepare("select fullname from registration where user_id = ?");
		$stmt4->bind_param("s",$requested_user_name);
		$exec = $stmt4->execute();
		$result = $stmt4->get_result();
		$row   = $result->fetch_assoc();
 		$reciever_name = $row['fullname']; 

		$otp ='';
		$message = "Hi $reciever_name,you have a document request from a user $fullname Please login to smart docs and share docs";
		
		/* Below Function is used to send SMS to requested user */
		
		//$sendOtp = $this->sendMessage($mobile_no,$otp,$message);

		$result =array("msg"=>"User Request Document Found","status"=>$status);
		$json = json_encode($result);
		return $json;
		
	}

	public function myRequestedDocs($user_id){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');

		$stmt = $conn->prepare("SELECT count(registration.fullname) as count ,registration.fullname as fullname,document_category.document_name as document_name,if(user_request.approved='0','Pending','Sent By') as status from registration INNER JOIN user_request on registration.user_id = user_request.requested_with INNER JOIN document_category on user_request.requested_for = document_category.id where user_request.requested_by=? GROUP BY user_request.requested_for");
		$stmt->bind_param("s",$user_id);
		$exec = $stmt->execute();
		$result = $stmt->get_result();
		$list  = array();
		while($row   = $result->fetch_assoc()){
			$list[]  = $row;
		}

		echo  json_encode($list);

	}

	public function requestedDocument($user_id){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');

		$stmt = $conn->prepare("SELECT user_request.description,user_request.id as id , user_request.requested_date, user_request.requested_time,registration.fullname as fullname,document_category.document_name as document_name,user_request.approved as status from registration INNER JOIN user_request on registration.user_id = user_request.requested_by INNER JOIN document_category on user_request.requested_for = document_category.id where user_request.requested_with=?");
		$stmt->bind_param("s",$user_id);
		$exec = $stmt->execute();
		$result = $stmt->get_result();
		$list  = array();
		while($row   = $result->fetch_assoc()){
			$list[]  = $row;
		}

		if(!$list){
			$result = array("msg"=>"No data found ","status"=>"false");
			return json_encode($result);
		}

		$isSeen=1;
		$stmt  = $conn->prepare("update user_request set isSeen=? where requested_with=? ");
		$stmt->bind_param("ss",$isSeen,$user_id);
		$exec = $stmt->execute();



		return  json_encode($list);
	}

	public function myRequestedDocsCount($user_id){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');
		$stmt = $conn->prepare("SELECT count(registration.fullname) as count from registration INNER JOIN user_request on registration.user_id = user_request.requested_with INNER JOIN document_category on user_request.requested_for = document_category.id where user_request.requested_by=?");
		$stmt->bind_param("s",$user_id);
		$exec = $stmt->execute();
		$result = $stmt->get_result();

		$row   = $result->fetch_assoc();
		echo $count = $row['count']; 
		die;
	}

	public function sendRequestedDocViaEmailToUser($document_id,$id,$user_id,$note){


		

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');
		//$stmt = $conn->prepare("SELECT registration.email as email from registration INNER JOIN user_request on registration.user_id = user_request.requested_by INNER JOIN document_category on user_request.requested_for = document_category.id where user_request.id=?");
        $stmt  = $conn->prepare("SELECT registration.email as email,user_request.requested_by as requested_user,user_docs.document_image from registration INNER JOIN user_request on registration.user_id = user_request.requested_by INNER JOIN document_category on user_request.requested_for = document_category.id INNER JOIN user_docs on user_docs.document_id=user_request.requested_for where user_request.id=? and user_docs.user_id=?");
		$stmt->bind_param("ss",$id,$user_id);
		$exec = $stmt->execute();
		$result = $stmt->get_result();
		$row   = $result->fetch_assoc();
		$documentFoundCount =  count($row);

		if($documentFoundCount == 0)
		{
			$json   = array("msg"=>"Document Not Found","status"=>"404");
			$json = json_encode($json);
			return $json;
		}

		$stmt  = $conn->prepare("update user_request set approved=1 where id=?");
		$stmt->bind_param("s",$document_id);
		$exec = $stmt->execute();




		$email          = $row['email'];
		$document_image = $row['document_image'];

		$imagePath = "/opt/lampp/htdocs/Slim/images/".$document_image;
		$newPath   = "/opt/lampp/htdocs/Slim/requested_images/";

	 	$newName   = $newPath.$document_image; 
		$copied    = copy($imagePath , $newName);

		$result   = array("msg"=>"Document Sent","status"=>"200");
		$result   = json_encode($result);
		return $result;
		

		$document_image =  $this->saveImageWithText($note,$imagePath);

		if($copied){

			$result = $this->sendEmailWithAttachment($email,$document_image);

			$approved1=0;
			$update=1;

			$stmt  = $conn->prepare("update user_request set approved=? where id=? and approved = ?");
			$stmt->bind_param("sss",$update,$id,$approved1);
			$exec = $stmt->execute();

			/* Retrive  user id and id of requested user
			*/


			$stmt1  = $conn->prepare("SELECT requested_by,requested_for,requested_with from  user_request where id=?");
			$stmt1->bind_param("s",$id);
			$exec1 = $stmt1->execute();
			$result1 = $stmt1->get_result();
			$row1   = $result1->fetch_assoc();

			$share_with     = $row1['requested_with'];
			$document_id    = $row1['requested_for'];
            $requested_by   = $row1['requested_by'];


			/* 
			Retrive Document Image 
			*/

			$stmt2  = $conn->prepare("SELECT image_url,document_image from user_docs where user_id=? and document_id=?");
			$stmt2->bind_param("ss",$share_with,$document_id);
			$exec2   = $stmt2->execute();
			$result2 = $stmt2->get_result();
			$row2    = $result2->fetch_assoc();

			$document_image = $row2['image_url'].$row2['document_image'];
			$transaction_id =  microtime();
			$transaction_id = str_replace('.','',$transaction_id);
			$transaction_id = str_replace(' ','',$transaction_id);
	
			$transaction_date =  date("d-m-Y");
			$transaction_time = date("h:i:s a"); 
	 
			$stmt1 = $conn->prepare("INSERT INTO `share_document` (`user_id`,`share_with`,`document_id`,
			`document_image`,`note`,
			`transaction_id`,`transaction_date`,`transaction_time`) 
			VALUES (?,?,?,?,?,?,?,?) ");
			$stmt1->bind_param("ssssssss",$user_id,$requested_by,$document_id,$document_image,$note,$transaction_id,$transaction_date,$transaction_time);
			$exec2 = $stmt1->execute();

			

			


			$json   = array("msg"=>"Document Send","status"=>"200");
			$json = json_encode($json);

			return $json;

		}else{
			$json   = array("msg"=>"Unable To Send Document","status"=>"500");
			return json_encode($json);
		}

	}

	public function sendEmailWithAttachment($email,$document_image){

		$url = 'https://api.sendgrid.com/';
		$user = 'Prabalgupta';
		$pass = 'Prabal94074_';

		$json_string = array(

		'to' => array(
			$email
		),
		'category' => 'test_category'
		);

		$link = 'http://sendimages.smartdocuments.com/'.$document_image;

		$html ='Dear Customer your requested document has been shared via below link
				This Link will valid for 30 minutes 
				';
		$html = $html.$link;		

		$params = array(
			'api_user'  => $user,
			'api_key'   => $pass,
			'x-smtpapi' => json_encode($json_string),
			'to'        => $email,
			'subject'   => 'Request Document',
			'html'      => $html,
			'text'      => 'Its a texting ',
			'from'      => 'prabal4747@gmail.com',
		);

		$request =  $url.'api/mail.send.json';
		// Generate curl request
		$session = curl_init($request);
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		// obtain response
		$response = curl_exec($session);
		curl_close($session);

		return  $response;
	}

	public function checkUserPassword($user_id,$oldPass){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');
        $stmt  = $conn->prepare("SELECT count(*) as count from registration where user_id = ? and password=?");
		$stmt->bind_param("ss",$user_id,$oldPass);
		$exec = $stmt->execute();
		$result = $stmt->get_result();
		$row   = $result->fetch_assoc();
		$count = $row['count'];
		echo $count;
		die;

	}

	public function changePassword($user_id,$newPass){

		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');

		$stmt  = $conn->prepare("SELECT email from registration where user_id = ?");
		$stmt->bind_param("s",$user_id);
		$exec   = $stmt->execute();
		$result = $stmt->get_result();
		$row    = $result->fetch_assoc();
		$email  = $row['email'];

		$salt_string  =  md5($email);
		$salt_string1 =  md5($newPass);
		$salt_string  = $salt_string."$".$salt_string1;


		$stmt  = $conn->prepare("update registration set password=?,salt_string=? where user_id=?");
		$stmt->bind_param("sss",$newPass,$salt_string,$user_id);
		$exec = $stmt->execute();
		if($exec){
			$json   = array("msg"=>"Password Change Successfully","status"=>"200");
			$json = json_encode($json);
			return $json;
		}else{
			$json   = array("msg"=>"Unable to Change Password","status"=>"500");
			$json = json_encode($json);
			return $json;
		}

	}

	public function sendMessage($mobile_no,$otp='',$message){

		$apiKey = urlencode('b/526Z7aZeA-o0zK7ODrQLJ1QLcXBD6fMPMYjZTXTn');
	
		// Message details
		
		$numbers = array($mobile_no);
		$sender = urlencode('TXTLCL');
		$message = rawurlencode($message.$otp);
		
		$numbers = implode(',', $numbers);
		
		// Prepare data for POST request
		$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
		
		// Send the POST request with cURL
		$ch = curl_init('https://api.textlocal.in/send/');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
	}

	public function myNotificationForDocs($user_id){
		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');
		$isSeen=0;

        $stmt  = $conn->prepare("SELECT count(*) as count from user_request where requested_with=? and isSeen=?");
		$stmt->bind_param("ss",$user_id,$isSeen);
		$exec = $stmt->execute();
		$result = $stmt->get_result();
		$row   = $result->fetch_assoc();
		echo  $row['count']; die;
	}

	public function verifyUserIpin($user_id,$ipin){
		
		include_once '../dbconfig/db.php';
		$db = new Db();
		$conn = $db->connect('Admin');

		$stmt  = $conn->prepare( "SELECT count(*) as count from user_ipin where user_id=? and user_ipin = ?" );
		$stmt->bind_param("ss",$user_id,$ipin);
		$exec = $stmt->execute();
		$result = $stmt->get_result();
		$row   = $result->fetch_assoc();
		$count = $row['count'];
		if($count > 0){
		$json   = array("msg"=>"Ipin found","status"=>"200");
		$json = json_encode($json);
		return $json;
		}else{
			$json   = array("msg"=>"Ipin Not found","status"=>"500");
			$json = json_encode($json);
			return $json;
		}

	}

	/* Convert Text into Image and save it */


	public function saveImageWithText($text,$source_file) {
   
		$public_file_path = '.';
		 
		// Copy and resample the imag
		list($width, $height) = getimagesize($source_file);
		$image_p = imagecreatetruecolor($width, $height);
		$image = imagecreatefromjpeg($source_file);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width, $height); 
		 
		// Prepare font size and colors
		$text_color = imagecolorallocate($image_p, 0, 0, 0);
		$bg_color = imagecolorallocate($image_p, 255, 255, 255);
		$font = '/opt/lampp/htdocs/Slim/model/arial.ttf';

		$font_size = 25; 
		 
		// Set the offset x and y for the text position
		$offset_x = 0;
		$offset_y = 20;
		 
		// Get the size of the text area
		$dims = imagettfbbox($font_size, 2, $font, $text);
		$text_width = $dims[4] - $dims[6] + $offset_x;
		$text_height = $dims[3] - $dims[5] + $offset_y;

		// Add text background
		imagefilledrectangle($image_p, 0, 0, $text_width, $text_height, $bg_color);
		 
		// Add text
		imagettftext($image_p, $font_size, 1, $offset_x, $offset_y, $text_color, $font, $text);

		// image font_size angle x y  color fontfile text
		 
		// Save the picture

		$time = rand(10,1000);

		// $result = imagejpeg($image_p, $public_file_path . '/'.$time.'.jpg', 100); 
		$result = imagejpeg($image_p, $public_file_path . '/output.jpg', 100); 

		echo  $result;die;


	    
		// Clear
		// imagedestroy($image); 
		// imagedestroy($image_p); 
	  }
	  


}

?>