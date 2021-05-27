<?php
require_once __DIR__ . "/../../database/database.php";

function getProductInformation($name)
{
    if (strpos($name, "_")) {
        $name = explode("_", $name)[1];
    }
    $name = str_replace("-", "", $name);
    $query = "SELECT p.link, title, characteristics, description, price, image, COALESCE(FLOOR(AVG(rating)), 0) AS rating, " .
        "COUNT(rating) AS ratings, offers, views FROM products p LEFT JOIN rating r ON p.link = r.link " .
        "WHERE REPLACE(REPLACE(p.link, '/', ''), '-', '') LIKE CONCAT('%', :name, '%') GROUP BY link, title, characteristics, " .
        "description, price, image, offers, views";
    $statement = Database::getConnection()->prepare($query);
    $statement->execute(array("name" => $name));
    if ($statement->rowCount() == 0) {
        return null;
    }
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    return array
    (
        "link" => urlencode($row["link"]),
        "title" => $row["title"],
        "characteristics" => str_replace("<b> ", "<b>", trim($row["characteristics"])),
        "description" => str_replace("\n ", "\n\t", preg_replace("/ +/", " ",
            str_replace("\xc2\xa0", " ", trim($row["description"])))),
        "price" => $row["price"],
        "image" => urlencode($row["image"]),
        "rating" => $row["rating"],
        "ratings" => $row["ratings"],
        "offers" => $row["offers"],
        "views" => $row["views"]
    );
}

function getRating($name)
{
    if (strpos($name, "_")) {
        $name = explode("_", $name)[1];
    }
    $name = str_replace("-", "", $name);
    $query = "SELECT COALESCE(FLOOR(AVG(rating)), 0) AS rating, COUNT(rating) AS ratings " .
        "FROM products p LEFT JOIN rating r ON p.link = r.link " .
        "WHERE REPLACE(REPLACE(link, '/', ''), '-', '') LIKE CONCAT('%', :name, '%')";
    $statement = Database::getConnection()->prepare($query);
    $statement->execute(array("name" => $name));
    if ($statement->rowCount() == 0) {
        return null;
    }
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    return array
    (
        "rating" => $row["rating"],
        "ratings" => $row["ratings"],
    );
}

function getVendors($name)
{
    if (strpos($name, "_")) {
        $name = explode("_", $name)[1];
    }
    $name = str_replace("-", "", $name);
    $query = "SELECT vendors FROM products WHERE REPLACE(REPLACE(link, '/', ''), '-', '') LIKE CONCAT('%', :name, '%')";
    $statement = Database::getConnection()->prepare($query);
    $statement->execute(array("name" => $name));
    if ($statement->rowCount() == 0) {
        return null;
    }
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    return array
    (
        "vendors" => $row["vendors"]
    );
}

function getProductPrices($name)
{
    if (strpos($name, "_")) {
        $name = explode("_", $name)[1];
    }
    $name = str_replace("-", "", $name);
    $query = "SELECT price, updated_at FROM product_log WHERE REPLACE(REPLACE(link, '/', ''), '-', '') " .
        "LIKE CONCAT('%', :name, '%')";
    $statement = Database::getConnection()->prepare($query);
    $statement->execute(array("name" => $name));
    if ($statement->rowCount() == 0) {
        return null;
    }
    $prices = array();
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $record = array
        (
            "price" => $row["price"],
            "date" => $row["updated_at"]
        );
        array_push($prices, $record);
    }
    return $prices;
}
