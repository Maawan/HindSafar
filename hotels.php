<?php
// session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hotels – HindSafar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .menu-icon {
            filter: grayscale(100%);
        }

        .active-tab {
            color: #0072ff;
            border-bottom: 3px solid #0072ff;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <!-- Hotel Search Form -->
    <div class="bg-white mt-6 mx-4 md:mx-20 rounded-xl shadow-lg p-6">
        <form id="hotelSearchForm" action="search-hotels.php" method="GET" class="grid md:grid-cols-6 gap-4">
            <!-- City -->
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700">City, Property Name Or Location</label>
                <input name="city" value="Shimla"  class="w-full border rounded-md p-3 mt-1 font-bold text-lg" required />
                <p class="text-sm text-gray-500 mt-1">India</p>
            </div>

            <!-- Check-In -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Check-In</label>
                <input type="date" name="checkin" value="2025-08-03" class="w-full border rounded-md p-3 mt-1" />
            </div>

            <!-- Check-Out -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Check-Out</label>
                <input type="date" name="checkout" value="2025-08-05" class="w-full border rounded-md p-3 mt-1" />
            </div>

            <!-- Rooms & Guests -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Rooms & Guests</label>
                <input name="rooms" type="number" value="1" class="w-full border rounded-md p-3 mt-1" />
            </div>

            <!-- Room Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Room Type</label>
                <select name="room_type" class="w-full border rounded-md p-3 mt-1">
                    <option value="All">All</option>
                    <option value="Economy">Economy</option>
                    <option value="Luxury">Luxury</option>
                    <option value="Deluxe">Deluxe</option>
                </select>
            </div>
        </form>

        <!-- Search Button -->
        <div class="flex justify-end mt-6">
            <button type="submit" onclick="search()" class="bg-blue-600 text-white px-12 py-3 text-xl rounded-full hover:bg-blue-700 transition">
                SEARCH
            </button>
        </div>
    </div>

    <div id="results" class="px-4 md:px-20 py-10">
        <!-- Hotel results will be shown here -->
    </div>

    <script>
        function search() {
            const form = document.getElementById('hotelSearchForm');
            const formData = new FormData(form);
            const values = Object.fromEntries(formData.entries());
            const city = values['city'];
            const checkin = values['checkin'];
            const checkout = values['checkout'];
            let roomType = values['room_type'];
            const guests = values['rooms'];

            const resultContainer = document.getElementById("results");

            const checkinDate = new Date(checkin);
            const checkoutDate = new Date(checkout);
            const today = new Date();
            const sixtyDaysLater = new Date();
            sixtyDaysLater.setDate(today.getDate() + 60);

            if (checkinDate > sixtyDaysLater) {
                alert("Check-in date must be within the next 60 days.");
                return;
            }

            if (checkoutDate <= checkinDate) {
                alert("Check-out date must be after check-in date.");
                return;
            }

            const diffTime = Math.abs(checkoutDate - checkinDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays > 20) {
                alert("You can book a hotel for a maximum of 20 days.");
                return;
            }

            resultContainer.innerHTML = `<p>🔍 Loading hotels...</p>`;

            fetch("backend/api/hotels/search-hotels.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        city: city,
                        room_type: roomType.toLowerCase(),
                        checkin_date: checkin,
                        checkout_date: checkout,
                        no_of_rooms: parseInt(guests)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        resultContainer.innerHTML = `<p>❌ ${data.message}</p>`;
                        return;
                    }

                    const hotels = data.data;

                    function formatDateVerbose(dateStr) {
                        const dateObj = new Date(dateStr);
                        const day = dateObj.getDate();
                        const month = dateObj.toLocaleString('default', {
                            month: 'long'
                        });
                        const year = dateObj.getFullYear();

                        let suffix = "th";
                        if (day % 10 === 1 && day !== 11) suffix = "st";
                        else if (day % 10 === 2 && day !== 12) suffix = "nd";
                        else if (day % 10 === 3 && day !== 13) suffix = "rd";

                        return `${day}${suffix} of ${month}, ${year}`;
                    }

                    const checkinFormatted = formatDateVerbose(checkin);
                    const checkoutFormatted = formatDateVerbose(checkout);

                    resultContainer.innerHTML = `<h2 class='text-2xl font-semibold mb-4'>Available Hotels in ${city}</h2>`;
                    resultContainer.innerHTML += `<p class='mb-6 text-gray-700'>🗓️ ${diffDays} nights stay from ${checkinFormatted} to ${checkoutFormatted} for ${guests} Room(s).</p>`;

                    if (hotels.length === 0) {
                        resultContainer.innerHTML += `<p>❌ No rooms found for selected criteria.</p>`;
                        return;
                    }

                    resultContainer.innerHTML += `<div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">`;

                    hotels.forEach(hotel => {
                        
                        const totalPrice = parseFloat(hotel.price_per_night) * diffDays;
                        const url = `hotels/book.php?hotel_id=${hotel["meta-data"].hotel_id}&room_type_id=${hotel["meta-data"].room_type_id}&checkin_date=${checkin}&checkout_date=${checkout}&rooms=${guests}`;
                        let roomType = hotel.room_type;
                        if(hotel.room_type == "eco"){
                            roomType = "Economy";
                        }else if(hotel.room_type == "lux"){
                            roomType = "Luxary";
                        }else if(hotel.room_type == "del"){
                            roomType = "Deluxe";
                        }
                        resultContainer.innerHTML += `
                  <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition flex mb-4">
  <!-- Image Section (1/3 Width) -->
  <div class="w-2/5">
    <img src="${hotel.photo_url}" alt="Hotel Photo" class="h-full w-full object-cover">
  </div>

  <!-- Details Section (2/3 Width) -->
  <div class="w-full flex justify-between ">
    <!-- Text Details -->
    <div class="p-5 flex flex-col justify-between">
      <div>
        <h3 class="text-xl font-semibold text-blue-700 mb-1">${hotel.hotel_name}</h3>
        <p class="text-sm text-gray-500 mb-1">${hotel.address}</p>
        <p class="text-sm text-gray-600 mb-1"><strong>Room Type:</strong> ${roomType}</p>
        <p class="text-sm text-gray-600 mb-2"><strong>Price/Night:</strong> ₹${hotel.price_per_night}</p>
        <p class="text-green-600 font-semibold">Total for ${diffDays} nights: ₹${totalPrice}</p>
      </div>
    </div>

    <!-- Book Now Button -->
    <div class="p-5 flex items-end">
      <button onclick="window.location.href='${url}'" class="bg-blue-600 text-white px-5 py-2 rounded-full hover:bg-blue-700 transition">
        Book Now
      </button>
    </div>
  </div>
</div>

              `;
                    });

                    resultContainer.innerHTML += `</div>`;

                })
                .catch(error => {
                    console.error("Error:", error);
                    resultContainer.innerHTML = `<p>❌ Error retrieving hotels. Please try again later.</p>`;
                });
        }
    </script>

</body>

</html>