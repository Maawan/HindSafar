<?php
require 'vendor/autoload.php'; // Dompdf
include './backend/Database/db.php'; // your DB connection

use Dompdf\Dompdf;
session_start();
$booking_id = $_GET['booking_id'] ?? null;
if(!isset($_SESSION['user_id'])){
    die("Gaand marao ");
}
//echo "User Id is " . $_SESSION['user_id'];
if(!$booking_id){
    die("No booking ID provided.");
}

// Fetch booking details
$stmt = $pdo->prepare("SELECT * FROM flight_bookings WHERE booking_id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

if(!$booking){
    die("Booking not found.");
}

// Fetch flight details
$stmt = $pdo->prepare("SELECT * FROM flights WHERE flight_id = ?");
$stmt->execute([$booking['flight_id']]);
$flight = $stmt->fetch();

// Fetch passenger(s) details
$stmt = $pdo->prepare("SELECT * FROM flight_passengers WHERE booking_id = ?");
$stmt->execute([$booking_id]);
$passengers = $stmt->fetchAll();

// Fetch payment details
$stmt = $pdo->prepare("SELECT * FROM payments WHERE payment_id = ?");
$stmt->execute([$booking['payment_id']]);
$payment = $stmt->fetch();

if($payment['payment_status'] !== 'Completed' || !isset($_SESSION['user_id']) || $_SESSION['user_id'] !== $booking['user_id']){
    die("");
}
// echo $payment['payment_status'] . !isset($_SESSION['user_id']) . $_SESSION['user_id'] . $flight['user_id'];

// Start buffering output to capture HTML
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket PDF</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: auto; border:1px solid #ccc; padding:20px; }
        h2 { border-bottom:1px solid #333; padding-bottom:5px; }
        table { width:100%; border-collapse: collapse; margin-bottom: 20px; }
        td, th { border:1px solid #ccc; padding:8px; text-align:left; }
    </style>
</head>
<body>

<div class="container">
    <h2>Flight Ticket Details</h2>

    <h3>Booking Information</h3>
    <table>
        <tr><td>Booking ID</td><td><?= $booking['booking_id'] ?></td></tr>
        <tr><td>Status</td><td><?= $booking['status'] ?></td></tr>
        <tr><td>Total Fare</td><td>Rs. <?= $booking['total_fare'] ?></td></tr>
        <tr><td>Booked At</td><td><?= $booking['created_at'] ?></td></tr>
    </table>

    <h3>Flight Information</h3>
    <table>
        <tr><td>Flight ID</td><td><?= $flight['flight_id'] ?></td></tr>
        <tr><td>Airline</td><td><?= $flight['airline'] ?></td></tr>
        <tr><td>From</td><td><?= $flight['from_city'] ?></td></tr>
        <tr><td>To</td><td><?= $flight['to_city'] ?></td></tr>
        <tr><td>Departure</td><td><?= $flight['dep_time'] ?></td></tr>
        <tr><td>Arrival</td><td><?= $flight['arrival_time'] ?></td></tr>
    </table>

    <h3>Passenger(s) Information</h3>
    <table>
        <tr><th>Name</th><th>Age</th><th>Gender</th><th>Cabin Weight</th><th>Luggage Weight</th></tr>
        <?php foreach($passengers as $p): ?>
        <tr>
            <td><?= $p['passenger_name'] ?></td>
            <td><?= $p['age'] ?></td>
            <td><?= $p['gender'] ?></td>
            <td><?= $p['cabin_weight'] ?> kg</td>
            <td><?= $p['luggage_weight'] ?> kg</td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Payment Information</h3>
    <table>
        <tr><td>Amount</td><td>Rs. <?= $payment['amount'] ?></td></tr>
        <tr><td>Method</td><td><?= $payment['method'] ?></td></tr>
        <tr><td>Status</td><td><?= $payment['payment_status'] ?></td></tr>
    </table>
</div>

</body>
</html>

<?php
$html = ob_get_clean();

// Initialize Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Optional) Setup paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output to browser (download)
$dompdf->stream("ticket_$booking_id.pdf", ["Attachment" => 1]);
?>
