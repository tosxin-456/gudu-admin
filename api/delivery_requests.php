<?php

header("Content-Type: application/json");
require "./config/db.php";

$sql = "SELECT 
            id,
            rideType,
            packageId,
            senderName,
            senderPhoneNumber,
            recipientName,
            recipientPhoneNumber,
            vehicleType,
            packageSize,
            customerOfferAmount,
            finalAmount,
            status,
            pickupDateTime,
            createdAt
        FROM delivery_requests
        ORDER BY createdAt DESC";

$result = $conn->query($sql);

$requests = [];

while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

echo json_encode($requests);
