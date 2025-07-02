<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['name'])) {
  header("Location: dashboard.php");
  exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HindSafar – Travel Booking Platform</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to bottom right, #f5f7fa, #c3cfe2);
      color: #333;
    }

    header {
      background: #004aad;
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      margin: 0;
      font-size: 1.6rem;
    }

    nav button {
      background: white;
      color: #004aad;
      border: none;
      padding: 0.5rem 1rem;
      margin-left: 0.5rem;
      border-radius: 5px;
      cursor: pointer;
    }

    .hero {
      text-align: center;
      padding: 3rem 1rem;
    }

    .hero h2 {
      font-size: 2.2rem;
    }

    .hero p {
      font-size: 1.1rem;
      max-width: 600px;
      margin: 1rem auto;
    }

    .hero .cta {
      margin-top: 1.5rem;
    }

    .hero button {
      background: #004aad;
      color: white;
      padding: 0.7rem 1.5rem;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      cursor: pointer;
    }

    .features {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      padding: 2rem 1rem;
      background-color: #ffffff;
    }

    .feature-box {
      width: 280px;
      background: #eef1f5;
      margin: 1rem;
      padding: 1.5rem;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
    }

    .feature-box h3 {
      margin-bottom: 0.5rem;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal-content {
      background: white;
      padding: 2rem;
      border-radius: 8px;
      width: 90%;
      max-width: 400px;
    }

    .modal-content h3 {
      margin-top: 0;
    }

    .modal-content input {
      width: 100%;
      padding: 0.5rem;
      margin: 0.5rem 0 1rem;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    .modal-content button {
      background: #004aad;
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 5px;
      cursor: pointer;
    }

    .close {
      float: right;
      cursor: pointer;
      font-weight: bold;
    }

    footer {
      text-align: center;
      padding: 1rem;
      background: #f1f1f1;
      font-size: 0.9rem;
      color: #666;
    }

    @media (max-width: 600px) {
      .hero h2 {
        font-size: 1.6rem;
      }
    }
  </style>
</head>
<body>

  <header>
    <h1>HindSafar</h1>
    <nav>
      <button onclick="login()">Login</button>
      <button onclick="signup()">Sign Up</button>
    </nav>
  </header>

  <section class="hero">
    <h2>Your Journey Starts Here</h2>
    <p>Book flights, hotels, and curated travel experiences in one platform. Discover affordable offbeat destinations with ease.</p>
    <div class="cta">
      <button onclick="alert('Explore feature coming soon!')">Explore Destinations</button>
    </div>
  </section>

  <section class="features">
    <div class="feature-box">
      <h3>📅 All-in-One Booking</h3>
      <p>Plan and book your entire trip – flights, hotels, and packages – all from a single platform.</p>
    </div>
    <div class="feature-box">
      <h3>💰 Budget-Friendly Deals</h3>
      <p>Explore exclusive deals and travel packages to stunning yet affordable destinations.</p>
    </div>
    <div class="feature-box">
      <h3>🔐 Secure Payments</h3>
      <p>Make payments safely using PayPal, Google Pay, or card – encrypted and PCI-DSS compliant.</p>
    </div>
    <div class="feature-box">
      <h3>🌐 Multi-Device Support</h3>
      <p>Book and manage your trips from mobile or desktop anytime, anywhere.</p>
    </div>
  </section>

  <!-- Login Modal -->
  <div class="modal" id="loginModal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('login')">&times;</span>
      <h3>Login to HindSafar</h3>
      <input type="text" id="loginPhone" placeholder="Contact Number">
      <input type="password" id="loginPass" placeholder="Password">
      <button onclick="login()">Login</button>
    </div>
  </div>

  <!-- Signup Modal -->
  <div class="modal" id="signupModal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('signup')">&times;</span>
      <h3>Start Your HindSafar Journey</h3>
      <input type="text" id="signupName" placeholder="Given Name">
      <input type="text" id="signupPhone" placeholder="Mobile Number">
      <input type="password" id="signupPass" placeholder="Password">
      <button onclick="signup()">Sign Up</button>
    </div>
  </div>

  <footer>
    &copy; 2025 HindSafar. Empowering your next journey.
  </footer>

  <script>
    function login(){
        window.location.href = "login.html";
    }
    function signup(){ 
        window.location.href = "signup.html";
    }

    

    
  </script>
</body>
</html>
