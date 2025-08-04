<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require '../../Database/db.php';
session_start();

$data = json_decode(file_get_contents("php://input"), true);
require_once '../../services/emails/sendMail.php';
$phone = $data['phone'] ?? '';
$password = $data['password'] ?? '';
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';

if (!$name || !$phone || !$password || !$email) {
    echo json_encode([
        'status' => 'error',
        'message' => 'All fields are required'
    ]);
    exit;
}

// Check if phone number already exists
$checkStmt = $conn->prepare("SELECT CUSTOMER_ID FROM customers WHERE CONTACT_NUMBER = ?");
$checkStmt->bind_param("s", $phone);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Phone number already registered'
    ]);
    $checkStmt->close();
    exit;
}
$checkStmt->close();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$dateCreated = date('Y-m-d');
$timeCreated = date('H:i:s');

$stmt = $conn->prepare("INSERT INTO customers (NAME, CONTACT_NUMBER, PASSWORD, DATE_CREATED, DAY_TIME , email) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $name, $phone, $hashedPassword, $dateCreated, $timeCreated, $email);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id; // get the auto-incremented CUSTOMER_ID

    $_SESSION['user_id'] = $user_id;
    $_SESSION['name'] = $name;
    $_SESSION['last_activity'] = time();
    sendWelcomeMail($email , $name);
    echo json_encode([
        'status' => 'success',
        'message' => 'Signup successful',
        'user' => [
            'id' => $user_id,
            'name' => $name
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Signup failed. Try again.'
    ]);
}

$stmt->close();
$conn->close();
