<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Your Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-10 px-4">
        <h1 class="text-2xl font-bold mb-6">Complete your booking</h1>

        <!-- Page Grid Layout -->
        <div class="md:flex md:gap-6">

            <!-- Left Section: Flight & Passenger Info -->
            <div class="md:w-2/3 flex flex-col gap-6">

                <!-- Flight Summary -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <!-- <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
            Your flight goes to Hindon Airport, which is 32 km away from Indira Gandhi International Airport.
          </div> -->

                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h2 class="text-xl font-bold">Kolkata → Ghaziabad</h2>
                            <p class="text-sm text-gray-500">Wednesday, Aug 6 | Non Stop • 2h 10m</p>
                        </div>
                        <!-- <a href="#" class="text-blue-500 text-sm font-semibold">View Fare Rules</a> -->
                    </div>

                    <div class="flex items-center gap-2 mb-2">
                        <img src="https://logos-world.net/wp-content/uploads/2023/01/SpiceJet-Logo.jpg" class="w-6 h-6" alt="Airline Logo">
                        <span class="font-semibold">IndiGo</span>
                        <span class="text-gray-500">6E 2588</span>
                        <!-- <span class="text-gray-500">Airbus A320</span> -->
                    </div>

                    <div class="border rounded-lg p-4 bg-gray-50 mb-3">
                        <div class="flex justify-between">
                            <div>
                                <p class="font-semibold">05:50</p>
                                <p class="text-sm text-gray-500">Kolkata </p>
                            </div>
                            <div class="text-center text-sm text-gray-600">2h 10m</div>
                            <div>
                                <p class="font-semibold">08:00</p>
                                <p class="text-sm text-gray-500">Ghaziabad </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 text-sm text-gray-700 mb-3">
                        <div>🧳 <strong>Cabin Baggage:</strong> 7 Kgs (1 piece only) / Adult</div>
                        <div>🛄 <strong>Check-In Baggage:</strong> 15 Kgs (1 piece only) / Adult</div>
                        <!-- <a href="#" class="ml-auto text-blue-600 font-semibold">ADD BAGGAGE</a> -->
                    </div>

                    <div class="text-sm text-gray-500 bg-purple-50 p-3 rounded-lg">
                        Got excess baggage? Don’t stress, buy extra check-in baggage allowance from passenger list
                    </div>
                </div>

                <!-- Passenger Details -->
                <!-- ... keep existing head and layout ... -->

<!-- Passenger Details -->
<div class="bg-white rounded-lg shadow-md p-6">
  <h3 class="text-lg font-bold mb-4">Passenger Details</h3>

  <form id="passengerForm" class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <input type="text" name="name" placeholder="Full Name" class="border rounded-lg px-4 py-2 w-full" required>
      <input type="number" name="age" placeholder="Age" class="border rounded-lg px-4 py-2 w-full" required>
      <select name="gender" class="border rounded-lg px-4 py-2 w-full" required>
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
      </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <input type="number" name="checkin" placeholder="Check-in Baggage (Kg)" class="border rounded-lg px-4 py-2 w-full" required>
      <input type="number" name="cabin" placeholder="Cabin Baggage (Kg)" class="border rounded-lg px-4 py-2 w-full" required>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Passenger</button>
  </form>

  <ul id="passengerList" class="mt-6 space-y-3"></ul>
</div>

            </div>

            <!-- Right Section: Fare Summary -->
            <div class="md:w-1/3">
                <div class="bg-white rounded-lg shadow-md p-6 mt-6 md:mt-0">
                    <h4 class="text-lg font-bold mb-4">Fare Summary</h4>
                    <div class="flex justify-between mb-2">
                        <span>Base Fare</span><span>₹3,173</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Taxes and Surcharges</span><span>₹1,260</span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total Amount</span><span>₹4,433</span>
                    </div>
                    <button class="mt-6 w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold">Proceed to Pay</button>
                </div>
            </div>

        </div>
    </div>

    <script>
        const form = document.getElementById("passengerForm");
const list = document.getElementById("passengerList");

form.addEventListener("submit", (e) => {
  e.preventDefault();
  const name = form.name.value;
  const age = form.age.value;
  const gender = form.gender.value;
  const checkin = form.checkin.value;
  const cabin = form.cabin.value;

  const li = document.createElement("li");
  li.className = "flex justify-between items-center border px-4 py-2 rounded-lg bg-gray-50";
  li.innerHTML = `
    <div>
      <p><strong>${name}</strong>, ${age} (${gender})</p>
      <p class="text-sm text-gray-600">Check-in: ${checkin} Kg | Cabin: ${cabin} Kg</p>
    </div>
    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">Delete</button>
  `;
  list.appendChild(li);

  form.reset();
});

    </script>
</body>

</html>