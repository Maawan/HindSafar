<?php
header("Content-Type: application/json");
require '../../Database/db.php';


if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["error" => "Connection failed"]);
  exit();
}

$sql = "SELECT * FROM custom_packages ORDER BY category";
$result = $conn->query($sql);

$grouped = [];

while ($row = $result->fetch_assoc()) {
  $cat = isset($row['category']) && $row['category'] !== '' ? $row['category'] : 'uncategorized';
  $grouped[$cat][] = $row;
}

echo json_encode($grouped, JSON_PRETTY_PRINT);
?>