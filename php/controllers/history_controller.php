<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . "/../models/history_model.php";

if (!isset($_SESSION["username"])) {
    http_response_code(403);
    echo json_encode(array("message" => "Nu esti autentificat."));
} else if (isset($_GET["history"])) {
    $products = getHistory($_SESSION["username"]);
    if ($products != null) {
        http_response_code(200);
        echo json_encode($products);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Nu exista niciun produs."));
    }
} else if (isset($_GET["name"])) {
    $name = $_GET["name"];
    updateViews($name);
    $message = updateHistory($_SESSION["username"], $_GET["name"]);
    if ($message != null) {
        http_response_code(202);
        echo json_encode(array("message" => $message));
    } else {
        http_response_code(200);
        echo json_encode(array("message" => "Ai vizualizat produsul specificat."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Nu ai specificat toate informatiile necesare."));
}
