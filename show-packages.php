<?php
// Get the package ID from query param
$packageId = isset($_GET['id']) ? intval($_GET['id']) : 0;

session_start();
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id || !isset($_SESSION['name'])) {
    header("Location: /HindSafar/login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Package Details – HindSafar</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <header class="bg-white shadow mb-6">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold">Package Details</h1>
      <a href="./dashboard.php?tab=packages" class="text-blue-600 hover:underline">Back to Packages</a>
    </div>
  </header>

  <main class="max-w-5xl mx-auto px-4 space-y-6" id="package-details">
    <div class="text-center py-10 text-gray-500">Loading package details...</div>
  </main>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
    const packageId = <?= $packageId ?>;

    async function fetchPackage() {
      const container = document.getElementById('package-details');
      try {
        const res = await fetch('./backend/api/packages/get-single-package.php?id=' + packageId);
        const pkg = await res.json();

        const banner = (pkg.banner && pkg.banner !== "null") 
          ? pkg.banner 
          : "https://via.placeholder.com/900x300?text=No+Banner";

        container.innerHTML = `
          <img src="${banner}" alt="Banner" class="w-full h-64 object-cover rounded-xl shadow-md">

          <div class="bg-white shadow rounded-xl p-6">
            <h2 class="text-2xl font-bold mb-2">${pkg.package_name}</h2>
            <p class="text-lg font-semibold text-blue-600 mb-4">₹${parseInt(pkg.price).toLocaleString()}</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
              <div>
                <p><strong>Location:</strong> ${pkg.location}</p>
                <p><strong>No. of Days:</strong> ${pkg.no_of_days}</p>
                <p><strong>No. of Persons:</strong> ${pkg.no_of_persons}</p>
                <p><strong>Hotel Type:</strong> ${pkg.hotel_type || 'N/A'}</p>
                <p><strong>Tags:</strong> ${pkg.tags || 'N/A'}</p>
              </div>
              <div>
                <p><strong>Category:</strong> ${pkg.category}</p>
                <p><strong>Contact:</strong> ${pkg.contact_number || 'N/A'}</p>
                <p><strong>Cancellation:</strong> ${pkg.cancellation_policy || 'N/A'}</p>
                <p><strong>Refund:</strong> ${pkg.refund_applicable === "1" ? '✅ Applicable' : '❌ Not Applicable'}</p>
              </div>
            </div>

            <div class="mt-6">
              <h3 class="font-semibold text-lg mb-2">What’s Included</h3>
              <ul class="list-disc ml-5 space-y-1 text-sm">
                ${pkg.flights_included === "1" ? "<li>✈️ Flights</li>" : ""}
                ${pkg.meals_included === "1" ? "<li>🍽️ Meals</li>" : ""}
                ${pkg.accommodation_included === "1" ? "<li>🏨 Accommodation</li>" : ""}
                ${pkg.location_commute_included === "1" ? "<li>🚌 Local Commute</li>" : ""}
                ${pkg.commute_airport_included === "1" ? "<li>🚐 Airport Transfers</li>" : ""}
              </ul>
            </div>

            <div class="mt-6">
              <h3 class="font-semibold text-lg mb-2">Destinations Covered</h3>
              <p class="text-sm">${pkg.destination_covered}</p>
            </div>

            <div class="mt-6">
              <h3 class="font-semibold text-lg mb-2">Additional Info</h3>
              <p class="text-sm">${pkg.additional_info || 'None'}</p>
            </div>

            <div class="mt-6">
              <label class="block mb-2 font-semibold text-sm">Select Start Date</label>
              <input type="date" id="start-date" class="w-full max-w-xs border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <div class="mt-6">
              <button onclick="payNow(${pkg.price}, '${pkg.package_id}')" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded text-sm font-semibold">
                Pay Now
              </button>
            </div>
          </div>
        `;
      } catch (err) {
        console.error(err);
        container.innerHTML = '<div class="text-red-500 text-center">Failed to load package.</div>';
      }
    }

    async function payNow(amount, packageId) {
      const date = document.getElementById('start-date').value;
      if (!date) {
        alert("Please select a start date before proceeding.");
        return;
      }

      // ⚠️ You can hook up Razorpay or pass to booking confirmation here
    //   alert(`Proceeding to payment of ₹${amount} for package ID: ${packageId} on ${date}`);
      const object = {
        package_id : packageId,
        start_date : date
      }
      try{
        const res = await fetch("./backend/api/packages/initiate-order.php", {
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
                order_id : result.payment_id,
                callback_url : "http://localhost/Hindsafar/verify.php?type=package"
                }
                let rzp = new Razorpay(options);
                rzp.open();
            } else {
                alert("Failed to create order: " + (result.message || "Unknown error"));
            }
      }catch(error){

      }
      
    }

    fetchPackage();
  </script>
</body>
</html>
