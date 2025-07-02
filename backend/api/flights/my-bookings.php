<?php
session_start();

require '../../Database/db.php';

// Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//   header("Location: login.html");
//   exit();
// }

// $name = $_SESSION['name'];


// Get user_id from GET parameter
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "user_id is required"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            flight_bookings.booking_id,
            flight_bookings.status AS booking_status,
            flight_bookings.total_fare AS amount,
            flight_bookings.created_at AS booking_date,
            flights.from_city,
            flights.to_city
        FROM flight_bookings
        JOIN flights ON flight_bookings.flight_id = flights.flight_id
        WHERE flight_bookings.user_id = ?
        ORDER BY booking_date DESC";
$sql2 = "SELECT 
    fb.booking_id,
    f.flight_status AS flight_status,
    p.payment_status AS payment_status,
    fb.total_fare AS amount,
    fb.created_at AS booking_date,
    f.from_city,
    f.to_city
FROM flight_bookings fb
JOIN flights f ON fb.flight_id = f.flight_id
LEFT JOIN payments p ON fb.payment_id = p.payment_id
WHERE fb.user_id = ?
ORDER BY booking_date DESC;";

$stmt = $conn->prepare($sql2);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = [
        "booking_id" => $row['booking_id'],
        "from" => $row['from_city'],
        "to" => $row['to_city'],
        "flight_status" => $row['flight_status'],
        "payment_status" => $row['payment_status'],
        "amount" => $row['amount'],
        "date_time" => $row['booking_date']
    ];
}

echo json_encode(["status" => "success", "bookings" => $bookings]);

$stmt->close();
$conn->close();
?>
