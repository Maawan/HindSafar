<?php
header("Access-Control-Allow-Origin: *"); // Change * to frontend domain in production
header("Content-Type: application/json");
session_start();
require '../../Database/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$phone = $data['phone'] ?? '';
$password = $data['password'] ?? '';

if (!$phone || !$password) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Phone and password are required'
    ]);
    exit;
}

$stmt = $conn->prepare("SELECT CUSTOMER_ID, NAME, PASSWORD FROM customers WHERE CONTACT_NUMBER = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid phone or password'
    ]);
    exit;
}

$user = $result->fetch_assoc();

if (password_verify($password, $user['PASSWORD'])) {
    $_SESSION['user_id'] = $user['CUSTOMER_ID'];
    $_SESSION['name'] = $user['NAME'];
    $_SESSION['last_activity'] = time();

    echo json_encode([
        'status' => 'success',
        'message' => 'Login successful',
        'user' => [
            'id' => $user['CUSTOMER_ID'],
            'name' => $user['NAME']
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid phone or password'
    ]);
}

$stmt->close();
$conn->close();
