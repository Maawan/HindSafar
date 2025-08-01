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
    .row-highlight{
    }
    .row-highlight:hover{
      background: #f4f7fa;
    }
    .pointer{
      cursor : pointer;
    }
    h1{
      cursor: pointer;
    }
    .cancel-btn{
      display: inline-block;
      margin-left : 5px;
      margin-right : 5px;
      padding: 4px 10px;
      background:rgb(163, 34, 22);
      color: white;
      text-decoration: none;
      border-radius: 5px;
      outline : none;
      border : none;
      cursor: pointer;
    }
    .cancel-btn:hover{
      background:rgb(113, 10, 1);
    }
    .down-btn{
      display: inline-block;
      margin-left : 5px;
      margin-right : 5px;
      padding: 4px 10px;
      background:rgb(1, 65, 162);
      color: white;
      text-decoration: none;
      border-radius: 5px;
      outline : none;
      border : none;
      cursor: pointer;
    }
    .down-btn:hover{
      background:rgb(1, 44, 108);
    }
    .yellow-back{
      background : orange;
    }
    .yellow-back:hover{
      background : rgb(199, 122, 15);
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
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function showTab(tabId) {
  document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
  document.getElementById(tabId).style.display = 'block';
  event.target.classList.add('active');
}

function navigateToHome(){
  window.location.href = "./";
}

window.addEventListener('load', () => {
    fetchBooking();
});

async function fetchBooking(){
  const loader = document.getElementById('loader');
  const content = document.getElementById('content');
  let bookings = [];

  const res = await fetch("./backend/api/my-bookings.php", {
    method : "POST",
    headers: {"Content-Type" : "application/json"}
  });
  bookings = await res.json();

  const data = {
    flights : bookings.flight_bookings,
    hotels : bookings.hotel_bookings,
    packages : [] // (replace this when you add real packages)
  };
  console.log(data);
  populateBookings(data);
  loader.classList.add('hidden');
  content.classList.remove('hidden');
}

function populateBookings(data) {
  const all = document.getElementById('all');
  all.innerHTML = buildTable([...data.flights]);

  const flight = document.getElementById('flight');
  flight.innerHTML = data.flights.length ? buildTable(data.flights) : '<div class="no-booking">No flight bookings.</div>';

  const hotel = document.getElementById('hotel');
  hotel.innerHTML = data.hotels.length ? buildHotelTable(data.hotels) : '<div class="no-booking">No hotel bookings.</div>';

  const packageTab = document.getElementById('package');
  packageTab.innerHTML = data.packages.length ? buildTable(data.packages) : '<div class="no-booking">No package bookings.</div>';
}

function navigateToTicket(bookingId){
  window.location.href = "./my-ticket.php?booking_id=" + bookingId;
}

function downloadBtn(booking_id , type){
  console.log(type);
  // console.log("kkk");
  
  
 window.location.href = "./generate-ticket.php?type="+type+"&booking_id=" + booking_id;
}

function retryPayment(orderId, amount){
  console.log(orderId + " " + typeof amount);
  let options = {
    key: "rzp_test_tN2HjlxfDwX4rW",
    amount: amount * 100,
    currency: "INR",
    name: "HindSafar Online Booking Pvt Ltd",
    description: "Pay for your order",
    order_id: orderId,
    callback_url: "http://localhost/Hindsafar/verify.php"
  };
  let rzp = new Razorpay(options);
  rzp.open();
}

function buildTable(bookings) {
  if (!bookings.length) {
    return '<div class="no-booking">😔 You have no bookings yet.</div>';
  }
  let html = '<table><tr><th>Booking Description</th><th>Status</th><th>Price</th><th>Date</th></tr>';
  bookings.forEach(b => {
    let status = "";
    let color = "";
    if (b.payment_status === "Pending") {
      status = "Payment Pending";
      color = "yellow";
    } else if (b.payment_status === "Completed") {
      status = b.flight_status + " | Payment Verified";
      color = "green";
    }
    // console.log(b.booking_type);
    
    html += `<tr class="row-highlight">
      <td class="pointer" onclick="navigateToTicket(${b.booking_id})">${b.from} to ${b.to}</td>
      <td class="${color}">${status}</td>
      <td>Rs ${b.amount}</td>
      <td>${b.date_time} ${b.payment_status === "Completed" 
        ? `<button class="down-btn" onclick="downloadBtn(${b.booking_id} , '${b.booking_type}')">Download Ticket</button><button class="cancel-btn">Cancel Ticket</button>`
        : `<button class="down-btn yellow yellow-back" onclick='retryPayment("${b.order_id}" , ${b.amount})'>Retry Payment</button>`}
      </td>
    </tr>`;
  });
  html += '</table>';
  return html;
}

function buildHotelTable(bookings) {
  if (!bookings.length) {
    return '<div class="no-booking">😔 You have no hotel bookings yet.</div>';
  }

  let html = '<table><tr><th>Hotel</th><th>Status</th><th>Price</th><th>Date</th></tr>';

  bookings.forEach(b => {
    let status = "";
    let color = "";
    if (b.payment_status === "Pending") {
      status = "Payment Pending";
      color = "yellow";
    } else if (b.payment_status === "Completed") {
      status = "Payment Completed";
      color = "green";
    }

    html += `<tr class="row-highlight">
      <td class="pointer" onclick="navigateToTicket(${b.booking_id})">
        ${b.Hotel_Name} (${b.Room_Type})<br>${b.Hotel_City}
      </td>
      <td class="${color}">${status}</td>
      <td>Rs ${b.amount}</td>
      <td>${b.date_time} ${b.payment_status === "Completed"
        ? `<button class="down-btn" onclick="downloadBtn(${b.booking_id},'${b.booking_type}')">Download Ticket</button><button class="cancel-btn">Cancel Ticket</button>`
        : `<button class="down-btn yellow yellow-back" onclick='retryPayment("${b.order_id}", ${b.amount})'>Retry Payment</button>`}
      </td>
    </tr>`;
  });

  html += '</table>';
  return html;
}
</script>


</body>
</html>
