<?php
header('Content-Type: application/json');
session_start();
require '../../Database/db.php'; // adjust path to your DB connection

require('../../../razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $_SESSION['user_id'] ?? null;



if(!$user_id || !isset($data['hotel_id'] , $data['room_type_id'] , $data['checkin_date'] , $data['checkout_date'] , $data['no_of_rooms'])){
    echo json_encode([
        'success' => false,
        'msg' => 'Invalid fields'
    ]);
    exit();
}

$hotel_id = $data['hotel_id'];
$room_type_id = $data['room_type_id'];
$checkin_date = $data['checkin_date'];
$checkout_date = $data['checkout_date'];
$no_of_rooms = $data['no_of_rooms'];

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

if(!is_numeric($no_of_rooms) || $no_of_rooms <= 0){
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid number of rooms']);
    exit();
}

$room_query = "SELECT r.price, r.type_name, h.name as hotel_name, h.address 
               FROM roomtype r 
               JOIN hotels h ON r.hotel_id = h.hotel_id 
               WHERE r.hotel_id = ? AND r.room_type_id = ?";
$stmt = $conn->prepare($room_query);
$stmt->bind_param("ii", $hotel_id, $room_type_id);
$stmt->execute();
$room_res = $stmt->get_result();

if($room_res->num_rows == 0){
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Hotel or room type not found']);
    exit();
}

$room_data = $room_res->fetch_assoc();
$price_per_night = $room_data['price'];
$room_type_name = $room_data['type_name'];
$hotel_name = $room_data['hotel_name'];
$hotel_address = $room_data['address'];

$available = true;
$date = $checkin_date;

while(strtotime($date) < strtotime($checkout_date)){
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
if(!$available){
    http_response_code(200);
    echo json_encode(['sucess' => false,
    'message' => 'Rooms not available for selected dates']);
    exit();
}

$diffTime = abs(strtotime($checkout_date) - strtotime($checkin_date));
$diffDays = ceil($diffTime / (60*60*24));
$total_cost = $diffDays * $price_per_night * $no_of_rooms;
$nights = ceil($diffTime / (60*60*24));
// echo "Available";



try{
    $pdo->beginTransaction();



    $api_key = 'rzp_test_tN2HjlxfDwX4rW';
    $api_secret = '5wjPwOMcdSgsbG1dZTvQCqBq';
    $api = new Api($api_key , $api_secret);
        
    $order = $api->order->create([
        'amount' => $total_cost * 100,
        'currency' => 'INR',
    ]);

    $orderId = $order->id;
    // echo $orderId;
    $stmt = $pdo->prepare("INSERT INTO payments (amount , method , payment_status , razorpay_order_id) VALUES (? , 'Razorpay' , 'Pending' , ?)");
    $stmt->execute([$total_cost , $orderId]);

    $payment_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO hotel_bookings(user_id, hotel_id, room_type_id, check_in_date, check_out_date, rooms_booked, payment_id) VALUES (?,?,?,?,?,?,?)");

    $stmt->execute([$user_id,$hotel_id,$room_type_id, $checkin_date, $checkout_date, $no_of_rooms, $payment_id]);
    $booking_id = $pdo->lastInsertId();
    echo json_encode([
        'success' => true,
        'message' => 'Order Created',
        'booking_id' => $orderId,
        'payment_id' => intval($payment_id),
        'amount' => $total_cost
    ]);
    $pdo->commit();

}catch(Exception $e){
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Cannot create order: ' . $e->getMessage()]);
    exit();
}





// echo json_encode([
//     'success' => true,
//     'msg' => 'Rooms are available',
//     'total_price' => $total_cost,
//     'nights' => $nights,
//     'price_per_night' => intval($price_per_night)
// ]);


