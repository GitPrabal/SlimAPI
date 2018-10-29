<?php 

class Db {

    public function connect($data){
        $servername = 'localhost';
        $username = 'root';
        $password = '';
        $dbname = $data ;
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        return $conn;

    }

}


?>