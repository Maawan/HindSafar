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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fc;
        }
        .contact-form {
            max-width: 600px;
            margin: auto;
            margin-top: 50px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        footer {
            margin-top: 60px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="contact-form">
        <h3 class="mb-4 text-center">Contact Us</h3>

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
                echo "<div class='alert alert-success'>Thank you, <strong>$name</strong>. Your query has been submitted.</div>";
            } else {
                echo "<div class='alert alert-danger'>Something went wrong. Please try again later.</div>";
            }

            $stmt->close();

        }
        ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name *</label>
                <input type="text" required class="form-control" name="name" id="name">
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Contact Number *</label>
                <input type="text" required class="form-control" name="contact" id="contact">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email *</label>
                <input type="email" required class="form-control" name="email" id="email">
            </div>
            <div class="mb-3">
                <label for="subject" class="form-label">Subject *</label>
                <input type="text" required class="form-control" name="subject" id="subject">
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message *</label>
                <textarea required class="form-control" name="message" id="message" rows="5"></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit Query</button>
        </form>
    </div>

    <footer class="mt-5">
        <hr>
        <p><strong>Contact Details</strong></p>
        <p>📞 Phone: +91 98765 43210</p>
        <p>✉️ Email: support@hindsafar.com</p>
        <p>&copy; 2025 HindSafar. Empowering your journey.</p>
    </footer>
</div>

</body>
</html>
