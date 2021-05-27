<?php
require_once __DIR__ . "/../../database/database.php";

class LoginModel
{
    public function addUser($username, $password)
    {
        $statement = "INSERT IGNORE INTO users (username, password) VALUES (:username, :password)";
        $query = Database::getConnection()->prepare($statement);
        $query->execute(array(
            "username" => $username,
            "password" => password_hash($password, PASSWORD_DEFAULT)
        ));
        if ($query->rowCount() == 0) {
            return "Numele de utilizator este deja luat.";
        } else {
            return "";
        }
    }

    public function validateUser($username, $password)
    {
        $statement = "SELECT username, password FROM users WHERE username = :username";
        $query = Database::getConnection()->prepare($statement);
        $query->execute(array("username" => $username));
        if ($query->rowCount() == 0) {
            return "Numele de utilizator este incorect.";
        }
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if (!password_verify($password, $row["password"])) {
            return "Parola este incorectă.";
        }
        return "";
    }
}
