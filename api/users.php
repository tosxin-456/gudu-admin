<?php
header("Content-Type: application/json");
error_reporting(0); // Suppress HTML errors from leaking into JSON
ini_set('display_errors', 0);

require "./config/db.php";

if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$sql = "SELECT id, fullName, email, phoneNumber, verified, accountType
        FROM users
        ORDER BY createdAt DESC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
