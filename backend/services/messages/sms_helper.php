<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Twilio\Rest\Client;

// Define the function
function sendSMS($to, $messageBody) {
    // Twilio credentials
    $sid = "ACb2ed50757680e2695c860b6fed68a1f1";
    $token = "e838ce0751e6a6273ad7446fe93ddc07";
    $twilioNumber = "+18563861767";

    // Create Twilio client
    $twilio = new Client($sid, $token);

    try {
        // Send SMS
        $message = $twilio->messages->create(
            $to,
            [
                "body" => $messageBody,
                "from" => $twilioNumber
            ]
        );

        // Return message SID or body if needed
        return "Message sent successfully. SID: " . $message->sid;

    } catch (Exception $e) {
        // Return error if failed
        return "Error: " . $e->getMessage();
    }
}



