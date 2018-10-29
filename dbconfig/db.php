<?php 

class Db {

    public function connect($dbname){
        switch ($dbname) {
            case 'Admin':
            $servername = 'localhost';
            $username = 'root';
            $password = '';
            $dbname = $dbname;
            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
    
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
    
            return $conn;
            break;

            default:
            break;

        }


    }

}


?>