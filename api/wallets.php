<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require "./config/db.php";

$sql = "SELECT 
            id,
            userId,
            balance,
            currency,
            status,
            createdAt
        FROM wallets
        ORDER BY createdAt DESC";

$result = $conn->query($sql);

$wallets = [];

while ($row = $result->fetch_assoc()) {
    $wallets[] = $row;
}

echo json_encode($wallets);
