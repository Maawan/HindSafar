<?php
session_start();
if (!isset($_SESSION['name'])) {
  header("Location: login.html");
  exit();
}
$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Flight Booking – HindSafar</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f2f7ff;
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

    .navbar h2 {
      margin: 0;
      font-size: 1.5rem;
    }

    .user {
      font-size: 0.95rem;
    }

    .container {
      max-width: 900px;
      margin: 2rem auto;
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .container h3 {
      margin-top: 0;
      color: #004aad;
    }

    form {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
    }

    label {
      display: block;
      font-weight: 500;
      margin-bottom: 0.5rem;
    }

    input {
      width: 90%;
      padding: 0.6rem;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    select {
      width: 100%;
    }

    .form-group {
      flex: 1 1 250px;
      min-width: 250px;
    }

    .search-btn {
      margin-top: 1rem;
      width: 100%;
      padding: 0.75rem;
      font-size: 1rem;
      background-color: #004aad;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .search-btn:hover {
      background-color: #003080;
    }

    #results {
      margin-top: 2rem;
    }

    .flight-card {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1rem;
      background: #f9f9f9;
    }

    .flight-card h4 {
      margin: 0 0 0.5rem;
    }

    @media (max-width: 768px) {
      .form-group {
        flex: 1 1 100%;
      }
    }
  </style>
</head>
<body>

  <div class="navbar">
    <h2>HindSafar – Book a Flight</h2>
    <div class="user">👤 <?php echo htmlspecialchars($name); ?></div>
  </div>

  <div class="container">
    <h3>Find Your Flight</h3>
    <form id="flightForm">
      <div class="form-group">
        <label for="from">From Airport</label>
        <input list="airportList" id="from" name="from" placeholder="Type departure airport" required />
      </div>

      <div class="form-group">
        <label for="to">To Airport</label>
        <input list="airportList" id="to" name="to" placeholder="Type destination airport" required />
      </div>

      <div class="form-group">
        <label for="date">Travel Date</label>
        <input type="date" id="date" required min="<?php echo date('Y-m-d'); ?>" />
      </div>

      <div class="form-group">
        <label for="passengers">Passenger Count</label>
        <input type="number" id="passengers" name="passengers" min="1" value="1" required />
      </div>

      <div class="form-group" style="flex: 1 1 100%;">
        <button type="submit" class="search-btn">Search Flights</button>
      </div>
    </form>

    <datalist id="airportList">
      <option value="Delhi">
      <option value="Mumbai">
      <option value="Amritsar">
      <option value="Bangalore">
      <option value="Hyderabad">
      <option value="Chennai">
      <option value="Kolkata">
      <option value="Ahmedabad">
      <option value="Goa">
      <option value="Pune">
    </datalist>

    <div id="results">
      <!-- Flight results will be shown here -->
    </div>
  </div>

  <script>
    document.getElementById("flightForm").addEventListener("submit", async function(e) {
      e.preventDefault();

      const from = document.getElementById("from").value.trim();
      const to = document.getElementById("to").value.trim();
      const date = document.getElementById("date").value;
      const passengers = parseInt(document.getElementById("passengers").value);

      if (from === to) {
        alert("Departure and arrival airports must be different.");
        return;
      }

      const resultContainer = document.getElementById("results");
      resultContainer.innerHTML = `<p>🔍 Loading flights...</p>`;

      try {
        const response = await fetch("backend/api/flights/search_flights.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ from, to, date, passengers })
        });

        const flights = await response.json();

        resultContainer.innerHTML = `<h3>Available Flights on ${date}</h3>`;

        if (flights.length === 0) {
          resultContainer.innerHTML += `<p>❌ No flights found for the selected route and date.</p>`;
          return;
        }

        flights.forEach(flight => {
          const cabinText = flight.cabin_extra_allowed || flight.cabin_free_weight > 0
            ? `✅ Cabin Allowed: ${flight.cabin_free_weight}kg free`
            : `❌ Cabin Not Allowed`;

          const luggageText = flight.luggage_allowed
            ? `✅ Luggage Allowed: ${flight.luggage_free_weight}kg free`
            : `❌ Luggage Not Allowed`;

          resultContainer.innerHTML += `
            <div class="flight-card">
              <h4>✈️ ${flight.airline} (${flight.flightID})</h4>
              <p><strong>From:</strong> ${flight.from_city} &nbsp; <strong>To:</strong> ${flight.to_city}</p>
              <p><strong>Departure:</strong> ${new Date(flight.dep_time).toLocaleString()}</p>
              <p><strong>Arrival:</strong> ${new Date(flight.arrival_time).toLocaleString()}</p>
              <p><strong>Seats Available:</strong> ${flight.seats_available}</p>
              <p><strong>Fare:</strong> ₹${parseFloat(flight.base_fare).toFixed(2)} per person</p>
              <p>🧳 ${luggageText}</p>
              <p>🎒 ${cabinText}</p>
              <button onclick="alert('Redirect to booking page for ${flight.flightID}')">Book Now</button>

              <button onclick="window.location.href = 'flights/book.php?fl=${flight.flightID}'">Book Now</button>
            </div>
          `;
        });

      } catch (error) {
        console.error("Fetch Error:", error);
        resultContainer.innerHTML = `<p>⚠️ Something went wrong. Please try again later.</p>`;
      }
    });
  </script>
</body>
</html>
