<?php
header('Content-Type: application/json');
session_start();

require '../../Database/db.php'; // adjust as per your folder structure

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Read and validate input
$data = json_decode(file_get_contents("php://input"), true);

$hotel_id = $data['hotel_id'] ?? null;
$room_type_id = $data['room_type_id'] ?? null;
$checkin_date = $data['checkin_date'] ?? null;
$checkout_date = $data['checkout_date'] ?? null;
$no_of_rooms = $data['no_of_rooms'] ?? null;

// Validate mandatory fields
if (!$hotel_id || !$room_type_id || !$checkin_date || !$checkout_date || !$no_of_rooms) {
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

// Check if hotel and room type exist and fetch price
$room_query = "SELECT r.price, r.type_name, h.name as hotel_name, h.address 
               FROM roomtype r 
               JOIN hotels h ON r.hotel_id = h.hotel_id 
               WHERE r.hotel_id = ? AND r.room_type_id = ?";
$stmt = $conn->prepare($room_query);
$stmt->bind_param("ii", $hotel_id, $room_type_id);
$stmt->execute();
$room_res = $stmt->get_result();

if ($room_res->num_rows == 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Hotel or room type not found']);
    exit();
}

$room_data = $room_res->fetch_assoc();
$price_per_night = $room_data['price'];
$room_type_name = $room_data['type_name'];
$hotel_name = $room_data['hotel_name'];
$hotel_address = $room_data['address'];

// Check availability for all dates in range
$available = true;
$date = $checkin_date;
// echo $hotel_name;
while (strtotime($date) < strtotime($checkout_date)) {
    $avail_query = "SELECT available_rooms FROM hotel_availability WHERE  room_type_id = ? AND date = ?";
    $stmt2 = $conn->prepare($avail_query);
    $stmt2->bind_param("ss", $room_type_id, $date);
    $stmt2->execute();
    $avail_res = $stmt2->get_result();

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

    // Increment date
    $date = date('Y-m-d', strtotime($date . ' +1 day'));
}

if (!$available) {
    http_response_code(200);
    echo json_encode(['success' => false, 'message' => 'Rooms not available for selected dates']);
    exit();
}

// Calculate total cost
$diffTime = abs(strtotime($checkout_date) - strtotime($checkin_date));
$diffDays = ceil($diffTime / (60*60*24));
$total_cost = $diffDays * $price_per_night * $no_of_rooms;

// Final response
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Rooms available',
    'data' => [
        'hotel_id' => $hotel_id,
        'hotel_name' => $hotel_name,
        'hotel_address' => $hotel_address,
        'room_type_id' => $room_type_id,
        'room_type_name' => $room_type_name,
        'checkin_date' => $checkin_date,
        'checkout_date' => $checkout_date,
        'no_of_rooms' => (int)$no_of_rooms,
        'price_per_night' => (float)$price_per_night,
        'total_nights' => $diffDays,
        'total_cost' => (float)$total_cost
    ]
]);
$conn->close();
?>
