<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require "./config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"] ?? null;

if (!$id) {
    echo json_encode(["success" => false]);
    exit;
}

$sql = "DELETE FROM users WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}
