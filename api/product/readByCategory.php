<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../../database/database.php";
include_once "../objects/product.php";

require_once "../../jwt/jwt_parameters.php";
require_once "../../jwt/src/JWT.php";
require_once "../../jwt/src/BeforeValidException.php";
require_once "../../jwt/src/ExpiredException.php";
require_once "../../jwt/src/SignatureInvalidException.php";
require_once "../../database/database.php";
require_once "../objects/product.php";

use Firebase\JWT\JWT;

$data = json_decode(file_get_contents("php://input"));
if (empty($data->jwt)) {
    http_response_code(401);
    print(json_encode(array("message" => "Nu ai autorizatie.")));
    exit();
}
try {
    $decoded_jwt = JWT::decode($data->jwt, JWT_KEY, array("HS256"));
    $user = $decoded_jwt->data;
} catch (Exception $exception) {
    http_response_code(401);
    echo json_encode(array("message" => $exception->getMessage()));
    exit();
}
if ($user->username != "admin") {
    http_response_code(401);
    print(json_encode(array("message" => "Nu ai drepturi de administrator.")));
    exit();
}
$database = new Database();
$connection = $database->getConnection();
$product = new Product($connection);
$category = isset($_GET["category"]) ? $_GET["category"] : die();
$product->setCategory($category);
$query = $product->readByCategory();
$productsNumber = $query->rowCount();
if ($productsNumber > 0) {
    $products = array("message" => $productsNumber . " products");
    $products["products"] = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $record = array
        (
            "id" => $row["id"],
            "link" => $row["link"],
            "title" => $row["title"],
            "characteristics" => $row["characteristics"],
            "description" => html_entity_decode($row["description"]),
            "price" => $row["price"],
            "currency" => $row["currency"],
            "offers" => $row["offers"],
            "image" => $row["image"],
            "vendors" => $row["vendors"],
            "views" => $row["views"]
        );
        array_push($products["products"], $record);
    }
    http_response_code(200);
    print(json_encode($products));
} else {
    http_response_code(404);
    print(json_encode(array("message" => "Nu exista produse in categoria " . $category . ".")));
}
