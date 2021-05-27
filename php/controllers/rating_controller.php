<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . "/../models/rating_model.php";

if (!isset($_SESSION["username"])) {
    http_response_code(403);
    echo json_encode(array("message" => "Nu esti autentificat."));
} else if (isset($_GET["name"]) && isset($_GET["stars"])) {
    $stars = $_GET["stars"];
    $message = updateRating($_SESSION["username"], $_GET["name"], $stars);
    if ($message != null) {
        http_response_code(400);
        echo json_encode(array("message" => $message));
    } else {
        http_response_code(200);
        if ($stars == 1) {
            echo json_encode(array("message" => "Ai acordat o stea."));
        } else {
            echo json_encode(array("message" => "Ai acordat " . $stars . " stele."));
        }
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Nu ai specificat toate informatiile necesare."));
}
