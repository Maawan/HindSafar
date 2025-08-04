<?php
session_start();
require '../../Database/db.php'; // ← Make sure this file contains your DB connection ($conn)
require('../../../razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "User not logged in..."]);
    exit();
}
$data = json_decode(file_get_contents("php://input"), true);
// Check required fields
// echo $_POST['package_id'];
if (!isset($data['package_id']) || !isset($data['start_date'])) {
    http_response_code(400); // Bad request
    echo json_encode(["error" => "Missing required fields."]);
    exit();
}

$package_id = intval($data['package_id']);
$start_date = $data['start_date'];
$user_id = $_SESSION['user_id'];

// Optional: validate date format
if (!DateTime::createFromFormat('Y-m-d', $start_date)) {
    http_response_code(422); // Unprocessable Entity
    echo json_encode(["error" => "Invalid date format. Use YYYY-MM-DD."]);
    exit();
}
try{
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("SELECT * from custom_packages where package_id = ?");
    $stmt->execute(([$package_id]));

    $package = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$package){
        throw new Exception("Invalid Package ID");
    }
    $totalAmount = $package['price'];
    // echo "Amount is " . $totalAmount;
    $api_key = 'rzp_test_tN2HjlxfDwX4rW';
    $api_secret = '5wjPwOMcdSgsbG1dZTvQCqBq';
    $api = new Api($api_key , $api_secret);
    
    $order = $api->order->create([
        'amount' => $totalAmount * 100,
        'currency' => 'INR',
    ]);
    $orderId = $order->id;
    $stmt = $pdo->prepare("INSERT INTO payments (amount, method, payment_status , razorpay_order_id) VALUES (?, 'Razorpay', 'Pending' , ?)");
    $stmt->execute([$totalAmount , $orderId]);
    $payment_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO custom_packages_bookings(package_id,startDate,payment_id,amount,user_id) VALUES(?,?,?,?,?)");


    $stmt->execute([$package_id , $start_date , $payment_id , $totalAmount , $user_id]);
    $booking_id = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Order created',
        'booking_id' => $booking_id,
        'payment_id' => $orderId,
        'amount' => $totalAmount
    ]);
    $pdo->commit();

}catch(Exception $e){
    echo "error";
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Cannot create order: ' . $e->getMessage()]);
    exit();
}




?>
