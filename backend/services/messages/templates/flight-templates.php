<?php

require __DIR__ . '/../../../Database/db.php';
require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../sms_helper.php';
use Twilio\Rest\Client;

use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;

function confirmFlightBooking($payment_id , $s) {
    global $pdo; // Ensure PDO is accessible within function

    // Fetch booking
    $stmt = $pdo->prepare("SELECT booking_id, user_id, flight_id,total_fare FROM flight_bookings WHERE payment_id = ?");
    $stmt->execute([$payment_id]);
    $booking_row = $stmt->fetch();

    if (!$booking_row) return;

    $booking_id = $booking_row['booking_id'];
    $flight_id = $booking_row['flight_id'];
    $user_id = $booking_row['user_id'];
    $amont_paid = $booking_row['total_fare'];

    // Fetch flight details
    $stmt = $pdo->prepare("SELECT * FROM flights WHERE flight_id = ?");
    $stmt->execute([$flight_id]);
    $flight_row = $stmt->fetch();

    if (!$flight_row) return;

    $from = $flight_row['from_city'];
    $to = $flight_row['to_city'];
    $dep_time = $flight_row['dep_time'];
    $arr_time = $flight_row['arrival_time'];
    $flight_no = $flight_row['flight_id'];

    // Construct message
    $message = "\nBooking Confirmed!\n";
    $message .= "Booking ID: " . $booking_id . "\n";
    $message .= "Payment ID: " . $payment_id . "\n";
    $message .= "Flight No: " . $flight_no . "\n";
    $message .= "From: " . $from . "\n";
    $message .= "To: " . $to . "\n";
    $message .= "Departure: " . $dep_time . "\n";
    $message .= "Arrival: " . $arr_time . "\n";
    $message .= "Total Amount Paid: " . $amont_paid . "\n";

    // // Fetch passengers for this booking
    // $stmt = $pdo->prepare("SELECT name, type FROM passengers WHERE booking_id = ?");
    // $stmt->execute([$booking_id]);
    // $passengers = $stmt->fetchAll();

    // $message .= "Passengers:\n";
    // foreach ($passengers as $index => $passenger) {
    //     $message .= ($index+1) . ". " . $passenger['name'] . " (" . $passenger['type'] . ")\n";
    // }

    

    
    $stmt = $pdo->prepare("SELECT NAME, CONTACT_NUMBER from customers where CUSTOMER_ID = ?");
    $stmt->execute([$user_id]);
    $user_row = $stmt->fetch();
    if(!$user_row) return;
    $userName = $user_row['NAME'];
    $phone = $user_row['CONTACT_NUMBER'];
    $message .= "Thank you " . $userName . " for choosing HindSafar.";
    //sendSms($phone, $message);

    $SnSclient = new SnsClient([
        'region'  => 'ap-south-1', // Choose your region that supports SMS, e.g., ap-south-1 (Mumbai)
        'version' => 'latest',
        'credentials' => [
            'key'    => 'AKIAVIS2447YSOGUV7TM',
            'secret' => 'zLpnoEgrWex7IGYRbhNXjUCCJt78DuAX6QYFT1Ht',
        ],
        'suppress_php_deprecation_warning' => true
    ]);
    try {
        $result = $SnSclient->publish([
            'Message' => $message,
            'PhoneNumber' => str_replace("+", "", $phone),
        ]);
        //echo "Message sent! Message ID: " . $result['MessageId'] . "\n";
    } catch (AwsException $e) {
        //echo "Error sending SMS: " . $e->getMessage() . "\n";
    }

}
// confirmFlightBooking(10,10);
?>
