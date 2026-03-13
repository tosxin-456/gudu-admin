<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");
error_reporting(0);
ini_set('display_errors', 0);

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require "./config/db.php";

$data     = json_decode(file_get_contents("php://input"), true);
$email    = isset($data['email'])    ? trim($data['email'])    : '';
$password = isset($data['password']) ? trim($data['password']) : '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(["message" => "Email and password are required."]);
    exit;
}

// Use prepared statement — never interpolate user input into SQL
$stmt = $conn->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode(["message" => "Invalid email or password."]);
    exit;
}

$admin = $result->fetch_assoc();

if (!password_verify($password, $admin['passwordHash'])) {
    http_response_code(401);
    echo json_encode(["message" => "Invalid email or password."]);
    exit;
}

// Don't send passwordHash to the client
unset($admin['passwordHash']);

http_response_code(200);
echo json_encode([
    "message" => "Login successful",
    "admin"   => $admin
]);
