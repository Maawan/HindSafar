<?php
header('Content-Type: application/json');

require '../../Database/db.php';

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing package ID']);
    exit;
}

$packageId = intval($_GET['id']);

// Prepare SQL (only active packages)
$sql = "SELECT * FROM custom_packages WHERE package_id = ? AND is_active = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $packageId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Package not found or inactive']);
    exit;
}

$package = $result->fetch_assoc();
echo json_encode($package);
