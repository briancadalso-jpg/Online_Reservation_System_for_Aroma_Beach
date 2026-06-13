<?php

class Dbh
{
    private string $host = 'localhost';
    private string $user = 'root';
    private string $pwd = '';
    private string $dbName = 'aroma_db';
    protected mysqli $conn;

    public function connect(): mysqli
    {
        if (isset($this->conn) && $this->conn instanceof mysqli) {
            return $this->conn;
        }

        $this->conn = new mysqli($this->host, $this->user, $this->pwd, $this->dbName);

        if ($this->conn->connect_error) {
            die('Database connection failed: ' . $this->conn->connect_error);
        }

        $this->conn->set_charset('utf8mb4');

        return $this->conn;
    }
}
