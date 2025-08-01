<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: #f2f2f2;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background: #fff;
      padding: 30px 25px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }

    .container h2 {
      text-align: center;
      margin-bottom: 20px;
      font-weight: 600;
      color: #333;
    }

    form input, form select, form button {
      width: 100%;
      padding: 12px 10px;
      margin: 10px 0;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    form input:focus, form select:focus {
      border-color: #0052CC;
      outline: none;
    }

    form button {
      background-color: #0052CC;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 16px;
      font-weight: 500;
      transition: background 0.3s ease;
    }

    form button:hover {
      background-color: #0052CC;
    }

    .phone-input-group {
      display: flex;
      gap: 10px;
    }

    .phone-input-group select {
      flex: 1.3;
      padding: 12px 5px;
    }

    .phone-input-group input {
      flex: 2;
    }

    p {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
      color: #666;
    }

    a {
      color: #0052CC;
      text-decoration: none;
      font-weight: 500;
    }

    a:hover {
      text-decoration: underline;
    }

    @media (max-width: 500px) {
      .phone-input-group {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Admin Login</h2>
    <form id="loginForm">
      <div class="phone-input-group">
        <select id="countryCode" required>
          <option value="+91">India (+91)</option>
          <option value="+1">USA (+1)</option>
          <option value="+44">UK (+44)</option>
          <option value="+61">Australia (+61)</option>
          <option value="+971">UAE (+971)</option>
          <!-- Add more as needed -->
        </select>
        <input type="tel" placeholder="Phone Number" id="phone" required>
      </div>

      <input type="password" placeholder="Password" id="password" required>
      <button type="submit">Login</button>
    </form>
    
  </div>

  <script>
    document.getElementById("loginForm").addEventListener("submit", async function (e) {
      e.preventDefault();

      const countryCode = document.getElementById("countryCode").value;
      const phone = document.getElementById("phone").value.trim();
      const password = document.getElementById("password").value;

      const fullPhoneNumber = countryCode + phone;

      try {
        const response = await fetch("http://localhost/HindSafar/backend/api/user/login.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          credentials: "include",
          body: JSON.stringify({ phone: fullPhoneNumber, password , type:"admin" })
        });

        const result = await response.json();

        if (result.status === "success" && result.role == "ADMIN") {
          alert("Login successful. Welcome " + result.user.name);
          window.location.href = "./dashboard.php";
        } else {
          alert("Login failed: ");
        }

      } catch (error) {
        console.error("Login error:", error);
        alert("Something went wrong. Please try again.");
      }
    });
  </script>
</body>
</html>
