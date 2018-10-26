<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->get('/', function (Request $request, Response $response, array $args) {
    echo  '<center><h1>Welcome To Slim App</h1></center>';
});

$app->get('/id/{id}', function(Request $request,Response $response,array $args){
return     $id = $args['id']; 
});


$app->post('/userlogin',function(Request $request , Response $response,array $args){

$email    =  $request->getParam('email');
$password =  $request->getParam('password');

$salt_string  =  md5($email);
$salt_string1 =  md5($password);
$salt_string  = $salt_string."$".$salt_string1;

$data  =  array("servername"=>"localhost","password"=>"","username"=>"root","dbname"=>"Admin");

try {

    $db    = new Db();
    $conn  = $db->connect($data); 
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

        $stmt = $conn->prepare("INSERT INTO `user_token`(`user_id`,`access_token`) VALUES (?, ?)");
        $stmt->bind_param("ss", $user_id, $access_token);

        $user_id      = $user_id;
        $access_token = $access_token;
        $stmt->execute();
        $conn->close();
    
        $result =array("user_id"=>$user_id,"email"=>$email, "msg"=>"Logged In Successfully","status"=>"200","flag"=>true,'fullname'=>$fullname,'reg_date'=>$reg_date,'access_token'=>$access_token);
        $json = json_encode($result);
        echo $json ;

    }else{
        $result =array("msg"=>"Invalid Credentials","status"=>"404","flag"=>false);
        $json = json_encode($result);
        echo $json ;
    }
    





}catch(PDOException $e){
    echo $e->getMessage();
} 


});


$app->run();


?>