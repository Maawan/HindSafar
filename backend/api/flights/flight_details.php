<?php
header('Content-Type: application/json');
session_start();
require '../../Database/db.php';

// Get raw JSON input
$data = json_decode(file_get_contents("php://input"), true);
$flightID = $data['flightID'] ?? '';


if (!$flightID) {
  echo json_encode(["error" => "Flight ID is required"]);
  exit;
}

// Query flight by ID
$stmt = $conn->prepare("SELECT * FROM flights WHERE flight_id = ?");
$stmt->bind_param("s", $flightID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo json_encode(["error" => "Flight not found"]);
} else {
  $flight = $result->fetch_assoc();
  echo json_encode($flight);
}

$stmt->close();
$conn->close();
?>
