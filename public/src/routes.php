<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->get('/', function (Request $request, Response $response, array $args) {
    echo  '<center><h1>Welcome To Slim App</h1></center>';
});

$app->get('/getCategory', function (Request $request, Response $response) {

    include_once '../controller/Controller.php';
    $controller = new Controller();
    $result = $controller->getAllCategory();
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

$app->post('/register',function(Request $request , Response $response){

    $fullname = $request->getParam('fullname');  
    $email    = $request->getParam('email'); 
    $password = $request->getParam('pass'); 

    include_once '../controller/Controller.php';
    $controller = new Controller();
    $result = $controller->UserRegister($fullname,$email,$password);
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
 from which is requested by other users from logged in user 
*/ 

$app->post('/requestedDocument', function (Request $request, Response $response, array $args) {

    $user_id = base64_decode( $request->getParam('user_id') );
    include_once '../controller/Controller.php';
    $controller = new Controller();
    return $result = $controller->requestedDocument($user_id);
});

$app->run();


?>