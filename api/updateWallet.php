<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
error_reporting(0);
ini_set('display_errors', 0);

require "./config/db.php";

$body   = json_decode(file_get_contents("php://input"), true);
$action = isset($body['action']) ? $body['action'] : '';

// ── Update wallet status (ACTIVE / SUSPENDED) ──────────────────────────────
if ($action === 'updateStatus') {
    $id     = isset($body['id'])     ? intval($body['id'])   : 0;
    $status = isset($body['status']) ? trim($body['status']) : '';

    if (!$id || !in_array(strtoupper($status), ['ACTIVE', 'SUSPENDED'])) {
        echo json_encode(["success" => false, "message" => "Invalid input"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE wallets SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    echo json_encode(["success" => $stmt->execute()]);
    exit;
}

// ── Adjust balance (credit / debit) ────────────────────────────────────────
if ($action === 'adjustBalance') {
    $userId = isset($body['userId']) ? intval($body['userId']) : 0;
    $type   = isset($body['type'])   ? $body['type']           : ''; // 'credit' | 'debit'
    $amount = isset($body['amount']) ? floatval($body['amount']) : 0;

    if (!$userId || !in_array($type, ['credit', 'debit']) || $amount <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid input"]);
        exit;
    }

    $operator = $type === 'credit' ? '+' : '-';
    $stmt = $conn->prepare("UPDATE wallets SET balance = balance $operator ? WHERE userId = ?");
    $stmt->bind_param("di", $amount, $userId);
    echo json_encode(["success" => $stmt->execute()]);
    exit;
}

echo json_encode(["success" => false, "message" => "Unknown action"]);
