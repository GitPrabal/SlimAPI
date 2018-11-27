<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->get('/', function (Request $request, Response $response, array $args) {


    $apiKey = urlencode('b/526Z7aZeA-o0zK7ODrQLJ1QLcXBD6fMPMYjZTXTn');
	
	// Message details
	$numbers = array(918105495600);
	$sender = urlencode('TXTLCL');
	$message = rawurlencode(' A demo msg');
 
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
	
	// Process your response here
	echo $response;





/*
    echo  '<center><h1>Welcome To Slim App</h1></center>';

    $imagePath = "/opt/lampp/htdocs/Slim/images/0001344001541080845.jpg";
    $newPath = "/opt/lampp/htdocs/Slim/requested_images/";
    $ext = '.jpg';
    $newName  = $newPath."a".$ext;

    $copied = copy($imagePath , $newName);

    if ((!$copied)) 
    {
        echo "Error : Not Copied";
    }
    else
    { 
        echo "Copied Successful";
    }
die;


$url = 'https://api.sendgrid.com/';
$user = 'Prabalgupta';
$pass = 'Prabal94074_';

$json_string = array(

  'to' => array(
    'prabal1.gupta@gmail.com'
  ),
  'category' => 'test_category'
);


$params = array(
    'api_user'  => $user,
    'api_key'   => $pass,
    'x-smtpapi' => json_encode($json_string),
    'to'        => 'prabal1.gupta@gmail.com',
    'subject'   => 'testing from curl',
    'html'      => 'Hi am inside the body',
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

echo  $response;

    
echo  '<center><h1>Welcome To Slim App</h1></center>';

*/

});

$app->get('/getCategory', function (Request $request, Response $response) {

    include_once '../controller/Controller.php';
    $controller = new Controller();
    $result = $controller->getAllCategory();
    return $result;

});

$app->post('/checkUserDetails',function(Request $request, Response $response){
    
    $user_id =  $request->getParam('user_id');
    include_once '../controller/Controller.php';
    $controller = new Controller();
    $result = $controller->checkUserDetails($user_id);
    return $result;

});




$app->post('/userlogin',function(Request $request , Response $response){

    $email    =  $request->getParam('email'); 
    $password =  $request->getParam('pass');
    include_once '../controller/Controller.php';
    $controller = new Controller();
    $result = $controller->userlogin($email,$password);
    return $result;
});


$app->post('/userlogout',function(Request $request , Response $response){
    $user_id    =  $request->getParam('user_id'); 
    include_once '../controller/Controller.php';
    $controller = new Controller();
    $result = $controller->userlogout($user_id);
    return $result;
});

$app->post('/checkUserPassword',function(Request $request, Response $response){

    $user_id    =  $request->getParam('user_id');
    $oldPass    = $request->getParam('oldPass');
    include_once '../controller/Controller.php';
    $controller = new Controller();
    $result = $controller->checkUserPassword($user_id,$oldPass);
    return $result;
});

$app->post('/changePassword',function(Request $request,Response $response){

    $user_id    =  $request->getParam('user_id');
    $newPass    = $request->getParam('newPass');

    include_once '../controller/Controller.php';
    $controller = new Controller();
    $result = $controller->changePassword($user_id,$newPass);
    return $result;

});

$app->post('/register',function(Request $request , Response $response){

    $fullname = $request->getParam('fullname');  
    $email    = $request->getParam('email'); 
    $password = $request->getParam('pass');
    $mobileno = $request->getParam('mobileno');

    include_once '../controller/Controller.php';
    $controller = new Controller();
    $result = $controller->UserRegister($fullname,$email,$password,$mobileno);
    return $result;
});

$app->post('/uploadDocs',function(Request $request, Response $response){

    $user_id       = $request->getParam('user_id');
    $document_id = $request->getParam('document_id');

    include_once '../controller/Controller.php';
    $controller = new Controller();

    return $result = $controller->uploadDocs($_FILES,$user_id,$document_id);
    
});

$app->post('/addCategory',function(Request $request, Response $response){
    
    $category       = $request->getParam('category');
    
    include_once '../controller/Controller.php';
    $controller = new Controller();

    return $result = $controller->addCategory($category);
});

$app->get('/getAllDocs',function(Request $request, Response $response){

      $user_id       = $_GET['id'];
      include_once '../controller/Controller.php';
      $controller = new Controller();
      return $result = $controller->getAllDocs($user_id);
});

