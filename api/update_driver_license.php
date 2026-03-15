<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

error_reporting(0);
ini_set('display_errors', 0);

require "./config/db.php";

$body = json_decode(file_get_contents("php://input"), true);

$id = isset($body['id']) ? intval($body['id']) : 0;
$isVerified = isset($body['isVerified']) ? intval($body['isVerified']) : null;

if (!$id || !in_array($isVerified, [0, 1])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid input"
    ]);
    exit;
}

$stmt = $conn->prepare("UPDATE driver_licenses SET isVerified = ? WHERE id = ?");
$stmt->bind_param("ii", $isVerified, $id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Driver license updated"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => $conn->error
    ]);
}
