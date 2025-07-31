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
<title>Confirm Your Hotel Booking</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<style>
    body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f2f7ff; color: #333; }
    .navbar { background-color: #004aad; padding: 1rem 2rem; color: white; display: flex; justify-content: space-between; align-items: center; }
    .navbar h2 { margin: 0; font-size: 1.5rem; }
    .container { max-width: 900px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
    h3 { color: #004aad; margin-bottom: 1rem; }
    .summary { margin-top: 1rem; }
    .summary p { margin: 0.5rem 0; }
    .info-section { background: #f9f9f9; border-radius: 6px; padding: 1rem; margin-top: 2rem; }
    .info-section h4 { margin-top: 0; color: #004aad; }
    .confirm-btn { margin-top: 2rem; padding: 0.75rem 2rem; background: #004aad; color: white; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer; }
    .confirm-btn:hover { background: #003080; }
    .terms { font-size: 0.9rem; color: #555; margin-top: 1rem; }
    .loader { text-align: center; padding: 2rem; }
</style>
</head>
<body>

<div class="navbar">
    <h2>HindSafar – Confirm Booking</h2>
    <div>👤 <?php echo htmlspecialchars($name); ?></div>
</div>

<div class="container" id="bookingContainer">
    <div class="loader">
        <p>Loading booking details...</p>
    </div>
</div>

<script>
async function fetchBookingDetails() {
    const params = new URLSearchParams(window.location.search);
    const hotel_id = params.get("hotel_id");
    const room_type_id = params.get("room_type_id");
    const checkin_date = params.get("checkin_date");
    const checkout_date = params.get("checkout_date");
    const rooms = params.get("rooms");

    if (!hotel_id || !room_type_id || !checkin_date || !checkout_date || !rooms) {
        alert("Missing booking parameters. Redirecting back.");
        window.location.href = "hotels.php";
        return;
    }

    const requestData = {
        hotel_id: hotel_id,
        room_type_id: room_type_id,
        checkin_date: checkin_date,
        checkout_date: checkout_date,
        no_of_rooms: rooms
    };

    try {
        const response = await fetch('../backend/api/hotels/prebook.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(requestData)
        });

        const result = await response.json();
        const container = document.getElementById("bookingContainer");

        if (!result.success) {
            alert(result.message || "Rooms not available. Redirecting back.");
            window.location.href = "hotels.php";
            return;
        }

        const data = result.data;

        container.innerHTML = `
            <h3>Booking Summary</h3>
            <div class="summary">
                <p><strong>Hotel:</strong> ${data.hotel_name}</p>
                <p><strong>Address:</strong> ${data.hotel_address}</p>
                <p><strong>Room Type:</strong> ${data.room_type_name}</p>
                <p><strong>Price per Night:</strong> ₹${data.price_per_night}</p>
                <p><strong>Number of Rooms:</strong> ${data.no_of_rooms}</p>
                <p><strong>Check-in Date:</strong> ${data.checkin_date}</p>
                <p><strong>Check-out Date:</strong> ${data.checkout_date}</p>
                <p><strong>Total Nights:</strong> ${data.total_nights}</p>
                <p><strong>Total Cost:</strong> ₹${data.total_cost}</p>
            </div>

            <div class="info-section">
                <h4>Check-in & Check-out</h4>
                <p>Check-in Time: 2:00 PM onwards</p>
                <p>Check-out Time: Before 11:00 AM</p>
            </div>

            <div class="info-section">
                <h4>Guest Policies</h4>
                <p>✅ Valid ID proof required at the time of check-in.</p>
                <p>✅ Unmarried couples allowed (subject to hotel policy).</p>
                <p>✅ Early check-in or late check-out is subject to availability and may be chargeable.</p>
            </div>

            <div class="info-section">
                <h4>Cancellation Policy</h4>
                <p>❌ Free cancellation before 24 hours of check-in date.</p>
                <p>❌ Cancellation within 24 hours of check-in date will incur one night charge.</p>
            </div>

            <div class="info-section terms">
                <h4>Terms & Conditions</h4>
                <p>By confirming this booking, you agree to HindSafar's booking and cancellation policies. Please review your booking details carefully before proceeding to payment.</p>
            </div>

            <form action="confirm_booking.php" method="POST">
                <input type="hidden" name="hotel_id" value="${data.hotel_id}">
                <input type="hidden" name="room_type_id" value="${data.room_type_id}">
                <input type="hidden" name="checkin_date" value="${data.checkin_date}">
                <input type="hidden" name="checkout_date" value="${data.checkout_date}">
                <input type="hidden" name="rooms" value="${data.no_of_rooms}">
                <input type="hidden" name="total_price" value="${data.total_cost}">
                <button type="submit" class="confirm-btn">Confirm Booking & Proceed to Payment</button>
            </form>
        `;
    } catch (error) {
        console.error("Error fetching booking details:", error);
        alert("Something went wrong. Redirecting back.");
        window.location.href = "hotels.php";
    }
}

fetchBookingDetails();
</script>

</body>
</html>
