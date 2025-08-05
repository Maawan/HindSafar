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
  <link rel="icon" href="./assets/images/second.png" type="image/png" />
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

<body class="bg-gray-200 text-gray-800">

  <!-- Navbar -->
  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <div class="flex items-center space-x-2">
        <img src="./assets/images/logo.png" alt="logo" class="h-6" />
      </div>
      <div class="space-x-6 hidden md:flex">
        <a href="./contact-us.php" class="text-sm font-medium hover:text-primary">Raise a query</a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="./my-bookings.php" class="text-sm font-medium hover:text-primary">My Trips</a>
          <a href="./logout.php" class="text-sm font-medium hover:text-primary">Signout</a>
        <?php else: ?>
          <a href="../Hindsafar/login.html" class="text-sm font-medium hover:text-primary">Login or Create Account</a>
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
    if ($tab === 'flights') {
      include('./flights2.php');
    } elseif ($tab === 'hotels') {
      include('./hotels.php');
    } elseif ($tab === 'packages') {
      include('./packages.php');
    } else {
    ?>
      <!-- HERO SECTION -->
      <section class="relative bg-white overflow-hidden rounded-xl shadow-md">
        <div class="px-6 py-12 lg:py-20 grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
          <div class="space-y-6">
            <h1 class="text-4xl lg:text-5xl font-extrabold text-primary leading-tight">
              Explore the World with <span class="text-red-500">HindSafar</span>
            </h1>
            <p class="text-gray-600 text-lg">
              Book Flights, Hotels & Travel Packages all in one place. Start your journey with exclusive deals, fast booking, and 24/7 support.
            </p>
            <div class="space-x-4">
              <a href="?tab=flights" class="px-6 py-3 bg-primary text-white rounded-lg shadow hover:bg-blue-700 font-semibold transition">Book Now</a>
              <a href="?tab=packages" class="px-6 py-3 bg-white border border-primary text-primary rounded-lg hover:bg-gray-100 font-semibold transition">View Packages</a>
            </div>
          </div>
          <div class="flex justify-center">
            <img src="./assets/images/airplane.png" class="h-60" alt="Travel" class="w-full max-w-md md:max-w-lg">
          </div>
        </div>
      </section>

      <!-- REVIEWS SECTION -->
      <section class="my-12">
        <h2 class="text-2xl font-bold text-center mb-6">What Our Travelers Say</h2>
        <div class="grid md:grid-cols-3 gap-6">
          <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center space-x-4 mb-4">
              <img src="https://i.pravatar.cc/100?img=" class="w-12 h-12 rounded-full" />
              <div>
                <p class="font-semibold">Amita Roy </p>
                <p class="text-sm text-gray-500">★★★★★</p>
              </div>
            </div>
            <p class="text-sm text-gray-700">
              "Amazing service, smooth bookings, and great customer support. Highly recommend HindSafar!"
            </p>
          </div>
          <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center space-x-4 mb-4">
              <img src="https://i.pravatar.cc/100?img=1" class="w-12 h-12 rounded-full" />
              <div>
                <p class="font-semibold">Rahul </p>
                <p class="text-sm text-gray-500">★★★★★</p>
              </div>
            </div>
            <p class="text-sm text-gray-700">
              "HindSafar turned my trip into a breeze — highly recommend!"
            </p>
          </div>
          <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center space-x-4 mb-4">
              <img src="https://i.pravatar.cc/100?img=2" class="w-12 h-12 rounded-full" />
              <div>
                <p class="font-semibold">Joy </p>
                <p class="text-sm text-gray-500">★★★★★</p>
              </div>
            </div>
            <p class="text-sm text-gray-700">
              "Seamless experience from booking to boarding — HindSafar nailed it!"
            </p>
          </div>
        </div>
      </section>

      <!-- FEATURED PACKAGES -->
      <section class="my-12">
        <h2 class="text-2xl font-bold text-center mb-6">Top Travel Packages</h2>
        <div class="grid md:grid-cols-3 gap-6">
          <?php
          $packages = [
            ["title" => "Manali Getaway", "price" => "₹9,999", "img" => "https://manalitourplanner.com/wp-content/uploads/2023/11/Manali-Family-Tour-Packages-2.jpg"],
            ["title" => "Goa Beach Tour", "price" => "₹12,499", "img" => "https://www.aladdinholidays.com/wp-content/uploads/2024/09/goa-beach.jpg"],
            ["title" => "Kashmir Paradise", "price" => "₹15,999", "img" => "https://www.bestvoyage.in/upload/blog/kashmir_-_a_paradise_on_earth_-2022022102360764.jpg"],
          ];
          foreach ($packages as $p):
          ?>
            <div class="bg-white rounded-xl shadow overflow-hidden hover:shadow-lg transition">
              <img src="<?= $p['img'] ?>" alt="<?= $p['title'] ?>" class="w-full h-48 object-cover">
              <div class="p-5">
                <h3 class="text-xl font-semibold text-blue-700 mb-1"><?= $p['title'] ?></h3>
                <p class="text-gray-600 mb-2"><?= $p['price'] ?></p>
                <a href="?tab=packages" class="text-primary font-medium hover:underline">View Details</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- FOOTER -->
      
    <?php
    }
    ?>
  </main>
  <footer class="bg-white mt-12 pt-10 pb-6 border-t">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-4 gap-6 text-sm text-gray-600">
          <div>
            <img src="./assets/images/logo.png" class="h-10 mb-5" alt="" srcset="">
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
              <li><a href="./contact-us.php" class="hover:text-primary">Contact</a></li>
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
</body>

</html>