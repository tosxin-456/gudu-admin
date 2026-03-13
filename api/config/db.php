<?php

$host = "localhost";
$user = "root";
$password = "";
$dbname = "gudu";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]));
}

header("Content-Type: application/json");
