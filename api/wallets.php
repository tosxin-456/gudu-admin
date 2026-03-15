<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

error_reporting(0);
ini_set('display_errors', 0);

require "./config/db.php";

if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// First, get all users
$sqlUsers = "SELECT 
                u.id,
                u.fullName,
                u.email,
                u.phoneNumber,
                u.profilePicture,
                u.location,
                u.accountType,
                u.dob,
                u.gender,
                u.verified,
                u.onboardingCompleted,
                u.fcmToken,
                u.createdAt,
                u.updatedAt,
                dl.id AS driverLicenseId,
                bd.id AS bankDetailsId,
                pl.id AS primaryLocationId,
                v.id AS vehicleId
             FROM users u
             LEFT JOIN driver_licenses dl ON dl.userId = u.id
             LEFT JOIN bank_details bd ON bd.userId = u.id
             LEFT JOIN primary_locations pl ON pl.userId = u.id
             LEFT JOIN vehicles v ON v.userId = u.id
             ORDER BY u.createdAt DESC";

$resultUsers = $conn->query($sqlUsers);

if (!$resultUsers) {
    echo json_encode([
        "error" => "Query failed",
        "details" => $conn->error
    ]);
    exit;
}

$data = [];

while ($user = $resultUsers->fetch_assoc()) {
    $userId = $user['id'];

    // Determine if user is a driver
    $isDriver = $user['driverLicenseId'] && $user['bankDetailsId'] && $user['primaryLocationId'] && $user['vehicleId'] ? true : false;

    // Get wallet info
    $walletSql = "SELECT id, balance, currency, status, createdAt FROM wallets WHERE userId = $userId LIMIT 1";
    $walletResult = $conn->query($walletSql);
    $wallet = $walletResult && $walletResult->num_rows ? $walletResult->fetch_assoc() : null;

    // Get transactions
    $txnSql = "SELECT id, type, amount, reference, description, status, createdAt 
               FROM wallet_transactions 
               WHERE userId = $userId 
               ORDER BY createdAt DESC";
    $txnResult = $conn->query($txnSql);

    $transactions = [];
    if ($txnResult && $txnResult->num_rows) {
        while ($txn = $txnResult->fetch_assoc()) {
            $transactions[] = $txn;
        }
    }

    $data[] = [
        "id" => $user['id'],
        "fullName" => $user['fullName'],
        "email" => $user['email'],
        "phoneNumber" => $user['phoneNumber'],
        "profilePicture" => $user['profilePicture'],
        "location" => $user['location'],
        "accountType" => $user['accountType'],
        "dob" => $user['dob'],
        "gender" => $user['gender'],
        "verified" => (bool)$user['verified'],
        "onboardingCompleted" => (bool)$user['onboardingCompleted'],
        "fcmToken" => $user['fcmToken'],
        "createdAt" => $user['createdAt'],
        "updatedAt" => $user['updatedAt'],
        "isDriver" => $isDriver,
        "wallet" => $wallet,
        "transactions" => $transactions
    ];
}

echo json_encode($data);
