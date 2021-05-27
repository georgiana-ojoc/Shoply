<?php
require_once __DIR__ . "/../../database/database.php";

function updateRating($user, $name, $stars)
{
    $query = "SELECT username FROM users WHERE username = :user";
    $statement = Database::getConnection()->prepare($query);
    $statement->execute(array("user" => $user));
    if ($statement->rowCount() == 0) {
        return "Nu exista utilizatorul \"" . $user . "\" in baza noastra de date.";
    }

    if (strpos($name, "_")) {
        $name = explode("_", $name)[1];
    }
    $name = str_replace("-", "", $name);

    $query = "SELECT link FROM products WHERE REPLACE(REPLACE(link, '/', ''), '-', '') LIKE CONCAT('%', :name, '%')";
    $statement = Database::getConnection()->prepare($query);
    $statement->execute(array("name" => $name));
    if ($statement->rowCount() == 0) {
        return "Nu exista produsul specificat in baza noastra de date.";
    }

    $row = $statement->fetch(PDO::FETCH_ASSOC);
    $product = $row["link"];
    $query = "SELECT rating FROM rating WHERE user = :user AND product = :product";
    $statement = Database::getConnection()->prepare($query);
    $statement->execute(array("user" => $user, "product" => $product));
    $statement->rowCount();
    if ($statement->rowCount() == 0) {
        $query = "INSERT INTO rating (user, product, rating) VALUES (:user, :product, :rating)";
        $insertStatement = Database::getConnection()->prepare($query);
        $insertStatement->execute(array("user" => $user, "product" => $product, "rating" => $stars));
        return null;
    }
    $query = "UPDATE rating SET rating = :rating WHERE user = :user AND product = :product";
    $statement = Database::getConnection()->prepare($query);
    $statement->execute(array("rating" => $stars, "user" => $user, "product" => $product));
    return null;
}
