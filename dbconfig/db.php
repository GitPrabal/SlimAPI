<?php 

class Db {

    public function connect($data){

        $servername = $data['servername'];
        $username = $data['username'];
        $password = $data['password'];
        $dbname = $data['dbname'];
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