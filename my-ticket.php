<?php
session_start();
if (!isset($_SESSION['name'])) {
  header("Location: login.html");
  exit();
}
$name = $_SESSION['name'];

require 'vendor/autoload.php'; // for Dompdf later
include './backend/Database/db.php'; // your DB connection
include './showError.php';


$booking_id = $_GET['booking_id'] ?? null;
if(!$booking_id){
  showError(
      "Booking Not Found",
      "The booking you are looking for does not exist or has been removed.",
      "assets/images/no-data.png",
      "./",
      5
    );
}


// Fetch booking details
$stmt = $pdo->prepare("SELECT * FROM flight_bookings WHERE booking_id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();
if(!$booking){
    showError(
      "Booking Not Found",
      "The booking you are looking for does not exist or has been removed.",
      "assets/images/no-data.png",
      "./",
      5
    );
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HindSafar Ticket Details</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f4f7fa;
      color: #333;
    }

    /* Navbar */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #004aad;
      color: white;
      padding: 1rem 2rem;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar h1 {
      margin: 0;
      font-size: 1.5rem;
    }

    .user-menu {
      position: relative;
      cursor: pointer;
      user-select: none;
    }

    .user-dropdown {
      display: none;
      position: absolute;
      right: 0;
      top: 2.5rem;
      background: white;
      color: #333;
      border-radius: 6px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      min-width: 160px;
    }

    .user-dropdown a {
      display: block;
      padding: 0.75rem 1rem;
      text-decoration: none;
      color: #333;
      border-bottom: 1px solid #eee;
    }

    .user-dropdown a:last-child {
      border-bottom: none;
    }

    .user-dropdown a:hover {
      background-color: #f5f5f5;
    }

    .user-dropdown.show {
      display: block;
    }

    .container {
      max-width: 900px;
      margin: 2rem auto;
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    h2 {
      border-bottom:1px solid #333;
      padding-bottom:5px;
    }

    table {
      width:100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    td, th {
      border:1px solid #ccc;
      padding:8px;
      text-align:left;
    }

    th {
      background: #f1f1f1;
    }

    .actions {
      text-align: center;
      margin-top: 20px;
    }

    .btn {
      padding:10px 20px;
      background:#004aad;
      color:white;
      text-decoration: none;
      border-radius: 4px;
      margin: 0 10px;
      display: inline-block;
    }

    .btn.cancel {
      background: #dc3545;
    }

    footer {
      text-align: center;
      padding: 1rem;
      background: #f1f1f1;
      font-size: 0.9rem;
      color: #666;
      margin-top: 2rem;
    }

    @media (max-width: 768px) {
      .container {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>

  <div class="navbar">
    <h1 style="cursor:pointer;" onclick="homepage()">HindSafar</h1>
    <div class="user-menu">
      <span>👤 <?php echo htmlspecialchars($name); ?></span>
      <div class="user-dropdown">
        <a href="./my-bookings.php">My Bookings</a>
        <a href="./contact-us.php">Contact Us</a>
        <a href="./logout.php">Logout</a>
      </div>
    </div>
  </div>

  <div class="container">
    <h2>Flight Ticket Details</h2>

    <h3>Booking Information</h3>
    <table>
      <tr><td>Booking ID</td><td><?= $booking['booking_id'] ?></td></tr>
      <tr><td>Status</td><td><?= $booking['status'] ?></td></tr>
      <tr><td>Total Fare</td><td>₹ <?= $booking['total_fare'] ?></td></tr>
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
      <tr><td>Amount</td><td>₹ <?= $payment['amount'] ?></td></tr>
      <tr><td>Method</td><td><?= $payment['method'] ?></td></tr>
      <tr><td>Status</td><td><?= $payment['payment_status'] ?></td></tr>
    </table>

    <div class="actions">
      <a href="generate-ticket.php?booking_id=<?= $booking_id ?>" class="btn">Download Ticket PDF</a>
      <a href="cancel-ticket.php?booking_id=<?= $booking_id ?>" class="btn cancel">Cancel Ticket</a>
    </div>
  </div>

  <footer>
    &copy; 2025 HindSafar. Empowering your journey.
  </footer>

  <script>

    function homepage(){
      console.log("ok");
      
      window.location.href = "./";
    }
   
    // Dropdown toggle functionality
    const userMenu = document.querySelector('.user-menu');
    const userDropdown = document.querySelector('.user-dropdown');

    userMenu.addEventListener('click', function(e) {
      e.stopPropagation(); // Prevent closing when clicking inside
      userDropdown.classList.toggle('show');
    });

    document.body.addEventListener('click', function() {
      userDropdown.classList.remove('show');
    });
  </script>

</body>
</html>
