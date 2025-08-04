<?php
session_start();
require('razorpay-php/Razorpay.php');
// include ''
use Razorpay\Api\Api;

$api_key = 'rzp_test_tN2HjlxfDwX4rW';
$api_secret = '5wjPwOMcdSgsbG1dZTvQCqBq';
$api = new Api($api_key , $api_secret);

require './backend/Database/db.php';
require_once "./backend/services/messages/templates/flight-templates.php";
require_once "./vendor/autoload.php";
require_once "./backend/services/emails/sendMail.php";

$success = true;
$error = null;

if(!isset($_POST['razorpay_payment_id'])){
    header("Location: index.php");
    exit();
}

$payment_id = $_POST['razorpay_payment_id'];
$razorpay_signature = $_POST['razorpay_signature'];
$orderId = $_POST['razorpay_order_id'];

try {
    $attributes = array(
        'razorpay_order_id' => $_POST['razorpay_order_id'],
        'razorpay_payment_id' => $payment_id,
        'razorpay_signature' => $razorpay_signature
    );
    $api->utility->verifyPaymentSignature($attributes);
} catch(\Razorpay\Api\Errors\SignatureVerificationError $e){
    $success = false;
    $error = 'Razorpay Signature Verification Failed';
}

if($success){
    $payment = $api->payment->fetch($payment_id);
    $amount_paid = $payment->amount / 100;

    $stmt = $pdo->prepare("UPDATE payments SET payment_status = 'Completed' WHERE razorpay_order_id = ?");
    $stmt->execute([$orderId]);

    $stmt = $pdo->prepare("SELECT payment_id, amount FROM payments WHERE razorpay_order_id = ?");
    $stmt->execute([$orderId]);
    $payment_row = $stmt->fetch();
    if ($payment_row) {
        $amount = $payment_row['amount'];
        $paymentt_id = $payment_row['payment_id'];
        $stmt = $pdo->prepare("UPDATE flight_bookings SET status = 'Confirmed' WHERE payment_id = ?");
        $stmt->execute([$paymentt_id]);

        confirmFlightBooking($paymentt_id , "ooo");
        $stmt = $pdo->prepare("SELECT * from customers where CUSTOMER_ID = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user_row = $stmt->fetch();
        $email = $user_row['email'];
        $name = $user_row['NAME'];
        
        // sendMail("maawan18@gmail.com");
        $type = $_GET['type'];
        if($type == "hotel"){
            $stmt = $pdo->prepare("SELECT booking_id from hotel_bookings where payment_id = ?");
            $stmt->execute([$paymentt_id]);
            $booking_row = $stmt->fetch();
            $booking_id = $booking_row['booking_id'];
            sendHotelMail($name , $email , $orderId , $amount , $booking_id);


        }else if($type == "flight"){
            $stmt = $pdo->prepare("SELECT booking_id from flight_bookings where payment_id = ?");
            $stmt->execute([$paymentt_id]);
            $booking_row = $stmt->fetch();
            $booking_id = $booking_row['booking_id'];
            sendFlightMail($name , $email , $orderId , $amount , $booking_id);
        }else if($type == "package"){
            $stmt = $pdo->prepare("SELECT booking_id from custom_packages_bookings where payment_id = ?");
            $stmt->execute([$paymentt_id]);
            $booking_row = $stmt->fetch();
            $booking_id = $booking_row['booking_id'];
            sendPackageMail($name , $email , $orderId , $amount , $booking_id);
        }

    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Payment Success – HindSafar</title>
        <link rel="icon" href="./assets/images/second.png" type="image/png" />
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    </head>
    <body class=" bg-gray-200  text-gray-800">

    <!-- Header -->
    <header class="bg-white shadow-xl">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="./dashboard.php" class="flex items-center space-x-2">
                <img src="./assets/images/logo.png" alt="logo" class="h-6" />
            </a>
            <h1 class="text-xl font-semibold"></h1>
            <div class="hidden md:flex space-x-6 text-sm text-gray-600">
                <div>INR</div>
                <div>English</div>
            </div>
        </div>
    </header>

    <!-- Success Container -->
    <div class="flex justify-center min-h-[70vh] px-4 ">
        <div class="bg-white shadow-green-500 my-4 rounded-2xl shadow-lg p-8 w-full max-w-xl text-center">
            <div class="text-green-600 mb-4">
                <i class="fas fa-check-circle text-6xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">✅ Payment Successful!</h2>
            <p class="text-gray-600 mb-4">Thank you for your payment.</p>
            <div class="bg-gray-100 p-4 rounded-lg text-left mb-4">
                <p class="mb-2"><strong>Payment ID:</strong> <?php echo htmlspecialchars($payment_id); ?></p>
                <p><strong>Amount Paid:</strong> ₹<?php echo htmlspecialchars($amount_paid); ?></p>
            </div>
            <a href="./my-bookings.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                Go to My Bookings
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white mt-12 pt-10 pb-6 border-t">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-4 gap-6 text-sm text-gray-600">
            <div>
                <img src="./assets/images/logo.png" class="h-10 mb-5" alt="">
                <p>Your one-stop travel solution for booking flights, hotels, and custom travel packages.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Explore</h4>
                <ul class="space-y-1">
                    <li><a href="?tab=flights" class="hover:text-blue-600">Flights</a></li>
                    <li><a href="?tab=hotels" class="hover:text-blue-600">Hotels</a></li>
                    <li><a href="?tab=packages" class="hover:text-blue-600">Packages</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Company</h4>
                <ul class="space-y-1">
                    <li><a href="#" class="hover:text-blue-600">About Us</a></li>
                    <li><a href="#" class="hover:text-blue-600">Contact</a></li>
                    <li><a href="#" class="hover:text-blue-600">Terms & Conditions</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Follow Us</h4>
                <ul class="space-y-1">
                    <li><a href="#" class="hover:text-blue-600">Instagram</a></li>
                    <li><a href="#" class="hover:text-blue-600">Facebook</a></li>
                    <li><a href="#" class="hover:text-blue-600">Twitter</a></li>
                </ul>
            </div>
        </div>
        <div class="text-center text-xs text-gray-400 mt-6">&copy; <?= date('Y') ?> HindSafar. All rights reserved.</div>
    </footer>

    </body>
    </html>

    <?php
} else {
    echo "<div style='text-align:center;margin-top:100px;'><h2>Payment failed</h2><p>$error</p></div>";
}
?>
