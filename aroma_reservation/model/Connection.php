<?php

class Dbh {
    private $host = "localhost";
    private $user = "root";
    private $pwd = "";
    private $dbName = "aroma_db";
    protected $conn;

    public function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pwd, $this->dbName);

        if ($this->conn->connect_error) {
            echo "Connection failed: ";
        } else {
            echo "Connected";
        }

    }
}
