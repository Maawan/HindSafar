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
<title>Hotel Booking – HindSafar</title>
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

    input, select {
        width: 90%;
        padding: 0.6rem;
        border: 1px solid #ccc;
        border-radius: 6px;
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

    .hotel-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        background: #f9f9f9;
    }

    .hotel-card h4 {
        margin: 0 0 0.5rem;
    }

    @media (max-width: 768px) {
        .form-group {
            flex: 1 1 100%;
        }

        input, select {
            width: 100%;
        }
    }
</style>
</head>
<body>

<div class="navbar">
    <h2>HindSafar – Book a Hotel</h2>
    <div class="user">👤 <?php echo htmlspecialchars($name); ?></div>
</div>

<div class="container">
    <h3>Find Your Hotel</h3>
    <form id="hotelForm">
        <div class="form-group">
            <label for="city">Select City</label>
            <input list="cityList" id="city" name="city" required placeholder="Start typing city name" />
            <datalist id="cityList">
                <option value="Delhi">
                <option value="Mumbai">
                <option value="Bangalore">
                <option value="Hyderabad">
                <option value="Chennai">
                <option value="Shimla">
            </datalist>
        </div>

        <div class="form-group">
            <label for="roomType">Room Type</label>
            <select id="roomType" name="roomType" required>
                <option value="All">All</option>
                <option value="eco">Economical</option>
                <option value="deluxe">Deluxe</option>
                <option value="lux">Luxury</option>
            </select>
        </div>

        <div class="form-group">
            <label for="checkin">Check-in Date</label>
            <input type="date" id="checkin" name="checkin" required min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+60 days')); ?>" />
        </div>

        <div class="form-group">
            <label for="checkout">Check-out Date</label>
            <input type="date" id="checkout" name="checkout" required />
        </div>

        <div class="form-group">
            <label for="guests">Number of Rooms</label>
            <input type="number" id="guests" name="guests" min="1" value="1" required />
        </div>

        <div class="form-group" style="flex: 1 1 100%;">
            <button type="submit" class="search-btn">Search Hotels</button>
        </div>
    </form>

    <div id="results">
        <!-- Hotel results will be shown here -->
    </div>
</div>

<script>
document.getElementById("hotelForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const city = document.getElementById("city").value;
    const roomType = document.getElementById("roomType").value.toLowerCase();
    const checkin = document.getElementById("checkin").value;
    const checkout = document.getElementById("checkout").value;
    const guests = document.getElementById("guests").value;

    const resultContainer = document.getElementById("results");

    // Validation checks
    const validCities = ["Delhi", "Mumbai", "Bangalore", "Hyderabad", "Chennai","Shimla"];
    if (!validCities.includes(city)) {
        alert("Please select a valid city from the suggestions.");
        return;
    }

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

    // Make actual API call
    fetch("backend/api/hotels/search-hotels.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            city: city,
            room_type: roomType === "all" ? "all" : roomType,
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

        resultContainer.innerHTML = `<h3>Available Hotels in ${city}</h3>`;

        function formatDateVerbose(dateStr) {
            const dateObj = new Date(dateStr);
            const day = dateObj.getDate();
            const month = dateObj.toLocaleString('default', { month: 'long' });
            const year = dateObj.getFullYear();

            let suffix = "th";
            if (day % 10 === 1 && day !== 11) suffix = "st";
            else if (day % 10 === 2 && day !== 12) suffix = "nd";
            else if (day % 10 === 3 && day !== 13) suffix = "rd";

            return `${day}${suffix} of ${month}, ${year}`;
        }

        const checkinFormatted = formatDateVerbose(checkin);
        const checkoutFormatted = formatDateVerbose(checkout);

        resultContainer.innerHTML += `<p>🗓️ You selected ${diffDays} nights stay from ${checkinFormatted} to ${checkoutFormatted} for ${guests} Room(s).</p>`;

        if (hotels.length === 0) {
            resultContainer.innerHTML += `<p>❌ No rooms found for selected criteria.</p>`;
            return;
        }
//http://localhost/HindSafar/hotels/book.php?hotel_id=1&room_type_id=1&checkin_date=2025-07-14&checkout_date=2025-07-16&rooms=2
        hotels.forEach(hotel => {
            const totalPrice = parseFloat(hotel.price_per_night) * diffDays;
            const url = `hotels/book.php?hotel_id=${hotel["meta-data"].hotel_id}&room_type_id=${hotel["meta-data"].room_type_id}&checkin_date=${checkin}&checkout_date=${checkout}&rooms=${guests}`;
            resultContainer.innerHTML += `
                <div class="hotel-card">
                    <h4>🏨 ${hotel.hotel_name}</h4>
                    <p><strong>Type:</strong> ${hotel.room_type}</p>
                    <p><strong>Address:</strong> ${hotel.address}</p>
                    <p><strong>Price per Night:</strong> ₹${hotel.price_per_night}</p>
                    <p><strong>Total for ${diffDays} nights:</strong> ₹${totalPrice}</p>
                    <button onclick="window.location.href='${url}';">Book Now</button>
                </div>
            `;
        });
    })
    .catch(error => {
        console.error("Error:", error);
        resultContainer.innerHTML = `<p>❌ Error retrieving hotels. Please try again later.</p>`;
    });
});
</script>

</body>
</html>
