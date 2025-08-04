<?php
session_start();
$tab = $_GET['tab'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HindSafar | Book Flights, Hotels & Packages</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#0078FA',
          },
        },
      },
    };
  </script>
</head>

<body class="bg-gray-100 text-gray-800">

  <!-- Navbar -->
  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <div class="flex items-center space-x-2">
        <img src="./assets/images/logo.png" alt="logo" class="h-6" />
      </div>
      <div class="space-x-6 hidden md:flex">
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="./my-bookings.php" class="text-sm font-medium hover:text-primary">My Trips</a>
          <a href="./logout.php" class="text-sm font-medium hover:text-primary">Signout</a>
        <?php else: ?>
          <a href="#" class="text-sm font-medium hover:text-primary">Login or Create Account</a>
        <?php endif; ?>
        <div class="text-sm">INR | English</div>
      </div>
    </div>
  </header>

  <!-- Category Tabs -->
  <div class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center overflow-x-auto space-x-6">
      <a href="?tab=flights" class="tab-btn flex flex-col items-center <?php echo ($tab == 'flights') ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-primary'; ?>">
        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 14.25L10.5 21v-4.125L21.75 3.75 20.25 2.25 10.5 13.125V9L2.25 14.25z" />
        </svg>
        <span class="text-xs font-semibold">Flights</span>
      </a>

      <a href="?tab=hotels" class="tab-btn flex flex-col items-center <?php echo ($tab == 'hotels') ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-primary'; ?>">
        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16M4 6h16M4 18h16" />
        </svg>
        <span class="text-xs">Hotels</span>
      </a>

      <a href="?tab=packages" class="tab-btn flex flex-col items-center <?php echo ($tab == 'packages') ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-primary'; ?>">
        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-xs">Packages</span>
      </a>
    </div>
  </div>

  <!-- Dynamic Content Section -->
  <main class="max-w-7xl mx-auto mt-6 px-4" id="main-content">

    <?php
    // Include the selected tab page or show the default hero
    if ($tab === 'flights') {
      include('./flights2.php');
    } elseif ($tab === 'hotels') {
      include('./hotels.php');
    } elseif ($tab === 'packages') {
      include('./packages.php');
    } else {
      // Hero Section (default)
    ?>
      <section id="hero-section" class="relative bg-white overflow-hidden rounded-xl shadow-md">
        <div class="max-w-7xl mx-auto px-6 py-12 lg:py-20 grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
          <!-- Text Content -->
          <div class="space-y-6">
            <h1 class="text-4xl lg:text-5xl font-extrabold text-primary leading-tight">
              Explore the World with <span class="text-red-500">HindSafar</span>
            </h1>
            <p class="text-gray-600 text-lg">
              Book Flights, Hotels & Travel Packages all in one place. Start your journey with exclusive deals, fast booking, and 24/7 support.
            </p>
            <div class="space-x-4">
              <a href="?tab=flights" class="inline-block px-6 py-3 bg-primary text-white rounded-lg shadow hover:bg-blue-700 font-semibold transition">
                Book Now
              </a>
              <a href="?tab=packages" class="inline-block px-6 py-3 bg-white border border-primary text-primary rounded-lg hover:bg-gray-100 font-semibold transition">
                View Packages
              </a>
            </div>
          </div>

          <!-- Image -->
          <div class="flex justify-center">
            <img src="./assets/images/hero-travel.svg" alt="Travel Illustration" class="w-full max-w-md md:max-w-lg">
          </div>
        </div>

        <!-- Decorative shapes (optional) -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-red-100 rounded-full blur-2xl opacity-40"></div>
      </section>
    <?php
    }
    ?>

  </main>
</body>
</html>
