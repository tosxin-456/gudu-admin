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

// Fetch users along with driver-related tables and vehicle info
$sql = "SELECT 
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
            
            -- Check if driver-related tables have records
            dl.id AS driverLicenseId,
            bd.id AS bankDetailsId,
            pl.id AS primaryLocationId,
            
            -- Vehicle details
            v.id AS vehicleId,
            v.make AS vehicleMake,
            v.model AS vehicleModel,
            v.color AS vehicleColor,
            v.capacity AS vehicleCapacity,
            v.plateNumber AS vehiclePlateNumber,
            v.frontPicture AS vehicleFrontPicture,
            v.isVerified AS vehicleIsVerified

        FROM users u
        LEFT JOIN driver_licenses dl ON dl.userId = u.id
        LEFT JOIN bank_details bd ON bd.userId = u.id
        LEFT JOIN primary_locations pl ON pl.userId = u.id
        LEFT JOIN vehicles v ON v.userId = u.id
        ORDER BY u.createdAt DESC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "error" => "Query failed",
        "details" => $conn->error
    ]);
    exit;
}

$data = [];

while ($row = $result->fetch_assoc()) {
    // Determine if user is a driver (has all four records)
    $isDriver = $row['driverLicenseId'] && $row['bankDetailsId'] && $row['primaryLocationId'] && $row['vehicleId'] ? true : false;

    // Prepare vehicle object if exists
    $vehicle = null;
    if ($row['vehicleId']) {
        $vehicle = [
            "id" => $row['vehicleId'],
            "make" => $row['vehicleMake'],
            "model" => $row['vehicleModel'],
            "color" => $row['vehicleColor'],
            "capacity" => $row['vehicleCapacity'],
            "plateNumber" => $row['vehiclePlateNumber'],
            "frontPicture" => $row['vehicleFrontPicture'],
            "isVerified" => (bool)$row['vehicleIsVerified']
        ];
    }

    $data[] = [
        "id" => $row['id'],
        "fullName" => $row['fullName'],
        "email" => $row['email'],
        "phoneNumber" => $row['phoneNumber'],
        "profilePicture" => $row['profilePicture'],
        "location" => $row['location'],
        "accountType" => $row['accountType'],
        "dob" => $row['dob'],
        "gender" => $row['gender'],
        "verified" => (bool)$row['verified'],
        "onboardingCompleted" => (bool)$row['onboardingCompleted'],
        "fcmToken" => $row['fcmToken'],
        "createdAt" => $row['createdAt'],
        "updatedAt" => $row['updatedAt'],
        "isDriver" => $isDriver,
        "vehicle" => $vehicle
    ];
}

echo json_encode($data);
