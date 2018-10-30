<?php 

class Model{

	public function userlogin($email,$password){

		include_once '../dbconfig/db.php';
		$db = new Db();
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

        if( $exec && $exec1 &&  $exec2 ){
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

	    

		

}

?>