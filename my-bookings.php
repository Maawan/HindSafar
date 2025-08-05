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
  <link rel="icon" href="./assets/images/second.png" type="image/png" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-200 text-gray-800">
  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <a href="./dashboard.php" class="flex items-center space-x-2">
        <img src="./assets/images/logo.png" alt="logo" class="h-6" />
      </a>
      <h1 class="text-xl font-semibold">My Bookings</h1>
      <div class="hidden md:flex space-x-6 text-sm text-gray-600">
        <div>INR</div>
        <div>English</div>
      </div>
    </div>
  </header>

  <main class="max-w-4xl mx-auto p-4">
    <!-- Filter Buttons -->
    <div class="flex gap-2 mb-6">
      <button onclick="filterBookings('all')" id="btn-all"
        class="filter-btn px-4 py-1.5 rounded-xl text-sm border transition-colors">
        All
      </button>
      <button onclick="filterBookings('flight')" id="btn-flight"
        class="filter-btn px-4 py-1.5 rounded-xl text-sm border transition-colors">
        Flights
      </button>
      <button onclick="filterBookings('hotel')" id="btn-hotel"
        class="filter-btn px-4 py-1.5 rounded-xl text-sm border transition-colors">
        Hotels
      </button>
      <button onclick="filterBookings('package')" id="btn-package"
        class="filter-btn px-4 py-1.5 rounded-xl text-sm border transition-colors">
        Travel Packages
      </button>
    </div>

    <!-- Loader -->
    <div class="flex justify-center my-10" id="loader">
      <div class="w-12 h-12 border-4 border-gray-300 border-t-blue-600 rounded-full animate-spin"></div>
    </div>

    <!-- Bookings Content -->
    <div id="content" class="hidden space-y-10">
      <section>
        <h2 class="text-lg font-semibold text-blue-700 mb-4">✈️ Flight Bookings</h2>
        <div id="flight" class="space-y-4"></div>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-blue-700 mb-4">🏨 Hotel Bookings</h2>
        <div id="hotel" class="space-y-4"></div>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-blue-700 mb-4">🧳 Travel Packages</h2>
        <div id="package" class="space-y-4"></div>
      </section>
    </div>
  </main>
  <footer class="bg-white mt-12 pt-10 pb-6 border-t">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-4 gap-6 text-sm text-gray-600">
          <div>
            <img src="./assets/images/logo.png" class="h-10 mb-5" alt="" srcset="">
            <p>Your one-stop travel solution for booking flights, hotels, and custom travel packages.</p>
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

  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
    window.addEventListener('load', async () => {
      await fetchBooking();

      // Apply filter from URL if present
      const urlParams = new URLSearchParams(window.location.search);
      const filter = urlParams.get("filter") || "all";
      filterBookings(filter);
    });

    async function fetchBooking() {
      const loader = document.getElementById('loader');
      const content = document.getElementById('content');

      const res = await fetch("./backend/api/my-bookings.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
      });

      const bookings = await res.json();
      const data = {
        flights: bookings.flight_bookings,
        hotels: bookings.hotel_bookings,
        packages: bookings.package_bookings
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
      return `
      <div class="bg-white p-5 shadow rounded-xl flex justify-between items-center">
        <div>
          <div class="text-lg font-semibold">${f.from} → ${f.to}</div>
          <div class="text-sm text-gray-500">${f.date_time}</div>
        </div>
        <div class="text-right">
          <div class="font-semibold">₹${f.amount}</div>
          ${f.payment_status === 'Completed' ?
            `<span class="text-green-600 text-sm">🗸 Confirmed</span><br>
             <button class="mt-1 bg-green-500 text-white px-3 py-1 text-sm rounded" onclick="downloadTicket('${f.booking_type}', '${f.booking_id}')">Download Receipt</button>` :
            `<span class="text-red-500 text-sm">❌ Payment failed</span><br>
             <button class="mt-1 bg-yellow-500 text-white px-3 py-1 text-sm rounded" onclick="retryPendingPayment('${f.order_id}', '${f.amount}')">Retry Payment</button>`}
        </div>
      </div>`;
    }

    function renderHotel(h) {
      return `
      <div class="bg-white p-5 shadow rounded-xl flex justify-between items-center">
        <div>
          <div class="text-lg font-semibold">${h.Hotel_Name}</div>
          <div class="text-sm text-gray-500">${h.Hotel_City} | ${h.date_time}</div>
        </div>
        <div class="text-right">
          <div class="font-semibold">₹${h.amount}</div>
          ${h.payment_status === 'Completed' ?
            `<span class="text-green-600 text-sm">🗸 Confirmed</span><br>
             <button class="mt-1 bg-green-500 text-white px-3 py-1 text-sm rounded" onclick="downloadTicket('${h.booking_type}', '${h.booking_id}')">Download Receipt</button>` :
            `<span class="text-red-500 text-sm">❌ Payment failed</span><br>
             <button class="mt-1 bg-yellow-500 text-white px-3 py-1 text-sm rounded" onclick="retryPendingPayment('${h.order_id}', '${h.amount}')">Retry Payment</button>`}
        </div>
      </div>`;
    }

    function renderPackage(p) {
      const isCompleted = p.payment_status === 'Completed';
      return `
      <div class="bg-white p-5 shadow rounded-xl flex justify-between items-center">
        <div>
          <div class="text-lg font-semibold">${p.package_name || 'Custom Package'}</div>
          <div class="text-sm text-gray-500">${p.destination_location || ''}</div>
          <div class="text-sm text-gray-500">${p.startDate}</div>
        </div>
        <div class="text-right">
          <div class="font-semibold">₹${p.amount}</div>
          ${isCompleted ?
            `<span class="text-green-600 text-sm">🗸 Confirmed</span><br>
             <button class="mt-1 bg-green-500 text-white px-3 py-1 text-sm rounded" onclick="downloadTicket('${p.booking_type}', '${p.booking_id}')">Download Receipt</button>` :
            `<span class="text-red-500 text-sm">❌ Payment failed</span><br>
             <button class="mt-1 bg-yellow-500 text-white px-3 py-1 text-sm rounded" onclick="retryPendingPayment('${p.order_id}', '${p.amount}')">Retry Payment</button>`}
        </div>
      </div>`;
    }

    function retryPendingPayment(orderId, amount) {
      const options = {
        key: "rzp_test_tN2HjlxfDwX4rW",
        amount: amount,
        currency: "INR",
        name: "HindSafar Online Booking Pvt Ltd",
        description: "Pay for your order",
        order_id: orderId,
        callback_url: "http://localhost/Hindsafar/verify.php"
      };
      const rzp = new Razorpay(options);
      rzp.open();
    }

    function downloadTicket(type, id) {
      window.location.href = `./generate-ticket.php?type=${type}&booking_id=${id}&download=1`;
    }

    function filterBookings(type) {
      const sections = {
        flight: document.querySelector('section:nth-of-type(1)'),
        hotel: document.querySelector('section:nth-of-type(2)'),
        package: document.querySelector('section:nth-of-type(3)')
      };

      const buttons = {
        all: document.getElementById('btn-all'),
        flight: document.getElementById('btn-flight'),
        hotel: document.getElementById('btn-hotel'),
        package: document.getElementById('btn-package')
      };

      // Reset all button styles
      Object.values(buttons).forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white');
        btn.classList.add('bg-white', 'text-gray-800');
      });

      // Apply selected filter styles
      if (type === 'all') {
        Object.values(sections).forEach(sec => sec.classList.remove('hidden'));
        buttons.all.classList.add('bg-blue-600', 'text-white');
      } else {
        Object.entries(sections).forEach(([key, sec]) => {
          sec.classList.toggle('hidden', key !== type);
        });
        buttons[type].classList.add('bg-blue-600', 'text-white');
      }

      // Update URL without reload
      const newUrl = `${window.location.pathname}?filter=${type}`;
      history.replaceState(null, '', newUrl);
    }
  </script>
</body>

</html>