$app->post('/getAllUser',function(Request $request, Response $response){
    $user_id       = $request->getParam('user_id');
    include_once '../controller/Controller.php';
    $controller = new Controller();
    return $result = $controller->getAllUser($user_id);
});


$app->post('/getUserApprovedDocs', function(Request $request, Response $response){
    $user_id       = $request->getParam('user_id');
    include_once '../controller/Controller.php';
    $controller = new Controller();
    return $result = $controller->getUserApprovedDocs($user_id);
});

$app->post('/shareUserDocuments',function(Request $request, Response $response){

    $user_id           = $request->getParam('user_id');
    $ipin              = $request->getParam('ipin');
    $selected_users    = $request->getParam('selected_users');
    $selected_document = $request->getParam('selected_document');

    include_once '../controller/Controller.php';
    $controller = new Controller();
    return $result = $controller->shareUserDocuments($user_id,$ipin,$selected_document,$selected_users);
});

$app->post('/getUserIpin' , function(Request $request, Response $response){

    $user_id           = $request->getParam('user_id');
    include_once '../controller/Controller.php';
    $controller = new Controller();
    return $result = $controller->getUserIpin($user_id);

});

$app->post('/setUserIpin' , function(Request $request, Response $response){
    
    $user_id           = $request->getParam('user_id');
    $ipin              = $request->getParam('ipin');
    $otp               = $request->getParam('otp');

    include_once '../controller/Controller.php';
    $controller = new Controller();
    return $result = $controller->setUserIpin($otp,$ipin,$user_id);

});

$app->post('/sendOtp' , function(Request $request, Response $response){

    $user_id       = $request->getParam('user_id');
    $otp           = $request->getParam('otp');
    include_once '../controller/Controller.php';
    $controller = new Controller();
    return $result = $controller->sendOtp($user_id,$otp);
});

$app->post('/getAllSharedDocsList',function(Request $request,Response $response){

$user_id = $request->getParam('user_id');
include_once '../controller/Controller.php';
$controller = new Controller();
return $result = $controller->getAllSharedDocsList($user_id);

});

/* Function is used to delete document which you have uploaded */

$app->post('/deleteUserDoc',function(Request $request, Response $response){

    $user_id     = $request->getParam('user_id');
    $document_id = $request->getParam('document_id');

    include_once '../controller/Controller.php';
    $controller = new Controller();
    return $result = $controller->deleteUserDoc($user_id,$document_id);
      
});

/* Below Function is used to make a request for document from other user */

$app->post('/requestForDocumentFromUser',function(Request $request,Response $response){

    $user_id             = $request->getParam('user_id');
    $document_id         = $request->getParam('document_id');
    $requested_user_name = $request->getParam('requested_user_name');
    $description         = $request->getParam('description');

    include_once '../controller/Controller.php';
    $controller = new Controller();
    return $result = $controller->requestForDocumentFromUser($user_id,$document_id,$requested_user_name,$description);
    
});

/* Below Functions is used retrive list of all documents 
   which is requested by logged in user from other users 
*/

$app->post('/myRequestedDocs',function( Request $request, Response $response){
    $user_id             = $request->getParam('user_id');
    include_once '../controller/Controller.php';
    $controller = new Controller();
    return $result = $controller->myRequestedDocs($user_id);
    
});


/* Below Function is used to retrive all documents 
   which is requested by other users from logged in user 
*/ 

$app->post('/requestedDocument', function (Request $request, Response $response, array $args) {

    $user_id = base64_decode( $request->getParam('user_id') );
    include_once '../controller/Controller.php';
    $controller = new Controller();
    return $result = $controller->requestedDocument($user_id);
});


/* Below Function is used to retrive count of all requested which is made
   by logged in users
   
*/

$app->post('/myRequestedDocsCount',function( Request $request, Response $response){
  
    $user_id             = $request->getParam('user_id');
    include_once '../controller/Controller.php';
    $controller = new Controller();
    return $result = $controller->myRequestedDocsCount($user_id);
    
});


/* Below function is used to send docs via email 
   Send Only For the Requested docs from other user to logged in user
*/

$app->post('/sendRequestedDocViaEmailToUser',function(Request $request,Response $response){

    $id      =  $request->getParam('id');
    $user_id =  $request->getParam('user_id');
    include_once '../controller/Controller.php';
    $controller = new Controller();
    return $result = $controller->sendRequestedDocViaEmailToUser($id,$user_id);

});
 


$app->run();


?>