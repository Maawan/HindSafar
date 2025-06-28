<?php
header('Content-Type: application/json');
$raw = json_decode(file_get_contents("php://input"), true);
session_start();
require '../../Database/db.php';
$from = $raw['from'] ?? '';
$to = $raw['to'] ?? '';
$date = $raw['date'] ?? '';
$passengers = intval($raw['passengers'] ?? 1);

if (!$from || !$to || !$date || $passengers <= 0) {
  echo json_encode([]);
  exit;
}

$stmt = $conn->prepare("SELECT * FROM flights
  WHERE LOWER(from_city) = LOWER(?) AND LOWER(to_city) = LOWER(?) 
    AND DATE(dep_time) = ? AND seats_available >= ?");

$stmt->bind_param("sssi", $from, $to, $date, $passengers);
$stmt->execute();
$result = $stmt->get_result();

$flights = [];
while ($row = $result->fetch_assoc()) {
  $flights[] = $row;
}

echo json_encode($flights);
?>
