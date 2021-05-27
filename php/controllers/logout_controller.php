<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if (isset($_GET["logout"])) {
    if (!isset($_SESSION["username"])) {
        http_response_code(403);
        echo json_encode(array("message" => "Nu esti autentificat."));
    } else {
        session_unset();
        http_response_code(200);
        echo json_encode(array("message" => "Te-ai deconectat."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Nu ai specificat toate informatiile necesare."));
}