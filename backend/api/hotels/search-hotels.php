<?php
header('Content-Type: application/json');
session_start();
require '../../Database/db.php'; // adjust path to your DB connection

require('../../../razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Read and validate input
$data = json_decode(file_get_contents("php://input"), true);

$user_id = $_SESSION['user_id'] ?? null;
$city = $data['city'] ?? null;
$room_type = $data['room_type'] ?? null;
$checkin_date = $data['checkin_date'] ?? null;
$checkout_date = $data['checkout_date'] ?? null;
$no_of_rooms = $data['no_of_rooms'] ?? null;

// Check mandatory fields
// if (!$user_id) {
//     http_response_code(401);
//     echo json_encode(['success' => false, 'message' => 'User not authenticated']);
//     exit();
// }
if (!$city || !$room_type || !$checkin_date || !$checkout_date || !$no_of_rooms) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

// Validate date formats
if (!strtotime($checkin_date) || !strtotime($checkout_date)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
    exit();
}

// Validate date logic
if (strtotime($checkin_date) >= strtotime($checkout_date)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Check-out date must be after check-in date']);
    exit();
}

// Validate number of rooms
if (!is_numeric($no_of_rooms) || $no_of_rooms <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid number of rooms']);
    exit();
}

$result = array();

// Get hotels in the city
$hotel_query = "SELECT * FROM hotels WHERE city = ?";
$stmt = $conn->prepare($hotel_query);
$stmt->bind_param("s", $city);
$stmt->execute();
$hotel_res = $stmt->get_result();

while ($hotel = $hotel_res->fetch_assoc()) {
    $hotel_id = $hotel['hotel_id'];
    $hotel_name = $hotel['name'];
    $address = $hotel['address'];
    // echo $hotel_name . " " . $hotel_id;
    // Get room types for this hotel matching the given room type
    $roomtype_query = "";
    if($room_type == "all"){
        // echo " all ";
        $roomtype_query = "SELECT * FROM roomtype WHERE hotel_id = ?";
        $stmt2 = $conn->prepare($roomtype_query);
        $stmt2->bind_param("s", $hotel_id);

    }else{
        $roomtype_query = "SELECT * FROM roomtype WHERE hotel_id = ? AND type_name = ?";
        $stmt2 = $conn->prepare($roomtype_query);
        $stmt2->bind_param("is", $hotel_id , $room_type);
    }
    $stmt2->execute();
    $roomtype_res = $stmt2->get_result();
    
    while ($rtype = $roomtype_res->fetch_assoc()) {
        $room_type_id = $rtype['room_type_id'];
        $type_name = $rtype['type_name'];
        $price = $rtype['price'];
        // echo $type_name . "-> " . $room_type_id;
        // Check availability for all dates in range
        $date = $checkin_date;
        $available = true;

        while (strtotime($date) < strtotime($checkout_date)) {
            $avail_query = "SELECT available_rooms FROM hotel_availability WHERE room_type_id = ? AND date = ?";
            $stmt3 = $conn->prepare($avail_query);
            $stmt3->bind_param("ss", $room_type_id, $date);
            $stmt3->execute();
            $avail_res = $stmt3->get_result();

            if ($avail_res->num_rows > 0) {
                $avail_row = $avail_res->fetch_assoc();
                if ($avail_row['available_rooms'] < $no_of_rooms) {
                    $available = false;
                    break;
                }
            } else {
                $available = false;
                break;
            }
            // Insert into hotel_availability(hotel_id , room_type_id , date , available_rooms) values(1,1,2025-08-01 , 20);
            // Increment date by 1 day
            $date = date('Y-m-d', strtotime($date . ' +1 day'));
        }

        if ($available) {
            $result[] = array(
                "hotel_name" => $hotel_name,
                "room_type" => $type_name,
                "address" => $address,
                "price_per_night" => $price,
                "meta-data" => array("hotel_id" => $hotel_id , "room_type_id" => $room_type_id)
            );
        }
    }
}

// Final response
http_response_code(200);
echo json_encode([
    "success" => true,
    "message" => count($result) > 0 ? "Available rooms retrieved" : "No rooms available",
    "data" => $result
]);
$conn->close();
?>
