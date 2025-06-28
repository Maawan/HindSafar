<?php
session_start();
$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['name']) || !isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$flightID = $_GET['fl'] ?? '';
if (!$flightID) {
  echo "<script>alert('Missing flight ID. Redirecting...'); window.location.href='flights.php';</script>";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Book Flight – <?php echo htmlspecialchars($flightID); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #eef4ff;
      color: #333;
    }
    .navbar {
      background-color: #004aad;
      padding: 1rem 2rem;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .container {
      max-width: 1000px;
      margin: 2rem auto;
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    h2, h3 { color: #004aad; }
    .a {
      margin-top: 15px;
      display: flex;
      flex-wrap: wrap;
    }
    .aa {
      margin: 10px;
      display: flex;
      flex-direction: column;
    }
    .aa input, .aa select {
      margin-top: 5px;
      padding: 4px;
    }
    .cost-breakdown {
      background: #f1f8ff;
      padding: 1rem;
      border-radius: 6px;
      font-size: 1rem;
    }
    .add-btn, .book-btn, .delete-btn {
      background-color: #004aad;
      color: white;
      border: none;
      padding: 0.6rem 1rem;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 1rem;
    }
    .divider {
      margin: 1rem 0;
      height: 1px;
      background-color: #ccc;
    }
    .terms {
      background: #f9f9f9;
      padding: 1rem;
      border-radius: 6px;
      margin-top: 2rem;
      font-size: 0.9rem;
      line-height: 1.5;
    }
    .terms h4 {
      margin-top: 0;
      color: #004aad;
    }
    @media only screen and (max-width: 600px) {
      .a { flex-direction: column; }
      .aa { width: 100%; }
    }
  </style>
</head>
<body>

<div class="navbar">
  <h3>HindSafar – Book Flight <?php echo htmlspecialchars($flightID); ?></h3>
  <div>👤 <?php echo htmlspecialchars($_SESSION['name']); ?></div>
</div>

<div class="container">
  <h2>Flight Details</h2>
  <div id="flightDetails">Loading flight data...</div>

  <h2>Add Passengers</h2>
  <form id="passengerForm">
    <div id="passengerContainer"></div>
    <button type="button" class="add-btn" onclick="addPassenger()">+ Add Passenger</button>
  </form>

  <h3>Total Cost</h3>
  <div class="cost-breakdown" id="totalCost">₹0</div>

  <button class="book-btn" onclick="proceedToPay()">Proceed to Pay</button>

  <div class="terms">
    <h4>Terms & Conditions</h4>
    <ul>
      <li>All bookings are non-refundable unless specified as refundable in fare rules.</li>
      <li>Name changes on tickets are not permitted after booking confirmation.</li>
      <li>Extra baggage allowance is subject to airline policies and availability.</li>
      <li>Check-in counters close 45 minutes prior to departure for domestic flights.</li>
      <li>Government-issued photo ID is mandatory during check-in.</li>
      <li>In case of flight cancellation by airline, refund will be processed as per airline policy within 7-14 working days.</li>
      <li>Travel insurance, if purchased, is provided by third-party providers. Please read their policy documents carefully.</li>
      <li>By proceeding, you agree to the airline’s Conditions of Carriage and HindSafar booking policies.</li>
    </ul>
  </div>
</div>

<script>
  const flightID = "<?php echo $flightID; ?>";
  let passengerIndex = 0;
  let flight = null;

  async function fetchFlight() {
    try {
      const res = await fetch("../backend/api/flights/flight_details.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ flightID })
      });
      flight = await res.json();

      if (flight.error) {
        alert(flight.error);
        window.location.href = "flights.php";
        return;
      }

      renderFlightDetails();
      addPassenger(); // default 1

    } catch (err) {
      console.error(err);
      document.getElementById("flightDetails").innerText = "Failed to load flight details.";
    }
  }

  function renderFlightDetails() {
    const f = flight;
    const allowedCabin = f.cabin_extra_allowed == 1;
    const allowedLuggage = f.luggage_extra_allowed == 1;

    const depTime = new Date(f.dep_time);
    const arrTime = new Date(f.arrival_time);
    const durationMs = arrTime - depTime;
    const durationHrs = Math.floor(durationMs / (1000 * 60 * 60));
    const durationMins = Math.floor((durationMs % (1000 * 60 * 60)) / (1000 * 60));

    document.getElementById("flightDetails").innerHTML = `
      <p><strong>Airline:</strong> ${f.airline} (${f.flightID})</p>
      <p><strong>Route:</strong> ${f.from_city} ➝ ${f.to_city}</p>
      <p><strong>Departure:</strong> ${depTime.toLocaleString()}</p>
      <p><strong>Arrival:</strong> ${arrTime.toLocaleString()}</p>
      <p><strong>Duration:</strong> ${durationHrs}h ${durationMins}m</p>
      <p><strong>Base Fare:</strong> ₹${f.base_fare.toFixed(2)} per passenger</p>
      <p><strong>Included:</strong> 
        ${f.cabin_free_weight}kg 🧳 Cabin, 
        ${f.luggage_free_weight}kg 🧱 Luggage
      </p>
      <p>
        ${allowedCabin ? `💼 Extra Cabin: ₹${f.cabin_extra_price}/kg` : `❌ Extra Cabin Not Allowed`} &nbsp;&nbsp;
        ${allowedLuggage ? `📦 Extra Luggage: ₹${f.luggage_extra_price}/kg` : `❌ Extra Luggage Not Allowed`}
      </p>
    `;
  }

  function addPassenger() {
    const container = document.getElementById("passengerContainer");
    const index = passengerIndex++;

    const disableCabin = flight.cabin_extra_allowed != 1;
    const disableLuggage = flight.luggage_extra_allowed != 1;

    const div = document.createElement("div");
    div.id = `passenger-${index}`;
    div.innerHTML = `
      <div class="a">
        <div class="aa">
          <label>Name</label>
          <input name="passenger[${index}][name]" required />
        </div>
        <div class="aa">
          <label>Gender</label>
          <select name="passenger[${index}][gender]">
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>
        </div>
        <div class="aa">
          <label>Age</label>
          <input type="number" name="passenger[${index}][age]" required />
        </div>
        <div class="aa">
          <label>Cabin (kg)</label>
          <input type="number" name="passenger[${index}][cabin]" min="0"
                 ${disableCabin ? "disabled" : ""}
                 oninput="calculateCost()" />
        </div>
        <div class="aa">
          <label>Luggage (kg)</label>
          <input type="number" name="passenger[${index}][luggage]" min="0"
                 ${disableLuggage ? "disabled" : ""}
                 oninput="calculateCost()" />
        </div>
        <div class="aa delete-btn-container" style="align-self: end;">
          <button type="button" class="delete-btn" onclick="deletePassenger(${index})">Delete</button>
        </div>
      </div>
      <div class="cost-breakdown" id="cost-${index}">Cost: ₹0</div>
      <div class="divider"></div>
    `;
    container.appendChild(div);

    updateDeleteButtons();
    calculateCost();
  }

  function deletePassenger(index) {
    const div = document.getElementById(`passenger-${index}`);
    if (div) {
      div.remove();
      calculateCost();
      updateDeleteButtons();
    }
  }

  function updateDeleteButtons() {
    const passengers = document.querySelectorAll("#passengerContainer > div");
    const deleteButtons = document.querySelectorAll(".delete-btn");

    if (passengers.length <= 1) {
      deleteButtons.forEach(btn => btn.style.display = "none");
    } else {
      deleteButtons.forEach(btn => btn.style.display = "block");
    }
  }

  async function proceedToPay() {
  // Gather passenger data
  const form = document.getElementById("passengerForm");
  const data = new FormData(form);
  const passengers = [];

  for (let i = 0; i < passengerIndex; i++) {
    const passengerDiv = document.getElementById(`passenger-${i}`);
    if (!passengerDiv) continue;

    passengers.push({
      name: data.get(`passenger[${i}][name]`),
      gender: data.get(`passenger[${i}][gender]`),
      age: data.get(`passenger[${i}][age]`),
      cabin: data.get(`passenger[${i}][cabin]`) || 0,
      luggage: data.get(`passenger[${i}][luggage]`) || 0
    });
  }
  // Prepare final order data
  const orderData = {
    flightID: flightID,
    passengers: passengers,
    totalAmount: parseFloat(document.getElementById("totalCost").innerText.replace("₹","").replace(",",""))
  };

  console.log("Sending order data to backend:", orderData);

  try {
    const res = await fetch("../backend/api/flights/initiate_booking.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(orderData)
    });

    const result = await res.json();
    console.log("Order API Response:", result);

    if (result.success) {
      // Redirect to payment or initiate payment logic here
      alert("Order created. Proceeding to payment...");
      // Example: redirect with payment_id
      // window.location.href = `payment_page.php?payment_id=${result.payment_id}`;
    } else {
      alert("Failed to create order: " + (result.message || "Unknown error"));
    }

  } catch (err) {
    console.error(err);
    alert("Error while creating order. Please try again.");
  }
}


  function calculateCost() {
    const form = document.getElementById("passengerForm");
    const data = new FormData(form);
    let total = 0;

    for (let i = 0; i < passengerIndex; i++) {
      const passengerDiv = document.getElementById(`passenger-${i}`);
      if (!passengerDiv) continue;

      const luggage = parseFloat(data.get(`passenger[${i}][luggage]`) || 0);
      const cabin = parseFloat(data.get(`passenger[${i}][cabin]`) || 0);

      const base = parseFloat(flight.base_fare);
      const lugFree = parseFloat(flight.luggage_free_weight);
      const cabFree = parseFloat(flight.cabin_free_weight);
      const lugRate = parseFloat(flight.luggage_extra_price);
      const cabRate = parseFloat(flight.cabin_extra_price);

      const extraLuggageCost = Math.max(0, luggage - lugFree) * lugRate;
      const extraCabinCost = Math.max(0, cabin - cabFree) * cabRate;
      const totalPassengerCost = base + extraLuggageCost + extraCabinCost;

      total += totalPassengerCost;

      const costBox = document.getElementById(`cost-${i}`);
      if (costBox) {
        costBox.innerText = `Cost: ₹${totalPassengerCost.toFixed(2)} (Base: ₹${base}, Extra: ₹${(extraLuggageCost + extraCabinCost).toFixed(2)})`;
      }
    }

    document.getElementById("totalCost").innerText = `₹${total.toFixed(2)}`;
  }

  window.onload = fetchFlight;
</script>

</body>
</html>
