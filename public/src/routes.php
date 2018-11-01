<?php

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




$app->run();


?>