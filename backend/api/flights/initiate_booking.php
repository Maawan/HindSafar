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

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id || !isset($data['flight_id'], $data['passengers'], $data['totalAmount'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid requestt' , $user_id , $data['flight_id'] , $data['passengers'] , $data['totalAmount']]);
    exit();
}

$flight_id = $data['flight_id'];
$passengers = $data['passengers'];
$frontendTotal = floatval($data['totalAmount']);

try {
    $pdo->beginTransaction();

    // Fetch flight details
    $stmt = $pdo->prepare("SELECT * FROM flights WHERE flight_id = ?");
    $stmt->execute([$flight_id]);
    $flight = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$flight) {
        throw new Exception("Flight not found");
    }

    // Check seat availability (assumes flights table has seats_available)
    if ($flight['seats_available'] < count($passengers)) {
        throw new Exception("Not enough seats available for this booking");
    }

    // Calculate backend total securely
    $backendTotal = 0;
    foreach ($passengers as $p) {
        $base = $flight['base_fare'];
        $lugFree = $flight['luggage_free_weight'];
        $cabFree = $flight['cabin_free_weight'];
        $lugRate = $flight['luggage_extra_price'];
        $cabRate = $flight['cabin_extra_price'];

        $luggage = floatval($p['luggage']);
        $cabin = floatval($p['cabin']);

        $extraLuggageCost = max(0, $luggage - $lugFree) * $lugRate;
        $extraCabinCost = max(0, $cabin - $cabFree) * $cabRate;

        $backendTotal += $base + $extraLuggageCost + $extraCabinCost;
    }

    // Cross verify total with frontend amount (allowing minimal rounding diff)
    if (abs($backendTotal - $frontendTotal) > 1) {
        throw new Exception("Fare mismatch. Cannot create order.");
    }
    $backendTotal = ceil($backendTotal);

    $api_key = 'rzp_test_tN2HjlxfDwX4rW';
    $api_secret = '5wjPwOMcdSgsbG1dZTvQCqBq';
    $api = new Api($api_key , $api_secret);
    
    $order = $api->order->create([
        'amount' => $backendTotal * 100,
        'currency' => 'INR',
    ]);

    $orderId = $order->id;

    // Insert payment record (status = pending)
    $stmt = $pdo->prepare("INSERT INTO payments (amount, method, payment_status , razorpay_order_id) VALUES (?, 'Razorpay', 'Pending' , ?)");
    $stmt->execute([$backendTotal , $orderId]);
    $payment_id = $pdo->lastInsertId();

    // Insert booking record (status = pending payment)
    $stmt = $pdo->prepare("INSERT INTO flight_bookings (user_id, flight_id, payment_id, total_fare, status) VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->execute([$user_id, $flight_id, $payment_id, $backendTotal]);
    $booking_id = $pdo->lastInsertId();

    // Insert passengers records
    $stmt = $pdo->prepare("INSERT INTO flight_passengers (booking_id, passenger_name, age, gender, cabin_weight, luggage_weight) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($passengers as $p) {
        $stmt->execute([
            $booking_id,
            $p['name'],
            intval($p['age']),
            substr(strtoupper($p['gender']), 0, 1), // M/F
            floatval($p['cabin']),
            floatval($p['luggage'])
        ]);
    }

    // Update flight seats_available
    $stmt = $pdo->prepare("UPDATE flights SET seats_available = seats_available - ? WHERE flight_id = ?");
    $stmt->execute([count($passengers), $flight_id]);

    
    


    echo json_encode([
        'success' => true,
        'message' => 'Order created',
        'booking_id' => $booking_id,
        'payment_id' => $orderId,
        'amount' => $backendTotal
    ]);

    $pdo->commit();

    

    

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Cannot create order: ' . $e->getMessage()]);
    exit();
}
