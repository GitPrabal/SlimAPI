<?php 

class Db {

    public $servername = 'localhost';
    public $username = 'root';
    public $password = '';
    public $dbname = 'Admin';

    public function connect(){

        $conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

}


?>