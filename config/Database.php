<?php 
 class Database {
    //DB params
    private $host = 'localhost';
    private $db_name = 'register_now';
    private $username = 'Amir';
    private $password = '123456';
    private $conn;
    

    // DB connect
    public function connect(){
        $this->conn = null;

        try {
            $this->conn = new PDO('mysql:hosts=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
        } catch(PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();

        }

        return $this->conn;

    }









 }