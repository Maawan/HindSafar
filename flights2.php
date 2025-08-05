<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HindSafar | Book Flights</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#0078FA'
          }
        }
      }
    }
  </script>
</head>

<body class="bg-gray-100 text-gray-800">
  <div class="max-w-7xl mx-auto mt-6 px-4">
    <div class="bg-white rounded-xl shadow-md p-6">
      

      <form id="flightForm">
        <div class="grid md:grid-cols-5 sm:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="text-xs font-semibold">From</label>
            <input list="airportList" id="from" name="from" placeholder="Delhi" required class="w-full mt-1 px-3 py-2 border rounded focus:outline-primary">
          </div>
          <div>
            <label class="text-xs font-semibold">To</label>
            <input list="airportList" id="to" name="to" placeholder="Bengaluru" required class="w-full mt-1 px-3 py-2 border rounded focus:outline-primary">
          </div>
          <div>
            <label class="text-xs font-semibold">Departure</label>
            <input type="date" id="date" min="<?php echo date('Y-m-d'); ?>" required class="w-full mt-1 px-3 py-2 border rounded focus:outline-primary">
          </div>
          <!-- <div>
            <label class="text-xs font-semibold">Return</label>
            <input type="date" class="w-full mt-1 px-3 py-2 border rounded focus:outline-primary">
          </div> -->
          <div>
            <label class="text-xs font-semibold">Travellers</label>
            <input type="number" id="passengers" name="passengers" min="1" value="1" required class="w-full mt-1 px-3 py-2 border rounded focus:outline-primary">
          </div>
        </div>

        <div class="flex justify-between items-center mt-4">
          <label class="flex items-center text-sm">
            <input type="checkbox" class="mr-2">
            Add Zero Cancellation
          </label>
          <button type="submit" class="bg-primary text-white font-semibold px-6 py-2 rounded hover:bg-blue-700">Search</button>
        </div>
      </form>
    </div>

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

    <section id="results" class="mt-10 space-y-4">
      <!-- Flight results will be shown here -->
    </section>
  </div>

  <script>
    document.getElementById("flightForm").addEventListener("submit", async function (e) {
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
      resultContainer.innerHTML = `<p class='text-blue-700'>🔍 Loading flights...</p>`;

      try {
        const response = await fetch("backend/api/flights/search_flights.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ from, to, date, passengers })
        });

        const flights = await response.json();

        resultContainer.innerHTML = `<h3 class='text-xl font-semibold text-blue-800'>Available Flights on ${date}</h3>`;

        if (flights.length === 0) {
          resultContainer.innerHTML += `<p class='text-red-600'>❌ No flights found for the selected route and date.</p>`;
          return;
        }

        flights.forEach(flight => {
  const cabinText = flight.cabin_extra_allowed || flight.cabin_free_weight > 0
    ? `✅ Cabin Allowed: ${flight.cabin_free_weight}kg free`
    : `❌ Cabin Not Allowed`;

  const luggageText = flight.luggage_allowed
    ? `✅ Luggage Allowed: ${flight.luggage_free_weight}kg free`
    : `❌ Luggage Not Allowed`;

    
    let airline_url = "";
    if(flight.airline == "Vistara"){
        airline_url = "https://upload.wikimedia.org/wikipedia/en/thumb/b/bd/Vistara_Logo.svg/1200px-Vistara_Logo.svg.png";
    }else if(flight.airline == "Akasa Air"){
        airline_url = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRJ3Gq_woK4rbx6iyGpcNLtyaO4ks5dmUjDpw&s";
    }else if(flight.airline == "SpiceJet"){
        airline_url = "https://logos-world.net/wp-content/uploads/2023/01/SpiceJet-Logo.jpg";
    }else if(flight.airline == "AirAsia India"){
        airline_url = "https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/AirAsia_Logo.svg/2560px-AirAsia_Logo.svg.png";
    }else if(flight.airline == "Go First"){
        airline_url = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRhhbRWH6ZUDU1CsHkNDlX8t_q4YzQyysOkFw&s";
    }else if(flight.airline == "IndiGo"){
        airline_url = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ4OEGUiBc_oFJGM9cd9yW_NQhLyzaWsaJDHg&s";
    }else if(flight.airline == "GoAir"){
        airline_url = "https://assets.planespotters.net/files/airlines/1/goair_72e0ed_opk.png";
    }else if(flight.airline == "Air India"){
        airline_url = "data:image/png;base64,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";
    }

  resultContainer.innerHTML += `
    <div class="bg-white rounded-xl border border-gray-300 shadow-sm p-4 mb-4">
      <div class="flex justify-between items-center">
        <!-- Airline & Flight Info -->
        <div class="flex items-center gap-3">
          <img src=${airline_url} class="w-10 h-10" alt="airline logo">
          <div>
            <p class="text-sm font-medium">${flight.airline}</p>
            <p class="text-xs text-gray-500">${flight.flight_id}</p>
          </div>
        </div>

        <!-- Departure / Duration / Arrival -->
        <div class="flex items-center gap-8">
          <div class="text-center">
            <p class="text-lg font-semibold">${new Date(flight.dep_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
            <p class="text-sm text-gray-500">${flight.from_city}</p>
          </div>

          <div class="text-center text-sm text-gray-600">
            <p class="font-medium">${Math.floor((new Date(flight.arrival_time) - new Date(flight.dep_time)) / (1000 * 60 * 60))}h ${(Math.floor((new Date(flight.arrival_time) - new Date(flight.dep_time)) / (1000 * 60)) % 60)}m</p>
            <p class="text-xs">Non Stop</p>
          </div>

          <div class="text-center">
            <p class="text-lg font-semibold">${new Date(flight.arrival_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
            <p class="text-sm text-gray-500">${flight.to_city}</p>
          </div>
        </div>

        <!-- Fare -->
        <div class="text-center">
          <p class="text-lg font-bold text-blue-600">₹${parseFloat(flight.base_fare).toFixed(2)}</p>
          <p class="text-xs text-gray-500">per adult</p>
        </div>

        <!-- CTA -->
        <div class="text-right">
          <a href="flights/book.php?fl=${flight.flight_id}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-md">BOOK NOW</a>
          <p class="text-xs text-blue-600 mt-2">Lock this price starting from ₹199</p>
        </div>
      </div>

      <!-- Offers & Info -->
      <div class="mt-3 border-t pt-3 text-xs text-gray-700">
        🧳 ${luggageText} &nbsp; 🎒 ${cabinText}
        
      </div>
    </div>
  `;
});


      } catch (error) {
        console.error("Fetch Error:", error);
        resultContainer.innerHTML = `<p class='text-red-600'>⚠️ Something went wrong. Please try again later.</p>`;
      }
    });
  </script>
</body>

</html>