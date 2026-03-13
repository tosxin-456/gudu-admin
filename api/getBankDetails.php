<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require "./config/db.php";

$sql = "SELECT 
            id,
            userId,
            bankName,
            accountNumber,
            bvn,
            isVerified,
            createdAt
        FROM bank_details
        ORDER BY createdAt DESC";

$result = $conn->query($sql);

$banks = [];

while ($row = $result->fetch_assoc()) {
    $banks[] = $row;
}

echo json_encode($banks);
