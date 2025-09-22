<?php
// receive_data.php

// Set response header to JSON
header('Content-Type: application/json');

// Read raw input
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Validate
if (!isset($data['name'], $data['email'], $data['company_name'], $data['message'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields."
    ]);
    exit;
}

$name = htmlspecialchars(trim($data['name']));
$email = htmlspecialchars(trim($data['email']));
$company = htmlspecialchars(trim($data['company_name']));
$message = htmlspecialchars(trim($data['message']));

// Example: Save to database (MySQLi)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed."
    ]);
    exit;
}

// Insert query (use prepared statement for safety)
$stmt = $conn->prepare("INSERT INTO inquiries (name, email, company_name, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $company, $message);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Data stored successfully."
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to insert data."
    ]);
}

$stmt->close();
$conn->close();
?>