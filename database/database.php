<?php
require_once "credentials.php";

class Database
{
    private static ?PDO $connection = NULL; // singleton

    public static function getConnection()
    {
        if (is_null(self::$connection)) {
            self::$connection = new PDO('mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_NAME,
                DATABASE_USERNAME, DATABASE_PASSWORD);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$connection;
    }
}
