<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->get('/', function (Request $request, Response $response, array $args) {
    echo  '<center><h1>Welcome To Slim App</h1></center>';
});

$app->post('/userlogin',function(Request $request , Response $response){
    $email    =  $request->getParam('email');
    $password =  $request->getParam('password');
    $controller = new Controller();
    return $controller->userlogin($email,$password);
});


$app->run();


?>