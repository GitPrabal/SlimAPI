<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app = new \Slim\App;

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");

    return $response;
});

$app->get('/', function (Request $request, Response $response, array $args) {
    echo  phpinfo();
});

$app->get('/id/{id}', function(Request $request,Response $response,array $args){
return     $id = $args['id']; 
});


$app->post('/userlogin',function(Request $request , Response $response,array $args){
echo $username =  $request->getParam('name');

    echo  "-------------";

    echo  $email =  $request->getParam('email');

    echo  "-------------";
    
    echo  $password =  $request->getParam('password');

    die;



/*
    try {
    $sql = "SELECT *,count(*) as count FROM registration where email='$email' and password='$password' and salt_string='$salt_string' "; ;
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
        }
    } else {
        echo "0 results";
    }
    $conn->close();
}catch(PDOException $e){
    echo $e->getMessage();
} 
*/  

});


$app->run();


?>