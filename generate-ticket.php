<?php
// At the top of your file
require 'vendor/autoload.php'; // Dompdf & PHPMailer
include './backend/Database/db.php'; // DB connection

use Dompdf\Dompdf;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// session_start();

// Extract request parameters
$type = $_GET['type'] ?? null;
$booking_id = $_GET['booking_id'] ?? null;

// Accessed from browser: serve the PDF directly
if (php_sapi_name() !== 'cli' && isset($_GET['download']) && $type && $booking_id) {
    if (!isset($_SESSION['user_id'])) {
        die("Unauthorized access.");
    }
    $pdf = generateTicketPDF($type, $booking_id, $_SESSION['user_id']);
    header('Content-Type: application/pdf');
    header("Content-Disposition: attachment; filename=ticket_{$type}_{$booking_id}.pdf");
    echo $pdf;
    exit;
}

// ========================
// ✅ FUNCTION: Generate Ticket PDF
// ========================
function generateTicketPDF($type, $booking_id, $user_id)
{
    global $pdo;
    ob_start();

    // --- HTML starts ---
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Ticket PDF</title>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { max-width: 800px; margin: auto; border: 1px solid #ccc; padding: 20px; }
            h2 { border-bottom: 1px solid #333; padding-bottom: 5px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            td, th { border: 1px solid #ccc; padding: 8px; text-align: left; }
            .header-logo { text-align: center; margin-bottom: 20px; }
            .footer { text-align: center; margin-top: 40px; font-size: 0.9em; color: #666; }
        </style>
    </head>
    <body>
    <div class="container">
    <div class="header-logo">
        <img src="./assets/images/logo.png" alt="HindSafar Logo" height="60" />
    </div>
    <?php

    // ==== FLIGHT BOOKING ====
    if ($type === 'flight') {
        $stmt = $pdo->prepare("SELECT * FROM flight_bookings WHERE booking_id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();

    if (!$booking) die("Flight booking not found.");

    $stmt = $pdo->prepare("SELECT * FROM flights WHERE flight_id = ?");
    $stmt->execute([$booking['flight_id']]);
    $flight = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT * FROM flight_passengers WHERE booking_id = ?");
    $stmt->execute([$booking_id]);
    $passengers = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT * FROM payments WHERE payment_id = ?");
    $stmt->execute([$booking['payment_id']]);
    $payment = $stmt->fetch();

    if ($payment['payment_status'] !== 'Completed' || $_SESSION['user_id'] !== $booking['user_id']) {
        die("Unauthorized or unpaid booking.");
    }

    echo "<h2>Flight Ticket Details</h2>";
    echo "<h3>Booking Information</h3><table>
        <tr><td>Booking ID</td><td>{$booking['booking_id']}</td></tr>
        <tr><td>Status</td><td>{$booking['status']}</td></tr>
        <tr><td>Total Fare</td><td>Rs. {$booking['total_fare']}</td></tr>
        <tr><td>Booked At</td><td>{$booking['created_at']}</td></tr>
    </table>";

    echo "<h3>Flight Information</h3><table>
        <tr><td>Flight ID</td><td>{$flight['flight_id']}</td></tr>
        <tr><td>Airline</td><td>{$flight['airline']}</td></tr>
        <tr><td>From</td><td>{$flight['from_city']}</td></tr>
        <tr><td>To</td><td>{$flight['to_city']}</td></tr>
        <tr><td>Departure</td><td>{$flight['dep_time']}</td></tr>
        <tr><td>Arrival</td><td>{$flight['arrival_time']}</td></tr>
    </table>";

    echo "<h3>Passenger(s) Information</h3><table>
        <tr><th>Name</th><th>Age</th><th>Gender</th><th>Cabin Weight</th><th>Luggage Weight</th></tr>";
    foreach ($passengers as $p) {
        echo "<tr>
            <td>{$p['passenger_name']}</td>
            <td>{$p['age']}</td>
            <td>{$p['gender']}</td>
            <td>{$p['cabin_weight']} kg</td>
            <td>{$p['luggage_weight']} kg</td>
        </tr>";
    }
    echo "</table>";

    echo "<h3>Payment Information</h3><table>
        <tr><td>Amount</td><td>Rs. {$payment['amount']}</td></tr>
        <tr><td>Method</td><td>{$payment['method']}</td></tr>
        <tr><td>Status</td><td>{$payment['payment_status']}</td></tr>
    </table>";
    }

    // ==== HOTEL BOOKING ====
    else if ($type === 'hotel') {
        $stmt = $pdo->prepare("SELECT 
            hb.booking_id as booking_id,
            h.name as hotel_name,
            hb.user_id as user_id,
            h.address as hotel_address,
            h.city as hotel_city,
            rt.type_name as Room_Type,
            hb.rooms_booked as rooms_booked,
            hb.check_in_date as checkin_date,
            hb.check_out_date as checkout_date,
            p.razorpay_order_id as order_id,
            p.amount as amount,
            p.payment_status as payment_status,
            hb.created_at as orderDateAndTime
            FROM hotels h, hotel_bookings hb , roomtype rt, payments p
            WHERE hb.hotel_id = h.hotel_id AND rt.room_type_id = hb.room_type_id 
            AND p.payment_id = hb.payment_id AND hb.booking_id = ?");
        $stmt->execute([$booking_id]);
        $hotel_booking = $stmt->fetch();

        if (!$hotel_booking) die("Hotel booking not found.");
        if ($hotel_booking['payment_status'] !== 'Completed' || $user_id !== $hotel_booking['user_id']) {
            die("Unauthorized or unpaid booking.");
        }

        echo "<h2>Hotel Booking Details</h2>";
        echo "<h3>Booking Information</h3><table>
            <tr><td>Booking ID</td><td>{$hotel_booking['booking_id']}</td></tr>
            <tr><td>Check-In</td><td>{$hotel_booking['checkin_date']}</td></tr>
            <tr><td>Check-Out</td><td>{$hotel_booking['checkout_date']}</td></tr>
            <tr><td>Room Type</td><td>{$hotel_booking['Room_Type']}</td></tr>
            <tr><td>Rooms Booked</td><td>{$hotel_booking['rooms_booked']}</td></tr>
        </table>";

        echo "<h3>Hotel Information</h3><table>
            <tr><td>Hotel Name</td><td>{$hotel_booking['hotel_name']}</td></tr>
            <tr><td>Address</td><td>{$hotel_booking['hotel_address']}</td></tr>
            <tr><td>City</td><td>{$hotel_booking['hotel_city']}</td></tr>
        </table>";

        echo "<h3>Payment Information</h3><table>
            <tr><td>Amount</td><td>Rs. {$hotel_booking['amount']}</td></tr>
            <tr><td>Status</td><td>{$hotel_booking['payment_status']}</td></tr>
            <tr><td>Payment Reference No</td><td>{$hotel_booking['order_id']}</td></tr>
            <tr><td>Booked At</td><td>{$hotel_booking['orderDateAndTime']}</td></tr>
        </table>";
    }

    // ==== PACKAGE BOOKING ====
    else if ($type === 'package') {
        $stmt = $pdo->prepare("SELECT 
        cp.package_id, cp.package_name, cp.no_of_days as duration,
        cp.location as destination_location, cp.destination_covered as placesToVisit,
        cp.accommodation_included as hotels_included, cp.hotel_type,
        cp.location_commute_included as taxi_included, cp.flights_included,
        cp.commute_airport_included as pick_from_home_included,
        cp.meals_included, cp.no_of_persons, cp.price,
        cpb.startDate, cpb.amount,
        cpb.user_id, cpb.booking_id,
        p.razorpay_order_id as order_id, p.payment_status
        FROM custom_packages cp, custom_packages_bookings cpb, payments p
        WHERE cp.package_id = cpb.package_id 
        AND cpb.payment_id = p.payment_id
        AND cpb.booking_id = ?");
    $stmt->execute([$booking_id]);
    $package = $stmt->fetch();

    if (!$package) die("Package booking not found.");

    if ($package['payment_status'] !== 'Completed' || $_SESSION['user_id'] !== $package['user_id']) {
        die("Unauthorized or unpaid booking.");
    }

    echo "<h2>Custom Travel Package Ticket</h2>";
    echo "<h3>Booking Information</h3><table>
        <tr><td>Booking ID</td><td>{$package['booking_id']}</td></tr>
        <tr><td>Start Date</td><td>{$package['startDate']}</td></tr>
        <tr><td>Package Name</td><td>{$package['package_name']}</td></tr>
        <tr><td>Duration</td><td>{$package['duration']} days</td></tr>
        <tr><td>Persons</td><td>{$package['no_of_persons']}</td></tr>
    </table>";

    echo "<h3>Package Inclusions</h3><table>
        <tr><td>Destination</td><td>{$package['destination_location']}</td></tr>
        <tr><td>Places to Visit</td><td>{$package['placesToVisit']}</td></tr>
        <tr><td>Accommodation</td><td>{$package['hotels_included']} (Type: {$package['hotel_type']})</td></tr>
        <tr><td>Flights Included</td><td>{$package['flights_included']}</td></tr>
        <tr><td>Local Commute</td><td>{$package['taxi_included']}</td></tr>
        <tr><td>Pickup from Home</td><td>{$package['pick_from_home_included']}</td></tr>
        <tr><td>Meals Included</td><td>{$package['meals_included']}</td></tr>
    </table>";

    echo "<h3>Payment Information</h3><table>
        <tr><td>Amount</td><td>Rs. {$package['amount']}</td></tr>
        <tr><td>Status</td><td>{$package['payment_status']}</td></tr>
        <tr><td>Payment Reference No</td><td>{$package['order_id']}</td></tr>
    </table>";
    }

    else {
        die("Invalid booking type.");
    }

    ?>
    <div class="footer">
        HindSafar – K-41 Clement Town, Dehradun, India<br/>
        Thank you for booking with us.
    </div>
    </div>
    </body>
    </html>
    <?php
    // --- HTML ends ---

    $html = ob_get_clean();
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return $dompdf->output(); // Return PDF binary
}
