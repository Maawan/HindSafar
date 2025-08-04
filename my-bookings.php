<?php
session_start();
if (!isset($_SESSION['name'])) {
  header("Location: login.html");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Bookings – HindSafar</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-800">
  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <div class="flex items-center space-x-2 cursor-pointer">
        <a href="./dashboard.php">

          <img src="./assets/images/logo.png" alt="logo" class="h-6" />
        </a>
      </div>
      <div class="font-semibold">My Bookings</div>
      <div class="space-x-6 hidden md:flex">

        <div class="text-sm">INR | English</div>
      </div>
    </div>
  </header>
  <div class="max-w-3xl mx-auto p-4">
    <!-- <div class="flex items-center justify-between py-4">
      <h1 class="text-2xl font-semibold">Bookings</h1>
      <div class="flex gap-4 items-center">
        <a href="#" class="text-sm text-gray-600 hover:underline">My trips</a>
        <a href="#" class="text-sm text-gray-600 hover:underline">Support</a>
        <button class="bg-blue-600 text-white text-sm px-4 py-1.5 rounded">Sign In</button>
      </div>
    </div> -->

    <!-- Loader -->
    <div class="flex justify-center my-10" id="loader">
      <div class="w-12 h-12 border-4 border-gray-300 border-t-blue-600 rounded-full animate-spin"></div>
    </div>

    <!-- Content -->
    <div id="content" class="hidden space-y-8">
      <div>
        <h2 class="text-sm font-semibold text-gray-500 mb-2">FLIGHT</h2>
        <div id="flight" class="space-y-4"></div>
      </div>

      <div>
        <h2 class="text-sm font-semibold text-gray-500 mb-2">HOTEL</h2>
        <div id="hotel" class="space-y-4"></div>
      </div>

      <div>
        <h2 class="text-sm font-semibold text-gray-500 mb-2">PACKAGE</h2>
        <div id="package" class="space-y-4"></div>
      </div>
    </div>
  </div>
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
    window.addEventListener('load', () => {
      fetchBooking();
    });

    async function fetchBooking() {
      const loader = document.getElementById('loader');
      const content = document.getElementById('content');

      const res = await fetch("./backend/api/my-bookings.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
      });
      const bookings = await res.json();

      const data = {
        flights: bookings.flight_bookings,
        hotels: bookings.hotel_bookings,
        packages: [] // Add package data here when available
      };

      populateCards(data);
      loader.classList.add('hidden');
      content.classList.remove('hidden');
    }

    function populateCards(data) {
      document.getElementById('flight').innerHTML = data.flights.map(renderFlight).join('');
      document.getElementById('hotel').innerHTML = data.hotels.map(renderHotel).join('');
      document.getElementById('package').innerHTML = data.packages.map(renderPackage).join('');
    }

    function renderFlight(f) {
  let paymentStatus = f.payment_status;

  if (paymentStatus === "Completed") {
    return `<div class="bg-white shadow rounded-xl p-4 flex justify-between items-center">
      <div>
        <div class="text-lg font-semibold">${f.from} → ${f.to}</div>
        <div class="text-sm text-gray-500">${f.date_time}</div>
      </div>
      <div class="text-right space-y-1">
        <div class="text-sm text-gray-800 font-semibold">₹${f.amount}</div>
        <span class="text-green-500 text-sm px-3 py-1 rounded">🗸 Confirmed</span><br>
        <span class="bg-green-500 cursor-pointer text-white text-sm px-3 py-1 rounded" onclick="downloadTicket('${f.booking_type}' , '${f.booking_id}')">Download Receipt</span>
      </div>
    </div>`;
  } else if (paymentStatus === "Pending") {
    return `<div class="bg-white shadow rounded-xl p-4 flex justify-between items-center">
      <div>
        <div class="text-lg font-semibold">${f.from} → ${f.to}</div>
        <div class="text-sm text-gray-500">${f.date_time}</div>
      </div>
      <div class="text-right space-y-1">
        <div class="text-sm text-gray-800 font-semibold">₹${f.amount}</div>
        <span class="text-sm text-red-400 mr-2">❌ Payment failed</span><br>
        <span class="bg-yellow-500 text-white cursor-pointer text-sm px-3 py-1 rounded" onclick="retryPendingPayment('${f.order_id}' , '${f.amount}')">Retry Payment</span>
      </div>
    </div>`;
  }
}



  function renderHotel(h) {
  let paymentStatus = h.payment_status;

  if (paymentStatus == "Pending") {
    return `<div class="bg-white shadow rounded-xl p-4 flex justify-between items-center">
      <div>
        <div class="text-lg font-semibold">${h.Hotel_Name}</div>
        <div class="text-sm text-gray-500">${h.Hotel_City} | ${h.date_time}</div>
      </div>
      <div class="text-right space-y-1">
        <div class="text-sm text-gray-800 font-semibold">₹${h.amount}</div>
        <span class="text-sm text-red-400 mr-2">❌ Payment failed</span><br>
        <span class="bg-yellow-500 text-white cursor-pointer text-sm px-3 py-1 rounded" onclick="retryPendingPayment('${h.order_id}' , '${h.amount}')">Retry Payment</span>
      </div>
    </div>`;
  } else if (paymentStatus == "Completed") {
    return `<div class="bg-white shadow rounded-xl p-4 flex justify-between items-center">
      <div>
        <div class="text-lg font-semibold">${h.Hotel_Name}</div>
        <div class="text-sm text-gray-500">${h.Hotel_City} | ${h.date_time}</div>
      </div>
      <div class="text-right space-y-1">
        <div class="text-sm text-gray-800 font-semibold">₹${h.amount}</div>
        <span class="text-green-500 text-sm px-3 py-1 rounded">🗸 Confirmed</span><br>
        <span class="bg-green-500 cursor-pointer text-white text-sm px-3 py-1 rounded" onclick="downloadTicket('${h.booking_type}' , '${h.booking_id}')">Download Receipt</span>
      </div>
    </div>`;
  }
  return ``;
}



    function retryPendingPayment(orderId , amount) {
      console.log("Order Id" + orderId);
      let options = {
          key : "rzp_test_tN2HjlxfDwX4rW",
          amount : amount,
          currency : "INR",
          name : "HindSafar Online Booking Pvt Ltd",
          description : "Pay for your order",
          order_id : orderId,
          callback_url : "http://localhost/Hindsafar/verify.php"
        }
        let rzp = new Razorpay(options);
        rzp.open();

    }

    function downloadTicket(type, id) {
      // console.log(type + " " + id);

      window.location.href = "./generate-ticket.php?type=" + type + "&booking_id=" + id;
    }

    function renderPackage(p) {
      return `<div class="bg-white shadow rounded-xl p-4 flex justify-between items-center">
    <div>
      <div class="text-lg font-semibold">${p.package_name || 'Custom Package'}</div>
      <div class="text-sm text-gray-500">${p.destination || ''}</div>
      <div class="text-sm text-gray-500">${p.start_date} – ${p.end_date}</div>
    </div>
    <div class="text-right">
      <div class="text-sm text-gray-700">$${p.amount}</div>
      <span class="bg-green-500 text-white text-sm px-3 py-1 rounded">Confirmed</span>
    </div>
  </div>`;
    }
  </script>
</body>

</html>