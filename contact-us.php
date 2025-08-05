<?php
session_start();
require './Backend/Database/db.php';
// 🔐 Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - HindSafar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 text-gray-800">
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

<div class="container mx-auto px-4">
    <div class="max-w-xl mx-auto mt-12 bg-white p-8 rounded-2xl shadow-md">
        <h3 class="text-2xl font-semibold mb-6 text-center">Contact Us</h3>

        <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name = htmlspecialchars($_POST["name"]);
            $contact = htmlspecialchars($_POST["contact"]);
            $email = htmlspecialchars($_POST["email"]);
            $subject = htmlspecialchars($_POST["subject"]);
            $message = htmlspecialchars($_POST["message"]);
            $user_id = $_SESSION['user_id'];
            
            $stmt = $conn->prepare("INSERT INTO customer_queries (user_id, customer_name, contact_number, email, subject, message, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("isssss", $user_id, $name, $contact, $email, $subject, $message);

            if ($stmt->execute()) {
                echo "<div class='bg-green-100 text-green-800 px-4 py-3 mb-4 rounded-md'>Thank you, <strong>$name</strong>. Your query has been submitted.</div>";
            } else {
                echo "<div class='bg-red-100 text-red-800 px-4 py-3 mb-4 rounded-md'>Something went wrong. Please try again later.</div>";
            }

            $stmt->close();
        }
        ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label for="name" class="block mb-1 font-medium">Full Name *</label>
                <input type="text" required name="name" id="name" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-400">
            </div>
            <div class="mb-4">
                <label for="contact" class="block mb-1 font-medium">Contact Number *</label>
                <input type="text" required name="contact" id="contact" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-400">
            </div>
            <div class="mb-4">
                <label for="email" class="block mb-1 font-medium">Email *</label>
                <input type="email" required name="email" id="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-400">
            </div>
            <div class="mb-4">
                <label for="subject" class="block mb-1 font-medium">Subject *</label>
                <input type="text" required name="subject" id="subject" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-400">
            </div>
            <div class="mb-6">
                <label for="message" class="block mb-1 font-medium">Message *</label>
                <textarea required name="message" id="message" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-400"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Submit Query</button>
        </form>
    </div>

    
</div>
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
</body>
</html>
