<?php
session_start();

require '../Database/db.php';

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
    f.to_city,
    p.razorpay_order_id AS order_id
FROM flight_bookings fb
JOIN flights f ON fb.flight_id = f.flight_id
LEFT JOIN payments p ON fb.payment_id = p.payment_id
WHERE fb.user_id = ?
ORDER BY booking_date DESC;";

$stmt = $conn->prepare($sql2);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$flight_bookings = [];
while ($row = $result->fetch_assoc()) {
    $flight_bookings[] = [
        "booking_id" => $row['booking_id'],
        "from" => $row['from_city'],
        "to" => $row['to_city'],
        "flight_status" => $row['flight_status'],
        "payment_status" => $row['payment_status'],
        "amount" => $row['amount'],
        "order_id" => $row['order_id'],
        "date_time" => $row['booking_date'],
        "booking_type" => "flight"

    ];
}

$sql3 = "SELECT 
hb.booking_id as booking_id,
h.name as hotel_name,
h.address as hotel_address,
h.city as hotel_city,
rt.type_name as Room_Type,
hb.check_in_date as checkin_date,
hb.check_out_date as checkout_date,
p.razorpay_order_id as razorpay_id,
p.amount as amount,
p.payment_status as payment_status,
hb.created_at as orderDateAndTime
from hotels h, hotel_bookings hb , roomtype rt, payments p
where hb.hotel_id = h.hotel_id AND rt.room_type_id = hb.room_type_id AND
p.payment_id = hb.payment_id AND hb.user_id = ? order by hb.created_at desc";

$stmt = $conn->prepare($sql3);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$hotel_bookings = [];
while ($row = $result->fetch_assoc()) {
    $hotel_bookings[] = [
        "booking_id" => $row['booking_id'],
        "checkin_date" => $row['checkin_date'],
        "checkout_date" => $row['checkout_date'],
        "Hotel_Name" => $row['hotel_name'],
        "Hotel_Address" => $row['hotel_address'],
        "Hotel_City" => $row['hotel_city'],
        "Room_Type" => $row['Room_Type'],
        "amount" => $row['amount'],
        "order_id" => $row['razorpay_id'],
        "payment_status" => $row['payment_status'],
        "date_time" => $row['orderDateAndTime'],
        "booking_type" => 'hotel'

    ];
}


echo json_encode(["status" => "success", "flight_bookings" => $flight_bookings , "hotel_bookings"=>$hotel_bookings]);

$stmt->close();
$conn->close();
?>
