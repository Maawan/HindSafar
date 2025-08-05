<?php

require __DIR__ . '/../../../Database/db.php';
require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../sms_helper.php';
use Twilio\Rest\Client;
require_once './backend/services/messages/sms_helper.php';
use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;
require_once './backend/services/emails/sendMail.php';
include_once './generate-ticket.php';

function confirmHotelBooking($booking_id){
    global $pdo;

    // ,  Amount
    $stmt = $pdo->prepare("SELECT
    h.name as hotel_name,
    h.address as hotel_address,
    h.city as hotel_city,
    b.check_in_date as checkin,
    b.check_out_date as checkout,
    b.rooms_booked as no_of_rooms,
    c.NAME as customer_name,
    c.CUSTOMER_ID as user_id,
    c.CONTACT_NUMBER as contact_number,
    c.email as email,
    p.razorpay_order_id as payment_reference,
    p.amount as amount_paid
    from hotels h, hotel_bookings b, customers c, payments p 
    where h.hotel_id = b.hotel_id AND b.user_id = c.CUSTOMER_ID AND b.payment_id = p.payment_id AND b.booking_id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();
    if(!$booking) return;
    $payment_ref = $booking['payment_reference'];
    $hotel_name = $booking['hotel_name'];
    $hotel_address = $booking['hotel_address'];
    $hotel_city = $booking['hotel_city'];
    $checkin_date = $booking['checkin'];
    $checkout_date = $booking['checkout'];
    $no_of_rooms = $booking['no_of_rooms'];
    $customer_name = $booking['customer_name'];
    $contact_number = $booking['contact_number'];
    $amount = $booking['amount_paid'];
    $email = $booking['email'];
    $user_id = $booking['user_id'];

    $receipt_link = "https://hindsafar.com/receipts/hotel/$booking_id"; // Replace with your actual receipt URL logic

    $message = "\n✅ Hotel Booking Confirmed – HindSafar\n";
    $message .= "Booking ID: " . $booking_id . "\n";
    $message .= "Hotel: " . $hotel_name . "\n";
    $message .= "Address: " . $hotel_address . ", " . $hotel_city . "\n";
    $message .= "Check-in: " . $checkin_date . "\n";
    $message .= "Check-out: " . $checkout_date . "\n";
    $message .= "Rooms Booked: " . $no_of_rooms . "\n";
    $message .= "Guest Name: " . $customer_name . "\n";
    $message .= "Contact: " . $contact_number . "\n";
    $message .= "Amount Paid: ₹" . $amount . "\n";
    $message .= "📄 Download Receipt: $receipt_link\n";
    $message .= "Thank you for booking with HindSafar!";

    $htmlBody = <<<EOD
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Hotel Booking Confirmation – HindSafar</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
    .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
    .header { background-color: #0369a1; color: white; padding: 20px; text-align: center; }
    .header img { max-height: 50px; margin-bottom: 10px; }
    .content { padding: 30px; color: #333333; }
    .content h1 { font-size: 22px; margin-top: 0; }
    .content p { font-size: 16px; line-height: 1.6; }
    .content .info-box { background: #f1f5f9; padding: 15px; border-radius: 6px; margin-top: 15px; }
    .footer { background-color: #e2e8f0; text-align: center; padding: 15px; font-size: 14px; color: #666666; }
    a.button { display: inline-block; margin-top: 20px; padding: 12px 24px; background-color: #0369a1; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="https://res.cloudinary.com/duklzb1ww/image/upload/v1754343126/logo_qcve8t.png" alt="HindSafar Logo" />
      <h2>Hotel Booking Confirmed</h2>
    </div>
    <div class="content">
      <h1>Hello {$customer_name},</h1>
      <p>We're pleased to inform you that your hotel booking has been <strong>confirmed</strong> and payment has been <strong>successfully received</strong>.</p>

      <div class="info-box">
        <p><strong>Booking ID:</strong> {$booking_id}</p>
        <p><strong>Payment Reference:</strong> {$payment_ref}</p>
        <p><strong>Hotel Name:</strong> {$hotel_name}</p>
        <p><strong>Hotel Address:</strong> {$hotel_address}, {$hotel_city}</p>
        <p><strong>Check-in Date:</strong> {$checkin_date}</p>
        <p><strong>Check-out Date:</strong> {$checkout_date}</p>
        <p><strong>Rooms Booked:</strong> {$no_of_rooms}</p>
        <p><strong>Guest Name:</strong> {$customer_name}</p>
        <p><strong>Contact Number:</strong> {$contact_number}</p>
        <p><strong>Amount Paid:</strong> ₹{$amount}</p>
      </div>

      <p>Your receipt is attached with this email. Please keep it for your records.</p>

      <p>If you have any questions or need to make changes, feel free to contact our support team.</p>

      <a href="https://hindsafar.com" class="button">Visit HindSafar</a>
    </div>
    <div class="footer">
      &copy; 2025 HindSafar. All rights reserved.<br/>
      Need help? <a href="mailto:support@hindsafar.com">support@hindsafar.com</a>
    </div>
  </div>
</body>
</html>
EOD;

    $pdfBinary = generateTicketPDF("hotel" , $booking_id , $user_id);
    // echo $pdfBinary;
    $file_name = "reciept_hotel_{$booking_id}.pdf";
    sendHotelMail($email , $htmlBody , $customer_name , $pdfBinary , $file_name);
    sendMessage($contact_number , $message);
}

function confirmFlightBooking($payment_id) {
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

    

    
    $stmt = $pdo->prepare("SELECT NAME,email, CONTACT_NUMBER from customers where CUSTOMER_ID = ?");
    $stmt->execute([$user_id]);
    $user_row = $stmt->fetch();
    if(!$user_row) return;
    $userName = $user_row['NAME'];
    $phone = $user_row['CONTACT_NUMBER'];
    $email = $user_row['email'];
    $message .= "Thank you " . $userName . " for choosing HindSafar.";
    
    $stmt = $pdo->prepare("Select razorpay_order_id from payments where payment_id = ?");
    $stmt->execute([$payment_id]);
    $payment_row = $stmt->fetch();
    $payment_reference = $payment_row['razorpay_order_id'];
    sendMessage($phone , $message);



    $htmlBody = <<<EOD
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Hotel Booking Confirmation – HindSafar</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
    .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
    .header { background-color: #0369a1; color: white; padding: 20px; text-align: center; }
    .header img { max-height: 50px; margin-bottom: 10px; }
    .content { padding: 30px; color: #333333; }
    .content h1 { font-size: 22px; margin-top: 0; }
    .content p { font-size: 16px; line-height: 1.6; }
    .content .info-box { background: #f1f5f9; padding: 15px; border-radius: 6px; margin-top: 15px; }
    .footer { background-color: #e2e8f0; text-align: center; padding: 15px; font-size: 14px; color: #666666; }
    a.button { display: inline-block; margin-top: 20px; padding: 12px 24px; background-color: #0369a1; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="https://res.cloudinary.com/duklzb1ww/image/upload/v1754343126/logo_qcve8t.png" alt="HindSafar Logo" />
      <h2>Flight Booking Confirmed</h2>
    </div>
    <div class="content">
      <h1>Hello {$userName},</h1>
      <p>We're pleased to inform you that your flight booking has been <strong>confirmed</strong> and payment has been <strong>successfully received</strong>.</p>

      <div class="info-box">
        <p><strong>Booking ID:</strong> {$booking_id}</p>
        <p><strong>Payment Reference:</strong> {$payment_reference}</p>
        <p><strong>Amount Paid:</strong> ₹{$amont_paid}</p>
      </div>

      <p>Your receipt is attached with this email. Please keep it for your records.</p>

      <p>If you have any questions or need to make changes, feel free to contact our support team.</p>

      <a href="https://hindsafar.com" class="button">Visit HindSafar</a>
    </div>
    <div class="footer">
      &copy; 2025 HindSafar. All rights reserved.<br/>
      Need help? <a href="mailto:support@hindsafar.com">support@hindsafar.com</a>
    </div>
  </div>
</body>
</html>
EOD;

    $pdfBinary = generateTicketPDF("flight" , $booking_id , $user_id);
    // echo $pdfBinary;
    $file_name = "reciept_hotel_{$booking_id}.pdf";
    sendHotelMail($email , $htmlBody , $userName , $pdfBinary , $file_name);

    

}

function confirmPackageBooking($name, $email, $payment_reference, $amount, $booking_id , $user_id){
    $htmlBody = <<<EOD
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Hotel Booking Confirmation – HindSafar</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
    .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
    .header { background-color: #0369a1; color: white; padding: 20px; text-align: center; }
    .header img { max-height: 50px; margin-bottom: 10px; }
    .content { padding: 30px; color: #333333; }
    .content h1 { font-size: 22px; margin-top: 0; }
    .content p { font-size: 16px; line-height: 1.6; }
    .content .info-box { background: #f1f5f9; padding: 15px; border-radius: 6px; margin-top: 15px; }
    .footer { background-color: #e2e8f0; text-align: center; padding: 15px; font-size: 14px; color: #666666; }
    a.button { display: inline-block; margin-top: 20px; padding: 12px 24px; background-color: #0369a1; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="https://res.cloudinary.com/duklzb1ww/image/upload/v1754343126/logo_qcve8t.png" alt="HindSafar Logo" />
      <h2>Travel Package Booking Confirmed</h2>
    </div>
    <div class="content">
      <h1>Hello {$name},</h1>
      <p>We're pleased to inform you that your Travel Package booking has been <strong>confirmed</strong> and payment has been <strong>successfully received</strong>.</p>

      <div class="info-box">
        <p><strong>Booking ID:</strong> {$booking_id}</p>
        <p><strong>Payment Reference:</strong> {$payment_reference}</p>
        <p><strong>Amount Paid:</strong> ₹{$amount}</p>
      </div>

      <p>Your receipt is attached with this email. Please keep it for your records.</p>

      <p>If you have any questions or need to make changes, feel free to contact our support team.</p>

      <a href="https://hindsafar.com" class="button">Visit HindSafar</a>
    </div>
    <div class="footer">
      &copy; 2025 HindSafar. All rights reserved.<br/>
      Need help? <a href="mailto:support@hindsafar.com">support@hindsafar.com</a>
    </div>
  </div>
</body>
</html>
EOD;

    // You can now send this HTML with PHPMailer or Mailtrap API.
    $pdfBinary = generateTicketPDF("package" , $booking_id , $user_id);
    // echo $pdfBinary;
    $file_name = "reciept_package_{$booking_id}.pdf";
    sendHotelMail($email , $htmlBody , $name , $pdfBinary , $file_name);
}

function sendMessage($phone , $message){
    sendSMS($phone , $message);
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
        // echo "Message sent! Message ID: " . $result['MessageId'] . "\n";
        // echo $phone;
    } catch (AwsException $e) {
        // echo "Error sending SMS: " . $e->getMessage() . "\n";
    }
}
// confirmFlightBooking(10,10);
?>
