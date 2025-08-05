<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id || !isset($_SESSION['name'])) {
    header("Location: /Hindsafar/login.html");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Confirm Your Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200">
<div class="container mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold mb-6">Complete your booking</h1>

    <div class="md:flex md:gap-6">
        <!-- Left: Flight Summary + Passenger Details -->
        <div class="md:w-2/3 flex flex-col gap-6">

            <!-- Flight Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 id="route" class="text-xl font-bold">Loading...</h2>
                        <p id="dateAndDuration" class="text-sm text-gray-500"></p>
                    </div>
                </div>

                <div class="flex items-center gap-2 mb-2">
                    <img id="logoo" src="https://logos-world.net/wp-content/uploads/2023/01/SpiceJet-Logo.jpg" class="w-6 h-6" alt="Airline Logo">
                    <span id="airlineName" class="font-semibold"></span>
                    <span id="flightCode" class="text-gray-500"></span>
                </div>

                <div class="border rounded-lg p-4 bg-gray-50 mb-3">
                    <div class="flex justify-between">
                        <div>
                            <p id="depTime" class="font-semibold"></p>
                            <p id="fromCity" class="text-sm text-gray-500"></p>
                        </div>
                        <div class="text-center text-sm text-gray-600" id="flightDuration"></div>
                        <div>
                            <p id="arrTime" class="font-semibold"></p>
                            <p id="toCity" class="text-sm text-gray-500"></p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-6 text-sm text-gray-700 mb-3">
                    <div>🧳 <strong>Cabin Baggage:</strong> <span id="cabinFree">-</span> Kg / Adult</div>
                    <div>🛄 <strong>Check-In Luggage:</strong> <span id="luggageFree">-</span> Kg / Adult</div>
                </div>

                <div class="text-sm text-gray-500 bg-purple-50 p-3 rounded-lg">
                    Got excess baggage? Don’t stress, buy extra check-in baggage allowance from passenger list.
                </div>
            </div>

            <!-- Passenger Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-4">Passenger Details</h3>
                <form id="passengerForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input type="text" name="name" placeholder="Full Name" class="border rounded-lg px-4 py-2 w-full" required />
                        <input type="number" name="age" placeholder="Age" class="border rounded-lg px-4 py-2 w-full" required />
                        <select name="gender" class="border rounded-lg px-4 py-2 w-full" required>
                            <option value="">Select Gender</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 mt-2 md:grid-cols-2 gap-4">
                        <input type="number" name="cabin" id="cabinInput" placeholder="Cabin Baggage (Kg)" class="border rounded-lg px-4 py-2 w-full" />
                        <input type="number" name="checkin" id="checkinInput" placeholder="Check-in Luggage (Kg)" class="border rounded-lg px-4 py-2 w-full" />
                    </div>
                    <button type="submit" class="bg-blue-600 mt-4 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Passenger</button>
                </form>
                <ul id="passengerList" class="mt-6 space-y-3"></ul>
            </div>
        </div>

        <!-- Right: Fare Summary -->
        <div class="md:w-1/3">
            <div class="bg-white rounded-lg shadow-md p-6 mt-6 md:mt-0">
                <h4 class="text-lg font-bold mb-4">Fare Summary</h4>
                <div class="flex justify-between mb-2">
                    <span>Base Fare</span><span id="baseFare">₹0.00</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span>Extra weight cost</span><span id="extraCharge">₹0.00</span>
                </div>
                <hr class="my-2">
                <div class="flex justify-between text-lg font-bold">
                    <span>Total Amount</span><span id="totalFare">₹0.00</span>
                </div>
                <button class="mt-6 w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold" onclick="pay()">Proceed to Pay</button>
            </div>
        </div>
    </div>
</div>
<footer class="bg-white mt-12 pt-10 pb-6 border-t">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-4 gap-6 text-sm text-gray-600">
          <div>
            <img src="../assets/images/logo.png" class="h-10 mb-5" alt="" srcset="">
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
    const flightID = "<?php echo $flightID; ?>";
    let flight;
    let extraCharge = 0;
    let passengerCount = 0;
    let passengers = [];
    let tot = 0;



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
        } catch (err) {
            console.error(err);
            alert("Error fetching flight details.");
        }
    }

    function renderFlightDetails() {
        const dep = new Date(flight.dep_time);
        const arr = new Date(flight.arrival_time);
        const durationMs = arr - dep;
        const h = Math.floor(durationMs / 3600000);
        const m = Math.floor((durationMs % 3600000) / 60000);

        document.getElementById("route").innerText = `${flight.from_city} → ${flight.to_city}`;
        document.getElementById("dateAndDuration").innerText = `${dep.toDateString()} | Non Stop • ${h}h ${m}m`;
        document.getElementById("flightDuration").innerText = `${h}h ${m}m`;
        document.getElementById("airlineName").innerText = flight.airline;
        document.getElementById("flightCode").innerText = flight.flight_id;
        document.getElementById("depTime").innerText = dep.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        document.getElementById("arrTime").innerText = arr.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        document.getElementById("fromCity").innerText = flight.from_city;
        document.getElementById("toCity").innerText = flight.to_city;
        document.getElementById("cabinFree").innerText = flight.cabin_free_weight;
        document.getElementById("luggageFree").innerText = flight.luggage_free_weight;

        const base = parseFloat(flight.base_fare);
        document.getElementById("baseFare").innerText = `₹${base.toFixed(2)}`;
        updateFareSummary();

        // Disable inputs based on allowance
        document.getElementById("checkinInput").disabled = flight.luggage_extra_allowed == 0;
        document.getElementById("cabinInput").disabled = flight.cabin_extra_allowed == 0;

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
        const item = document.getElementById("logoo");
        item.setAttribute("src",airline_url);


    }

    function updateFareSummary() {
        let base = parseFloat(flight.base_fare);
        if(passengerCount > 1){
            base = parseFloat(flight.base_fare) * passengerCount;
        }
        document.getElementById("baseFare").innerText = `₹${base.toFixed(2)}`;
        
        const total = base + extraCharge;
        tot = total;
        document.getElementById("extraCharge").innerText = `₹${extraCharge.toFixed(2)}`;
        document.getElementById("totalFare").innerText = `₹${total.toFixed(2)}`;
    }

    const form = document.getElementById("passengerForm");
    const list = document.getElementById("passengerList");



    form.addEventListener("submit", (e) => {
        e.preventDefault();
        const name = form.name.value.trim();
        const age = parseInt(form.age.value);
        const gender = form.gender.value;
        const checkin = parseFloat(form.checkin.value || 0);
        const cabin = parseFloat(form.cabin.value || 0);

        // Calculate baggage cost
        let extra = 0;

        if (flight.luggage_extra_allowed && checkin > flight.luggage_free_weight) {
            const over = checkin - flight.luggage_free_weight;
            extra += over * parseFloat(flight.luggage_extra_price);
        }

        if (flight.cabin_extra_allowed && cabin > flight.cabin_free_weight) {
            const over = cabin - flight.cabin_free_weight;
            extra += over * parseFloat(flight.cabin_extra_price);
        }

        extraCharge += extra;
        // updateFareSummary();

        const li = document.createElement("li");
        li.className = "flex justify-between items-center border px-4 py-2 rounded-lg bg-gray-50";
        li.innerHTML = `
            <div>
                <p><strong>${name}</strong>, ${age} (${gender})</p>
                <p class="text-sm text-gray-600">Cabin Bag: ${cabin} Kg | Check-in Luggage: ${checkin} Kg</p>
            </div>
            <button onclick="this.parentElement.remove(); adjustFare(${extra});" class="text-red-500 hover:text-red-700">Delete</button>
        `;
        // const name = form.name.value.trim();
        // const age = parseInt(form.age.value);
        // const gender = form.gender.value;
        // const checkin = parseFloat(form.checkin.value || 0);
        // const cabin = parseFloat(form.cabin.value || 0);
        passengers.push({
            name,
            gender,
            age,
            cabin,
            luggage: checkin
        })
        list.appendChild(li);
        passengerCount++;
        updateFareSummary();
        form.reset();
    });

    function adjustFare(amount) {
        extraCharge -= amount;
        passengerCount--;
        passengers.pop();
        updateFareSummary();
    }

    async function pay(){
        if(passengers.length == 0){
            alert("Fill the details for passengers");
            return ;
        }
        const object = {
            flight_id : flight.flight_id,
            passengers : passengers,
            totalAmount : tot
        }
        console.log(object);
        document.getElementById("paymentModal").classList.remove("hidden");
        try {
            const res = await fetch("../backend/api/flights/initiate_booking.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(object)
            });

            const result = await res.json();

            if (result.success) {
                let options = {
                key : "rzp_test_tN2HjlxfDwX4rW",
                amount : result.amount,
                currency : "INR",
                name : "HindSafar Online Booking Pvt Ltd",
                description : "Pay for your order",
                image: "https://res.cloudinary.com/duklzb1ww/image/upload/v1754343126/logo_qcve8t.png",
                order_id : result.payment_id,
                callback_url : "http://localhost/Hindsafar/verify.php?type=flight",
                modal: {
                    ondismiss: function () {
                        // Hide loader when modal is closed
                        document.getElementById("paymentModal").classList.add("hidden");
                        console.log("User closed Razorpay checkout.");
                    }
                }

                }
                let rzp = new Razorpay(options);
                rzp.open();
            } else {
                alert("Failed to create order: " + (result.message || "Unknown error"));
                document.getElementById("paymentModal").classList.add("hidden");
        
            }

        } catch (err) {
            console.error(err);
            document.getElementById("paymentModal").classList.add("hidden");
            alert("Error while creating order. Please try again." + err);
        }

        
    }

    fetchFlight();
</script>

</body>
</html>
