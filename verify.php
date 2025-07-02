<?php
require('razorpay-php/Razorpay.php');

use Razorpay\Api\Api;

$api_key = 'rzp_test_tN2HjlxfDwX4rW';
$api_secret = '5wjPwOMcdSgsbG1dZTvQCqBq';
$api = new Api($api_key , $api_secret);

require './backend/Database/db.php';

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
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Payment Successful</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f2f2f2;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .success-container {
                background: white;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                text-align: center;
            }
            .success-container i {
                font-size: 60px;
                color: green;
            }
            .success-container h2 {
                margin-top: 20px;
                color: green;
            }
            .success-container p {
                font-size: 18px;
                margin: 10px 0;
            }
            .btn {
                display: inline-block;
                margin-top: 20px;
                padding: 10px 20px;
                background: green;
                color: white;
                text-decoration: none;
                border-radius: 5px;
            }
            .btn:hover {
                background: darkgreen;
            }
        </style>
        <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="success-container">
            <i class="fas fa-check-circle"></i>
            <h2>Payment Successful</h2>
            <p>Thank you for your payment.</p>
            <p><strong>Payment ID:</strong> <?php echo htmlspecialchars($payment_id); ?></p>
            <p><strong>Amount Paid:</strong> ₹<?php echo htmlspecialchars($amount_paid); ?></p>
            <a href="index.php" class="btn">Go Back Home</a>
        </div>
    </body>
    </html>

    <?php
} else {
    echo "Payment failed: ".$error;
}
?>
