<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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

  <!-- Top Navigation Menu -->
  

  <!-- Hotel Search Form -->
  <div class="bg-white mt-6 mx-4 md:mx-20 rounded-xl shadow-lg p-6">
    

    <form action="search-hotels.php" method="GET" class="grid md:grid-cols-5 gap-4">
      <!-- City -->
      <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700">City, Property Name Or Location</label>
        <input name="city" value="Surat" class="w-full border rounded-md p-3 mt-1 font-bold text-lg" required />
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
        <input name="rooms" type="numbers" value="1" class="w-full border rounded-md p-3 mt-1"  />
      </div>
    </form>

    <!-- Price Filter -->
    

    <!-- Trending Searches -->
    

    <!-- Search Button -->
    <div class="flex justify-end mt-6">
      <button type="submit" class="bg-blue-600 text-white px-12 py-3 text-xl rounded-full hover:bg-blue-700 transition">
        SEARCH
      </button>
    </div>
  </div>

</body>
</html>
