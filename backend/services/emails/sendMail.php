<?php
function sendHotelMail($name, $email, $payment_reference, $amount, $booking_id) {
    $htmlBody = <<<EOD
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Hotel Booking Confirmation – HindSafar</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
    .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
    .header { background-color: #0369a1; color: white; padding: 20px; text-align: center; }
    .header img { max-height: 50px; margin-bottom: 10px; }
    .content { padding: 30px; color: #333333; }
    .content h1 { font-size: 22px; margin-top: 0; }
    .content p { font-size: 16px; line-height: 1.6; }
    .content .info-box { background: #f1f5f9; padding: 15px; border-radius: 6px; margin-top: 15px; }
    .footer { background-color: #e2e8f0; text-align: center; padding: 15px; font-size: 14px; color: #666666; }
    a.button { display: inline-block; margin-top: 20px; padding: 12px 24px; background-color: #0369a1; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="https://res.cloudinary.com/duklzb1ww/image/upload/v1754343126/logo_qcve8t.png" alt="HindSafar Logo" />
      <h2>Hotel Booking Confirmed</h2>
    </div>
    <div class="content">
      <h1>Hello {$name},</h1>
      <p>We're pleased to inform you that your hotel booking has been <strong>confirmed</strong> and payment has been <strong>successfully received</strong>.</p>

      <div class="info-box">
        <p><strong>Booking ID:</strong> {$booking_id}</p>
        <p><strong>Payment Reference:</strong> {$payment_reference}</p>
        <p><strong>Amount Paid:</strong> ₹{$amount}</p>
      </div>

      <p>Your receipt is attached with this email. Please keep it for your records.</p>

      <p>If you have any questions or need to make changes, feel free to contact our support team.</p>

      <a href="https://hindsafar.com" class="button">Visit HindSafar</a>
    </div>
    <div class="footer">
      &copy; 2025 HindSafar. All rights reserved.<br/>
      Need help? <a href="mailto:support@hindsafar.com">support@hindsafar.com</a>
    </div>
  </div>
</body>
</html>
EOD;

    // You can now send this HTML with PHPMailer or Mailtrap API.
    sendMail($email , $htmlBody , $name, "Payment Confirmation | Hindsafar");
}

function sendPackageMail($name, $email, $payment_reference, $amount, $booking_id) {
    $htmlBody = <<<EOD
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Hotel Booking Confirmation – HindSafar</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
    .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
    .header { background-color: #0369a1; color: white; padding: 20px; text-align: center; }
    .header img { max-height: 50px; margin-bottom: 10px; }
    .content { padding: 30px; color: #333333; }
    .content h1 { font-size: 22px; margin-top: 0; }
    .content p { font-size: 16px; line-height: 1.6; }
    .content .info-box { background: #f1f5f9; padding: 15px; border-radius: 6px; margin-top: 15px; }
    .footer { background-color: #e2e8f0; text-align: center; padding: 15px; font-size: 14px; color: #666666; }
    a.button { display: inline-block; margin-top: 20px; padding: 12px 24px; background-color: #0369a1; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="https://res.cloudinary.com/duklzb1ww/image/upload/v1754343126/logo_qcve8t.png" alt="HindSafar Logo" />
      <h2>Travel Package Booking Confirmed</h2>
    </div>
    <div class="content">
      <h1>Hello {$name},</h1>
      <p>We're pleased to inform you that your Travel Package booking has been <strong>confirmed</strong> and payment has been <strong>successfully received</strong>.</p>

      <div class="info-box">
        <p><strong>Booking ID:</strong> {$booking_id}</p>
        <p><strong>Payment Reference:</strong> {$payment_reference}</p>
        <p><strong>Amount Paid:</strong> ₹{$amount}</p>
      </div>

      <p>Your receipt is attached with this email. Please keep it for your records.</p>

      <p>If you have any questions or need to make changes, feel free to contact our support team.</p>

      <a href="https://hindsafar.com" class="button">Visit HindSafar</a>
    </div>
    <div class="footer">
      &copy; 2025 HindSafar. All rights reserved.<br/>
      Need help? <a href="mailto:support@hindsafar.com">support@hindsafar.com</a>
    </div>
  </div>
</body>
</html>
EOD;

    // You can now send this HTML with PHPMailer or Mailtrap API.
    sendMail($email , $htmlBody , $name, "Payment Confirmation | Hindsafar");
}

function sendFlightMail($name, $email, $payment_reference, $amount, $booking_id) {
    $htmlBody = <<<EOD
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Hotel Booking Confirmation – HindSafar</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
    .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
    .header { background-color: #0369a1; color: white; padding: 20px; text-align: center; }
    .header img { max-height: 50px; margin-bottom: 10px; }
    .content { padding: 30px; color: #333333; }
    .content h1 { font-size: 22px; margin-top: 0; }
    .content p { font-size: 16px; line-height: 1.6; }
    .content .info-box { background: #f1f5f9; padding: 15px; border-radius: 6px; margin-top: 15px; }
    .footer { background-color: #e2e8f0; text-align: center; padding: 15px; font-size: 14px; color: #666666; }
    a.button { display: inline-block; margin-top: 20px; padding: 12px 24px; background-color: #0369a1; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="https://res.cloudinary.com/duklzb1ww/image/upload/v1754343126/logo_qcve8t.png" alt="HindSafar Logo" />
      <h2>Flight Booking Confirmed</h2>
    </div>
    <div class="content">
      <h1>Hello {$name},</h1>
      <p>We're pleased to inform you that your flight booking has been <strong>confirmed</strong> and payment has been <strong>successfully received</strong>.</p>

      <div class="info-box">
        <p><strong>Booking ID:</strong> {$booking_id}</p>
        <p><strong>Payment Reference:</strong> {$payment_reference}</p>
        <p><strong>Amount Paid:</strong> ₹{$amount}</p>
      </div>

      <p>Your receipt is attached with this email. Please keep it for your records.</p>

      <p>If you have any questions or need to make changes, feel free to contact our support team.</p>

      <a href="https://hindsafar.com" class="button">Visit HindSafar</a>
    </div>
    <div class="footer">
      &copy; 2025 HindSafar. All rights reserved.<br/>
      Need help? <a href="mailto:support@hindsafar.com">support@hindsafar.com</a>
    </div>
  </div>
</body>
</html>
EOD;

    // You can now send this HTML with PHPMailer or Mailtrap API.
    sendMail($email , $htmlBody , $name, "Payment Confirmation | Hindsafar");
}



function sendWelcomeMail($mail, $name) {
    $htmlBody = <<<EOD
    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8">
    <title>Welcome to HindSafar</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        .header { background-color: #0369a1; color: white; padding: 20px; text-align: center; }
        .header img { max-height: 50px; margin-bottom: 10px; }
        .content { padding: 30px; color: #333333; }
        .content h1 { font-size: 24px; margin-top: 0; }
        .content p { font-size: 16px; line-height: 1.6; }
        .button { display: inline-block; margin-top: 20px; padding: 12px 24px; background-color: #0369a1; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .footer { background-color: #e2e8f0; text-align: center; padding: 15px; font-size: 14px; color: #666666; }
        @media only screen and (max-width: 600px) { .content { padding: 20px; } }
    </style>
    </head>
    <body>
    <div class="container">
        <div class="header">
        <img src="https://res.cloudinary.com/duklzb1ww/image/upload/v1754343126/logo_qcve8t.png" alt="HindSafar Logo" />
        <h2>Welcome to HindSafar!</h2>
        </div>
        <div class="content">
        <h1>Hello {$name}, and Welcome!</h1>
        <p>Thank you for signing up with <strong>HindSafar</strong> — your trusted travel booking partner.</p>
        <p>We're excited to have you on board. With HindSafar, you can easily book flights, hotels, and travel packages with confidence.</p>
        <p>Start exploring now and let your next journey begin!</p>
        <a href="https://hindsafar.com" class="button">Visit HindSafar</a>
        </div>
        <div class="footer">
        &copy; 2025 HindSafar. All rights reserved.<br/>
        Need help? Contact us at <a href="mailto:support@hindsafar.com">support@hindsafar.com</a>
        </div>
    </div>
    </body>
    </html>
    EOD;
    
    sendMail($mail, $htmlBody, $name , "Welcome to Hindsafar");
}


function sendMail($mailTo , $html , $name , $subject){
$url = 'https://send.api.mailtrap.io/api/send';

    $data = [
        "to" => [
            [
                "email" => $mailTo,
                "name" => $name
            ]
        ],
        
        "from" => [
            "email" => "algomingle@hayatsoftwares.com",
            "name" => "HindSafar Travels Limited"
        ],
        "reply_to" => [
            "email" => "info@hindsafar.com",
            "name" => "Reply"
        ],
        "attachments" => [
            // [
            //     "content" => base64_encode(file_get_contents('index.html')), // attach local index.html
            //     "filename" => "index.html",
            //     "type" => "text/html",
            //     "disposition" => "attachment"
            // ]
        ],
        "custom_variables" => [
            "user_id" => "45982",
            "batch_id" => "PSJ-12"
        ],
        "headers" => [
            "X-Message-Source" => "dev.mydomain.com"
        ],
        "subject" => $subject,
        "html" => $html,
        "category" => "API Test"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Api-Token: c0057a65c6e02bc9c30f4cfe9efba3f9'
    ]);

    $response = curl_exec($ch);

    // if (curl_errno($ch)) {
    //     echo 'Error: ' . curl_error($ch);
    // } else {
    //     echo 'Response: ' . $response;
    // }

    curl_close($ch);
}


?>