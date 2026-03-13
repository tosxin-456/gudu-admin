<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require "./config/db.php";

$sql = "SELECT 
            id,
            userId,
            make,
            model,
            color,
            capacity,
            plateNumber,
            frontPicture,
            isVerified,
            createdAt
        FROM vehicles
        ORDER BY createdAt DESC";

$result = $conn->query($sql);

$vehicles = [];

while ($row = $result->fetch_assoc()) {
    $vehicles[] = $row;
}

echo json_encode($vehicles);
