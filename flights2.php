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
    <section id="filters" class="mt-6 hidden">
      <div class="bg-white rounded-xl shadow-md p-4 flex flex-wrap gap-4 items-center">

        <!-- Sort by Price -->
        <div>
          <label class="text-xs font-semibold">Sort by</label>
          <select id="sortBy" class="mt-1 px-3 py-2 border rounded focus:outline-primary">
            <option value="priceLow">Price: Low to High</option>
            <option value="priceHigh">Price: High to Low</option>
          </select>
        </div>

        <!-- Filter by Airline -->
        <div>
          <label class="text-xs font-semibold">Airline</label>
          <select id="airlineFilter" class="mt-1 px-3 py-2 border rounded focus:outline-primary">
            <option value="">All Airlines</option>
          </select>
        </div>

        <!-- Filter by Departure Time -->
        <div>
          <label class="text-xs font-semibold">Departure Time</label>
          <select id="timeFilter" class="mt-1 px-3 py-2 border rounded focus:outline-primary">
            <option value="">Anytime</option>
            <option value="morning">Morning (5 AM - 12 PM)</option>
            <option value="afternoon">Afternoon (12 PM - 5 PM)</option>
            <option value="evening">Evening (5 PM - 9 PM)</option>
            <option value="night">Night (9 PM - 5 AM)</option>
          </select>
        </div>

      </div>
    </section>


    <section id="results" class="mt-10 space-y-4">
      <!-- Flight results will be shown here -->
    </section>
  </div>

  <script>
    let allFlights = [];
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
      resultContainer.innerHTML = `<p class='text-blue-700'>🔍 Loading flights...</p>`;

      try {
        const response = await fetch("backend/api/flights/search_flights.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            from,
            to,
            date,
            passengers
          })
        });

        const flights = await response.json();

        resultContainer.innerHTML = `<h3 class='text-xl font-semibold text-blue-800'>Available Flights on ${date}</h3>`;

        if (flights.length === 0) {
          resultContainer.innerHTML += `<p class='text-red-600'>❌ No flights found for the selected route and date.</p>`;
          return;
        }

        document.getElementById("filters").classList.remove("hidden");

        const airlineSet = [...new Set(flights.map(f => f.airline))];
        const airlineFilter = document.getElementById("airlineFilter");
        airlineFilter.innerHTML = `<option value="">All Airlines</option>`;
        airlineSet.forEach(airline => {
          airlineFilter.innerHTML += `<option value="${airline}">${airline}</option>`;
        });

        allFlights = flights;
        //renderFlights(flights , date);
        applyFilters();

      } catch (error) {
        console.error("Fetch Error:", error);
        resultContainer.innerHTML = `<p class='text-red-600'>⚠️ Something went wrong. Please try again later.</p>`;
      }
    });

    function renderFlights(flights, date) {
      const resultContainer = document.getElementById("results");
      resultContainer.innerHTML = `<h3 class='text-xl font-semibold text-blue-800'>Available Flights on ${date}</h3>`;

      // Find cheapest flight
      const cheapestPrice = Math.min(...flights.map(f => parseFloat(f.base_fare)));

      flights.forEach(flight => {
        const isCheapest = parseFloat(flight.base_fare) === cheapestPrice;

        const cabinText = flight.cabin_extra_allowed || flight.cabin_free_weight > 0 ?
          `✅ Cabin Allowed: ${flight.cabin_free_weight}kg free` :
          `❌ Cabin Not Allowed`;
        //console.log("Luggage allowrd -> " + flight.luggage_allowed + " "  + flight.luggage_free_weight);
        
        const luggageText = flight.luggage_extra_allowed > 0 ?
          `✅ Luggage Allowed: ${flight.luggage_free_weight}kg free` :
          `❌ Luggage Not Allowed`;

        // Airline logos
        let airline_url = "";
        if (flight.airline == "Vistara") airline_url = "https://upload.wikimedia.org/wikipedia/en/thumb/b/bd/Vistara_Logo.svg/1200px-Vistara_Logo.svg.png";
        else if (flight.airline == "Akasa Air") airline_url = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRJ3Gq_woK4rbx6iyGpcNLtyaO4ks5dmUjDpw&s";
        else if (flight.airline == "SpiceJet") airline_url = "https://logos-world.net/wp-content/uploads/2023/01/SpiceJet-Logo.jpg";
        else if (flight.airline == "AirAsia India") airline_url = "https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/AirAsia_Logo.svg/2560px-AirAsia_Logo.svg.png";
        else if (flight.airline == "Go First") airline_url = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRhhbRWH6ZUDU1CsHkNDlX8t_q4YzQyysOkFw&s";
        else if (flight.airline == "IndiGo") airline_url = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ4OEGUiBc_oFJGM9cd9yW_NQhLyzaWsaJDHg&s";
        else if (flight.airline == "GoAir") airline_url = "https://assets.planespotters.net/files/airlines/1/goair_72e0ed_opk.png";
        else if (flight.airline == "Air India") airline_url = "https://www.nicepng.com/png/detail/250-2508376_air-india-logo-air-india-airlines-logo.png";

        resultContainer.innerHTML += `
      <div class="bg-white rounded-xl border ${isCheapest ? 'border-green-500 ring-2 ring-green-300 border-2' : 'border-gray-300'} shadow-sm p-4 mb-4">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-3">
            <img src=${airline_url} class="w-10 h-10" alt="airline logo">
            <div>
              <p class="text-sm font-medium">${flight.airline}</p>
              <p class="text-xs text-gray-500">${flight.flight_id}</p>
              ${isCheapest ? `<span class="bg-green-500 text-white text-xs px-2 py-1 rounded">Cheapest</span>` : ""}
            </div>
          </div>
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
          <div class="text-center">
            <p class="text-lg font-bold ${isCheapest ? "text-green-600" : "text-blue-600"} ">₹${parseFloat(flight.base_fare).toFixed(2)}</p>
            <p class="text-xs text-gray-500">per adult</p>
          </div>
          <div class="text-right">
            <a href="flights/book.php?fl=${flight.flight_id}" class=" ${isCheapest ? "bg-green-600 hover:bg-green-700" : "bg-blue-600 hover:bg-blue-700"} text-white text-sm font-medium px-4 py-2 rounded-md">BOOK NOW</a>
            
          </div>
        </div>
        <div class="mt-3 border-t pt-3 text-xs text-gray-700">
          🧳 ${luggageText} &nbsp; 🎒 ${cabinText}
        </div>
      </div>
    `;
      });
    }

    // Listen for filter changes
    document.getElementById("sortBy").addEventListener("change", applyFilters);
    document.getElementById("airlineFilter").addEventListener("change", applyFilters);
    document.getElementById("timeFilter").addEventListener("change", applyFilters);

    function applyFilters() {
      console.log("Filter Triggered");
      
      let filtered = [...allFlights];

      // Airline filter
      const airlineVal = document.getElementById("airlineFilter").value;
      if (airlineVal) filtered = filtered.filter(f => f.airline === airlineVal);

      // Time filter
      const timeVal = document.getElementById("timeFilter").value;
      if (timeVal) {
        filtered = filtered.filter(f => {
          const hour = new Date(f.dep_time).getHours();
          if (timeVal === "morning") return hour >= 5 && hour < 12;
          if (timeVal === "afternoon") return hour >= 12 && hour < 17;
          if (timeVal === "evening") return hour >= 17 && hour < 21;
          if (timeVal === "night") return hour >= 21 || hour < 5;
          return true;
        });
      }

      // Sorting
      const sortVal = document.getElementById("sortBy").value;
      if (sortVal === "priceLow") filtered.sort((a, b) => parseFloat(a.base_fare) - parseFloat(b.base_fare));
      if (sortVal === "priceHigh") filtered.sort((a, b) => parseFloat(b.base_fare) - parseFloat(a.base_fare));

      renderFlights(filtered, document.getElementById("date").value);
    }
  </script>
</body>

</html>