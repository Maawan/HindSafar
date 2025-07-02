<?php
session_start();

// Check if user is logged in
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HindSafar Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f4f7fa;
      color: #333;
    }

    /* Navbar */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #004aad;
      color: white;
      padding: 1rem 2rem;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar h1 {
      margin: 0;
      font-size: 1.5rem;
    }

    .user-menu {
      position: relative;
      cursor: pointer;
      user-select: none;
    }

    .user-dropdown {
      display: none;
      position: absolute;
      right: 0;
      top: 2.5rem;
      background: white;
      color: #333;
      border-radius: 6px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      min-width: 160px;
    }

    .user-dropdown a {
      display: block;
      padding: 0.75rem 1rem;
      text-decoration: none;
      color: #333;
      border-bottom: 1px solid #eee;
    }

    .user-dropdown a:last-child {
      border-bottom: none;
    }

    .user-dropdown a:hover {
      background-color: #f5f5f5;
    }

    .user-dropdown.show {
      display: block;
    }

    /* Greeting Section */
    .greeting {
      text-align: center;
      padding: 2rem 1rem;
    }

    .greeting h2 {
      font-size: 2rem;
      margin-bottom: 1rem;
    }

    /* Feature Cards */
    .options {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 1.5rem;
      padding: 2rem 1rem;
    }

    .card {
      background: white;
      padding: 1.5rem;
      width: 300px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.2s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card h3 {
      margin-bottom: 0.5rem;
    }

    .card p {
      font-size: 0.95rem;
      color: #555;
    }

    .card button {
      margin-top: 1rem;
      padding: 0.5rem 1rem;
      background-color: #004aad;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .card button:hover {
      background-color: #003080;
    }

    footer {
      text-align: center;
      padding: 1rem;
      background: #f1f1f1;
      font-size: 0.9rem;
      color: #666;
      margin-top: 2rem;
    }

    @media (max-width: 768px) {
      .options {
        flex-direction: column;
        align-items: center;
      }

      .card {
        width: 90%;
      }

      .greeting h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>

  <div class="navbar">
    <h1>HindSafar</h1>
    <div class="user-menu">
      <span>👤 <?php echo htmlspecialchars($name); ?></span>
      <div class="user-dropdown">
        <a href="./my-bookings.php">My Bookings</a>
        <a href="./contact-us.php">Contact Us</a>
        <a href="./logout.php">Logout</a>
      </div>
    </div>
  </div>

  <section class="greeting">
    <h2>Welcome back, <?php echo htmlspecialchars($name); ?>!</h2>
    <p>Where would you like to go today?</p>
  </section>

  <section class="options">
    <div class="card">
      <h3>✈️ Flight Booking</h3>
      <p>Search and book domestic or international flights easily.</p>
      <button onclick="goTo('flights.php')">Book Flight</button>
    </div>

    <div class="card">
      <h3>🏨 Hotel Booking</h3>
      <p>Find hotels, compare prices, and book rooms with ease.</p>
      <button onclick="goTo('hotels.php')">Book Hotel</button>
    </div>

    <div class="card">
      <h3>🎒 Travel Deals</h3>
      <p>Discover exciting and budget-friendly travel packages.</p>
      <button onclick="goTo('deals.php')">View Packages</button>
    </div>
  </section>

  <footer>
    &copy; 2025 HindSafar. Empowering your journey.
  </footer>

  <script>
    function goTo(page) {
      window.location.href = page;
    }

    // Dropdown toggle functionality
    const userMenu = document.querySelector('.user-menu');
    const userDropdown = document.querySelector('.user-dropdown');

    userMenu.addEventListener('click', function(e) {
      e.stopPropagation(); // Prevent closing when clicking inside
      userDropdown.classList.toggle('show');
    });

    document.body.addEventListener('click', function() {
      userDropdown.classList.remove('show');
    });
  </script>
</body>
</html>
