<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . "/../models/products_model.php";

if (isset($_GET["categories"])) {
    $categories = getCategories();
    if ($categories != null) {
        http_response_code(200);
        echo json_encode($categories);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Nu exista nicio categorie."));
    }
} else if (isset($_GET["popular"])) {
    $products = getPopularProducts();
    if ($products != null) {
        http_response_code(200);
        echo json_encode(array_slice($products, 0, 120), JSON_PRETTY_PRINT);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Nu exista niciun produs."));
    }
} else if (isset($_GET["random"])) {
    $products = getRandomProducts();
    if ($products != null) {
        http_response_code(200);
        echo json_encode(array_slice($products, 0, 120), JSON_PRETTY_PRINT);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Nu exista niciun produs."));
    }
} else if (isset($_GET["category"])) {
    $category = $_GET["category"];
    if (isset($_GET["sort-by"])) {
        $sortBy = $_GET["sort-by"];
        $products = getProductsByCategory($category, $sortBy);
    } else {
        $products = getProductsByCategory($category, "most-popular");
    }
    if ($products != null) {
        http_response_code(200);
        echo json_encode(array_slice($products, 0, 120), JSON_PRETTY_PRINT);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Nu exista produse in categoria " . $category . "."));
    }
} else if (isset($_GET["title"])) {
    $title = $_GET["title"];
    $products = getProductsByTitle($title);
    if ($products != null) {
        http_response_code(200);
        echo json_encode(array_slice($products, 0, 120), JSON_PRETTY_PRINT);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Nu exista niciun produs."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Nu ai specificat toate informatiile necesare."));
}
