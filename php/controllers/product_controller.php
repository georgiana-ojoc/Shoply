<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . "/../models/product_model.php";

if (isset($_GET["name"])) {
    $name = $_GET["name"];
    if (isset($_GET["rating"])) {
        $information = getRating($name);
    } else if (isset($_GET["vendors"])) {
        $information = getVendors($name);
    } else if (isset($_GET["chart"])) {
        $information = getProductPrices($name);
    } else {
        $information = getProductInformation($name);
    }
    if ($information == null) {
        http_response_code(404);
        echo json_encode(array("message" => "Nu exista produsul."));
    } else {
        http_response_code(200);
        echo json_encode($information);
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Nu ai specificat denumirea produsului."));
}
