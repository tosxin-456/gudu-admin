<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require "./config/db.php";

$sql = "SELECT 
            id,
            userId,
            licenseNumber,
            expirationDate,
            frontDriversLicenseImg,
            profileImageLicense,
            isVerified,
            createdAt,
            updatedAt
        FROM driver_licenses
        ORDER BY createdAt DESC";

$result = $conn->query($sql);

$licenses = [];

while ($row = $result->fetch_assoc()) {
    $licenses[] = $row;
}

echo json_encode($licenses);
