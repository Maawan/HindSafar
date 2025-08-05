<?php
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id || !isset($_SESSION['name'])) {
    header("Location: /Hindsafar/login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Confirm Your Hotel Booking</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="../assets/images/second.png" type="image/png" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 font-sans">



<div class="container mx-auto px-4 py-10">
  <h1 class="text-2xl font-bold mb-6">Complete your hotel booking</h1>
  <div class="md:flex md:gap-6">
    <div class="md:w-2/3">
      <div id="bookingContainer" class="bg-white rounded-lg shadow-md p-6">
        <div class="text-gray-600 text-lg text-center py-10">Loading booking details...</div>
      </div>
    </div>
    <div class="md:w-1/3">
      <div class="bg-white rounded-lg shadow-md p-6 mt-6 md:mt-0">
        <h4 class="text-lg font-bold mb-4">Fare Summary</h4>
        <div class="flex justify-between mb-2">
          <span>Total Cost</span><span id="totalFare">₹0.00</span>
        </div>
        <button id="confirmBookingBtn" class="mt-6 w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold">
          Confirm Booking & Proceed to Payment
        </button>
      </div>
    </div>
  </div>
</div>

<footer class="bg-white mt-12 pt-10 pb-6 border-t">
  <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-4 gap-6 text-sm text-gray-600">
    <div>
      <img src="../assets/images/logo.png" class="h-10 mb-5" alt="" />
      <p>Your one-stop travel solution for flights, hotels & custom packages.</p>
    </div>
    <div>
      <h4 class="font-semibold text-gray-800 mb-2">Explore</h4>
      <ul class="space-y-1">
        <li><a href="?tab=flights" class="hover:text-primary">Flights</a></li>
        <li><a href="?tab=hotels" class="hover:text-primary">Hotels</a></li>
        <li><a href="?tab=packages" class="hover:text-primary">Packages</a></li>
      </ul>
    </div>
    <div>
      <h4 class="font-semibold text-gray-800 mb-2">Company</h4>
      <ul class="space-y-1">
        <li><a href="#" class="hover:text-primary">About Us</a></li>
        <li><a href="#" class="hover:text-primary">Contact</a></li>
        <li><a href="#" class="hover:text-primary">Terms & Conditions</a></li>
      </ul>
    </div>
    <div>
      <h4 class="font-semibold text-gray-800 mb-2">Follow Us</h4>
      <ul class="space-y-1">
        <li><a href="#" class="hover:text-primary">Instagram</a></li>
        <li><a href="#" class="hover:text-primary">Facebook</a></li>
        <li><a href="#" class="hover:text-primary">Twitter</a></li>
      </ul>
    </div>
  </div>
  <div class="text-center text-xs text-gray-400 mt-6">&copy; <?= date('Y') ?> HindSafar. All rights reserved.</div>
</footer>

<!-- Loader Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
  <div class="bg-white rounded-2xl p-6 w-80 text-center shadow-xl">
    <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-blue-500 mx-auto mb-4 border-solid border-gray-200"></div>
    <h2 class="text-lg font-semibold text-gray-800">Processing your payment...</h2>
    <p class="text-sm text-gray-500 mt-2">Please wait while we initiate Razorpay.</p>
  </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
  async function createOrder(data){
    document.getElementById("paymentModal").classList.remove("hidden");
    try {
      const response = await fetch('../backend/api/hotels/initiate-booking.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });

      const result = await response.json();
      const razorPayId = result?.booking_id;

      if (result.success) {
        let options = {
          key: "rzp_test_tN2HjlxfDwX4rW",
          amount: result.amount,
          image: "https://res.cloudinary.com/duklzb1ww/image/upload/v1754343126/logo_qcve8t.png",
          currency: "INR",
          name: "HindSafar Online Booking Pvt Ltd",
          description: "Pay for your order",
          order_id: razorPayId,
          modal: {
            ondismiss: function () {
              document.getElementById("paymentModal").classList.add("hidden");
              console.log("User closed Razorpay checkout.");
            }
          },
          callback_url: "http://localhost/Hindsafar/verify.php?type=hotel"
        };
        let rzp = new Razorpay(options);
        rzp.open();
      } else {
        document.getElementById("paymentModal").classList.add("hidden");
        alert("Failed to create order: " + (result.message || "Unknown error"));
      }
    } catch (error) {
      console.error(error);
      document.getElementById("paymentModal").classList.add("hidden");
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
        <h3 class="text-2xl font-bold text-gray-800 mb-6">Booking Summary</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700 text-sm">
          <div class="space-y-1">
            <p><span class="font-semibold">Hotel:</span> ${data.hotel_name}</p>
            <p><span class="font-semibold">Address:</span> ${data.hotel_address}</p>
            <p><span class="font-semibold">Room Type:</span> ${data.room_type_name}</p>
            <p><span class="font-semibold">Price per Night:</span> ₹${data.price_per_night}</p>
            <p><span class="font-semibold">Number of Rooms:</span> ${data.no_of_rooms}</p>
          </div>
          <div class="space-y-1">
            <p><span class="font-semibold">Check-in Date:</span> ${data.checkin_date}</p>
            <p><span class="font-semibold">Check-out Date:</span> ${data.checkout_date}</p>
            <p><span class="font-semibold">Total Nights:</span> ${data.total_nights}</p>
            <p><span class="font-semibold">Total Cost:</span> ₹${data.total_cost}</p>
          </div>
        </div>

        <div class="mt-8 space-y-6 text-sm text-gray-700">
          <div class="bg-gray-100 p-5 rounded-lg shadow-sm">
            <h4 class="text-base font-semibold text-blue-700 mb-1">Check-in & Check-out</h4>
            <p>Check-in Time: <strong>2:00 PM onwards</strong></p>
            <p>Check-out Time: <strong>Before 11:00 AM</strong></p>
          </div>

          <div class="bg-gray-100 p-5 rounded-lg shadow-sm">
            <h4 class="text-base font-semibold text-blue-700 mb-2">Guest Policies</h4>
            <ul class="list-disc ml-5 space-y-1">
              <li>✅ Valid ID proof required at the time of check-in.</li>
              <li>✅ Unmarried couples allowed (subject to hotel policy).</li>
              <li>✅ Early check-in or late check-out is subject to availability and may be chargeable.</li>
            </ul>
          </div>

          <div class="bg-gray-100 p-5 rounded-lg shadow-sm">
            <h4 class="text-base font-semibold text-blue-700 mb-2">Cancellation Policy</h4>
            <ul class="list-disc ml-5 space-y-1">
              <li>❌ Free cancellation before 24 hours of check-in date.</li>
              <li>❌ Cancellation within 24 hours of check-in date will incur one night charge.</li>
            </ul>
          </div>

          <div class="bg-gray-100 p-5 rounded-lg shadow-sm text-gray-600">
            <h4 class="text-base font-semibold text-blue-700 mb-2">Terms & Conditions</h4>
            <p>By confirming this booking, you agree to HindSafar's booking and cancellation policies. Please review your booking details carefully before proceeding to payment.</p>
          </div>
        </div>

        <form class="mt-8">
          <input type="hidden" name="hotel_id" value="${data.hotel_id}">
          <input type="hidden" name="room_type_id" value="${data.room_type_id}">
          <input type="hidden" name="checkin_date" value="${data.checkin_date}">
          <input type="hidden" name="checkout_date" value="${data.checkout_date}">
          <input type="hidden" name="rooms" value="${data.no_of_rooms}">
          <input type="hidden" name="total_price" value="${data.total_cost}">
          
        </form>
      `;

      document.getElementById("confirmBookingBtn").addEventListener('click', function(e) {
        e.preventDefault();
        createOrder(data);
      });
      document.getElementById("totalFare").innerText = "₹ "+data.total_cost;

    } catch (error) {
      console.error("Error fetching booking details:", error);
      alert("Something went wrong. Redirecting back.");
    }
  }

  fetchBookingDetails();
</script>

</body>
</html>
