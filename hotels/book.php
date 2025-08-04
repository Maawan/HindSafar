<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id || !isset($_SESSION['name'])) {
    header("Location: /HindSafar/login.html");
    exit();
}
$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Confirm Your Hotel Booking</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<!-- Header -->
<header class="bg-white shadow">
  <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
    <div class="flex items-center space-x-2">
      <img src="../assets/images/logo.png" alt="logo" class="h-6" />
    </div>
    <div class="space-x-6 hidden md:flex">
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="../my-bookings.php" class="text-sm font-medium hover:text-blue-600">My Trips</a>
        <a href="../logout.php" class="text-sm font-medium hover:text-blue-600">Signout</a>
      <?php else: ?>
        <a href="#" class="text-sm font-medium hover:text-blue-600">Login or Create Account</a>
      <?php endif; ?>
      <div class="text-sm">INR | English</div>
    </div>
  </div>
</header>

<!-- Welcome User -->
<!-- <div class="max-w-7xl mx-auto mt-4 px-4 text-right text-sm text-gray-600">
  👤 <?php echo htmlspecialchars($name); ?>
</div> -->

<!-- Booking Details Container -->
<div class="max-w-4xl mx-auto mt-6 bg-white shadow-md rounded-lg p-6" id="bookingContainer">
  <div class="text-center py-10">
    <p class="text-gray-600 text-lg">Loading booking details...</p>
  </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
  async function createOrder(data){
    try{
      const response = await fetch('../backend/api/hotels/initiate-booking.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await response.json();
      const razorPayId = result?.booking_id;
      if (result.success) {
        let options = {
          key : "rzp_test_tN2HjlxfDwX4rW",
          amount : result.amount,
          currency : "INR",
          name : "HindSafar Online Booking Pvt Ltd",
          description : "Pay for your order",
          order_id : razorPayId,
          callback_url : "http://localhost/Hindsafar/verify.php?type=hotel"
        }
        let rzp = new Razorpay(options);
        rzp.open();
      } else {
        alert("Failed to create order: " + (result.message || "Unknown error"));
      }
    }catch(error){
      console.error(error);
    }
  }   

  async function fetchBookingDetails() {
    const params = new URLSearchParams(window.location.search);
    const hotel_id = params.get("hotel_id");
    const room_type_id = params.get("room_type_id");
    const checkin_date = params.get("checkin_date");
    const checkout_date = params.get("checkout_date");
    const rooms = params.get("rooms");

    if (!hotel_id || !room_type_id || !checkin_date || !checkout_date || !rooms) {
      alert("Missing booking parameters. Redirecting back.");
      window.location.href = "hotels.php";
      return;
    }

    const requestData = {
      hotel_id: hotel_id,
      room_type_id: room_type_id,
      checkin_date: checkin_date,
      checkout_date: checkout_date,
      no_of_rooms: rooms
    };

    try {
      const response = await fetch('../backend/api/hotels/prebook.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(requestData)
      });

      const result = await response.json();
      const container = document.getElementById("bookingContainer");

      if (!result.success) {
        alert(result.message || "Rooms not available. Redirecting back.");
        return;
      }

      const data = result.data;

      container.innerHTML = `
        <h3 class="text-2xl font-semibold text-blue-800 mb-4">Booking Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
          <div>
            <p><strong>Hotel:</strong> ${data.hotel_name}</p>
            <p><strong>Address:</strong> ${data.hotel_address}</p>
            <p><strong>Room Type:</strong> ${data.room_type_name}</p>
            <p><strong>Price per Night:</strong> ₹${data.price_per_night}</p>
            <p><strong>Number of Rooms:</strong> ${data.no_of_rooms}</p>
          </div>
          <div>
            <p><strong>Check-in Date:</strong> ${data.checkin_date}</p>
            <p><strong>Check-out Date:</strong> ${data.checkout_date}</p>
            <p><strong>Total Nights:</strong> ${data.total_nights}</p>
            <p><strong>Total Cost:</strong> ₹${data.total_cost}</p>
          </div>
        </div>

        <div class="mt-6 space-y-4">
          <div class="bg-gray-100 p-4 rounded-md">
            <h4 class="text-blue-700 font-semibold">Check-in & Check-out</h4>
            <p>Check-in Time: 2:00 PM onwards</p>
            <p>Check-out Time: Before 11:00 AM</p>
          </div>

          <div class="bg-gray-100 p-4 rounded-md">
            <h4 class="text-blue-700 font-semibold">Guest Policies</h4>
            <ul class="list-disc ml-5">
              <li>✅ Valid ID proof required at the time of check-in.</li>
              <li>✅ Unmarried couples allowed (subject to hotel policy).</li>
              <li>✅ Early check-in or late check-out is subject to availability and may be chargeable.</li>
            </ul>
          </div>

          <div class="bg-gray-100 p-4 rounded-md">
            <h4 class="text-blue-700 font-semibold">Cancellation Policy</h4>
            <ul class="list-disc ml-5">
              <li>❌ Free cancellation before 24 hours of check-in date.</li>
              <li>❌ Cancellation within 24 hours of check-in date will incur one night charge.</li>
            </ul>
          </div>

          <div class="bg-gray-100 p-4 rounded-md text-sm text-gray-600">
            <h4 class="text-blue-700 font-semibold">Terms & Conditions</h4>
            <p>By confirming this booking, you agree to HindSafar's booking and cancellation policies. Please review your booking details carefully before proceeding to payment.</p>
          </div>
        </div>

        <form class="mt-6">
          <input type="hidden" name="hotel_id" value="${data.hotel_id}">
          <input type="hidden" name="room_type_id" value="${data.room_type_id}">
          <input type="hidden" name="checkin_date" value="${data.checkin_date}">
          <input type="hidden" name="checkout_date" value="${data.checkout_date}">
          <input type="hidden" name="rooms" value="${data.no_of_rooms}">
          <input type="hidden" name="total_price" value="${data.total_cost}">
          <button class="w-full md:w-auto px-6 py-3 bg-blue-700 text-white font-semibold rounded-md hover:bg-blue-900" id="confirmBookingBtn">
            Confirm Booking & Proceed to Payment
          </button>
        </form>
      `;

      document.getElementById("confirmBookingBtn").addEventListener('click', function(e){
        e.preventDefault();
        createOrder(data);
      });

    } catch (error) {
      console.error("Error fetching booking details:", error);
      alert("Something went wrong. Redirecting back.");
    }
  }

  fetchBookingDetails();
</script>
</body>
</html>
