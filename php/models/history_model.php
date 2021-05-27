<?php
session_start();
require_once __DIR__ . "/../../database/database.php";

function updateViews($name)
{
    if (strpos($name, "_")) {
        $name = explode("_", $name)[1];
    }
    $name = str_replace("-", "", $name);

    $query = "UPDATE products SET views = views + 1 WHERE REPLACE(REPLACE(link, '/', ''), '-', '') LIKE " .
        "CONCAT('%', :name, '%')";
    $statement = Database::getConnection()->prepare($query);
    $statement->execute(array("name" => $name));
}

function updateHistory($user, $name)
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
    $query = "SELECT id FROM history WHERE user = :user AND product = :product";
    $statement = Database::getConnection()->prepare($query);
    $statement->execute(array("user" => $user, "product" => $product));
    $statement->rowCount();
    if ($statement->rowCount() == 0) {
        $query = "INSERT INTO history (user, product) VALUES (:user, :product)";
        $insertStatement = Database::getConnection()->prepare($query);
        $insertStatement->execute(array("user" => $user, "product" => $product));
        return null;
    }
    $query = "UPDATE history SET id = id WHERE user = :user AND product = :product";
    $statement = Database::getConnection()->prepare($query);
    $statement->execute(array("user" => $user, "product" => $product));
    return null;
}

function getHistory($name)
{
    $query = "SELECT p.link, image, title, COALESCE(FLOOR(AVG(rating)), 0) AS rating, price, offers FROM products p " .
        "JOIN categories c ON p.link = c.link LEFT JOIN rating r ON p.link = r.link " .
        "LEFT JOIN history h ON p.link = h.product WHERE h.user = :username " .
        "GROUP BY p.link, image, title, price, offers, views";
    $statement = Database::getConnection()->prepare($query);
    $statement->execute(array("username" => $name));
    if ($statement->rowCount() == 0) {
        return null;
    }
    $products = array();
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $record = array
        (
            "link" => urlencode($row["link"]),
            "image" => urlencode($row["image"]),
            "title" => $row["title"],
            "rating" => $row["rating"],
            "price" => $row["price"],
            "offers" => $row["offers"]
        );
        array_push($products, $record);
    }
    return $products;
}
