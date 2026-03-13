<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
error_reporting(0);
ini_set('display_errors', 0);

require "./config/db.php";

$body = json_decode(file_get_contents("php://input"), true);
$id     = isset($body['id'])     ? intval($body['id'])               : 0;
$status = isset($body['status']) ? trim($body['status'])             : '';

$allowed = ['PENDING', 'IN_TRANSIT', 'DELIVERED', 'CANCELLED'];

if (!$id || !in_array(strtoupper($status), $allowed)) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$stmt = $conn->prepare("UPDATE delivery_requests SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $conn->error]);
}
