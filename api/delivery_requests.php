<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require "./config/db.php";

$sql = "SELECT 
            dr.id,
            dr.rideType,
            dr.packageId,
            dr.senderName,
            dr.senderPhoneNumber,
            dr.recipientName,
            dr.recipientPhoneNumber,
            dr.vehicleType,
            dr.packageSize,
            dr.packageImageUrl,
            dr.customerOfferAmount,
            dr.finalAmount,
            dr.status,
            dr.pickupDateTime,
            dr.createdAt,

            dr.selectedDriverId,
            u.fullName AS driverName,
            u.phoneNumber AS driverPhoneNumber,
            u.profilePicture AS driverProfilePicture

        FROM delivery_requests dr

        LEFT JOIN users u 
        ON dr.selectedDriverId = u.id

        ORDER BY dr.createdAt DESC";

$result = $conn->query($sql);

$requests = [];

while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

echo json_encode($requests);
