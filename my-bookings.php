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
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Bookings – HindSafar</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f4f7fa;
    }

    header {
      background: #0052cc;
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header h1 {
      margin: 0;
      font-size: 22px;
    }

    .container {
      max-width: 1000px;
      margin: 30px auto;
      padding: 0 15px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      position: relative;
      min-height: 200px;
    }

    /* Loader styles */
    .loader {
      border: 8px solid #f3f3f3;
      border-top: 8px solid #0052cc;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      animation: spin 1s linear infinite;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
    @keyframes spin {
      0% { transform: translate(-50%, -50%) rotate(0deg); }
      100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
    .hidden {
      display: none;
    }

    .tabs {
      display: flex;
      border-bottom: 1px solid #ccc;
    }
    .tab {
      padding: 12px 20px;
      cursor: pointer;
    }
    .tab.active {
      border-bottom: 3px solid #0052cc;
      font-weight: bold;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 0;
    }
    th, td {
      text-align: left;
      padding: 12px;
      border-bottom: 1px solid #eee;
    }
    th {
      background: #f4f7fa;
    }

    .green {
      color: green;
      font-weight: bold;
    }
    .red {
      color: red;
      font-weight: bold;
    }
    .yellow{
      color : orange;
      font-weight : bold;
    }
    .no-booking {
      text-align: center;
      padding: 30px;
      font-size: 18px;
      color: #555;
    }
    h1{
      cursor: pointer;
    }
  </style>
</head>
<body>

<header>
  <h1 onclick="navigateToHome()">HindSafar</h1>
  <div><?php echo $_SESSION['name']; ?></div>
</header>

<div class="container">
  <!-- Loader -->
  <div class="loader" id="loader"></div>

  <!-- Tabs -->
  <div id="content" class="hidden">
    <div class="tabs">
      <div class="tab active" onclick="showTab('all')">All</div>
      <div class="tab" onclick="showTab('flight')">Flight Booking</div>
      <div class="tab" onclick="showTab('hotel')">Hotel Booking</div>
      <div class="tab" onclick="showTab('package')">Travel Packages</div>
    </div>

    <!-- Tab contents -->
    <div id="all" class="tab-content"></div>
    <div id="flight" class="tab-content" style="display:none;"></div>
    <div id="hotel" class="tab-content" style="display:none;"></div>
    <div id="package" class="tab-content" style="display:none;"></div>
  </div>
</div>

<script>
function showTab(tabId) {
  document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
  document.getElementById(tabId).style.display = 'block';
  event.target.classList.add('active');
}

// Fetch bookings from API (simulate with setTimeout)
function navigateToHome(){
  window.location.href = "./";
}
async function getBookings() {
  let data = {};
  let flights = [];
  let hotels = [];
  let packages = [];
  

}
async function fetchBooking(){
  const loader = document.getElementById('loader');
  const content = document.getElementById('content');
  let flights = [];
  let hotels = [];
  let packages = [];
  const res = await fetch("./backend/api/flights/my-bookings.php",{
    method : "POST",
    headers: {"Content-Type" : "application/json"}
  });
  flights = await res.json();

  const data = {
    flights : flights.bookings,
    hotels,
    packages
  }
  console.log(data);
  populateBookings(data);
  loader.classList.add('hidden');
  content.classList.remove('hidden');

}

window.addEventListener('load', () => {
    fetchBooking();
});

function populateBookings(data) {
  // All Bookings
  const all = document.getElementById('all');
  all.innerHTML = buildTable([...data.flights]);

  // Flight Bookings
  const flight = document.getElementById('flight');
  flight.innerHTML = data.flights.length ? buildTable(data.flights) : '<div class="no-booking">No flight bookings.</div>';

  // Hotel Bookings
  const hotel = document.getElementById('hotel');
  hotel.innerHTML = data.hotels.length ? buildTable(data.hotels) : '<div class="no-booking">No hotel bookings.</div>';

  // Package Bookings
  const packageTab = document.getElementById('package');
  packageTab.innerHTML = data.packages.length ? buildTable(data.packages) : '<div class="no-booking">No package bookings.</div>';
}

function buildTable(bookings) {
  if (!bookings.length) {
    return '<div class="no-booking">😔 You have no bookings yet.</div>';
  }
  let html = '<table><tr><th>Booking Description</th><th>Status</th><th>Price</th><th>Date</th></tr>';
  bookings.forEach(b => {
    let status = "";
    let color = "";
    if(b.payment_status === "Pending"){
      status = "Payment Pending";
      color = "yellow";
    }else if(b.payment_status === "Completed"){
      status = b.flight_status + " | Payment Verified";
      color = "green";
    }
    html += `<tr>
      <td>${b.from} to ${b.to}</td>
      <td class="${color}">${status}</td>
      <td>Rs ${b.amount}</td>
      
      <td>${b.date_time}</td>
    </tr>`;
  });
  html += '</table>';
  return html;
}
</script>

</body>
</html>
