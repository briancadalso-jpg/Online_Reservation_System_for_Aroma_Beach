<?php include "Connection.php"; 

class Client extends Dbh {

public function view() {
    $sql = $this->connect()->query("SELECT * FROM users");

    return $sql;
}

}







?>