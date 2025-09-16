<?php
include 'db.php';

// Get raw POST data from Payfast (ITN data)
$pfData = file_get_contents('php://input');

// Use Payfast's sandbox validation URL for testing
$payfastUrl = 'https://sandbox.payfast.co.za/eng/query/validate';

// Verify the ITN data with Payfast sandbox (cURL setup)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $payfastUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $pfData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded'
));
$response = curl_exec($ch);
curl_close($ch);

if (strcasecmp($response, 'VALID') == 0) {
    // Parse the response data
    parse_str($pfData, $data);
    $payment_status = $data['payment_status'];
    $order_id = $data['m_payment_id']; // This is your unique order ID

    if ($payment_status == 'COMPLETE') {
        // Mark order as completed in the database
        $update_sql = "UPDATE orders SET status = 'completed' WHERE order_id = :order_id";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute(['order_id' => $order_id]);

        // Get the user and tender details for sending the email
        $order_info_sql = "SELECT u.email, t.tender_title, t.tender_document_attachment 
                           FROM orders o 
                           JOIN order_items oi ON o.order_id = oi.order_id
                           JOIN tenders t ON oi.tender_id = t.tender_id
                           JOIN users u ON o.user_id = u.id
                           WHERE o.order_id = :order_id";
        $order_info_stmt = $pdo->prepare($order_info_sql);
        $order_info_stmt->execute(['order_id' => $order_id]);
        $order_info = $order_info_stmt->fetch(PDO::FETCH_ASSOC);

        if ($order_info) {
            // User's email and tender document information
            $userEmail = $order_info['email'];
            $tenderTitle = $order_info['tender_title'];
            $documentLink = "http://127.0.0.1/etender/admin/" . $order_info['tender_document_attachment'];

            // Send the email with the download link
            sendTenderDocumentEmail($userEmail, $tenderTitle, $documentLink);

            echo "Payment successful and email sent.";
        }
    } else {
        // Update order as failed if payment was not successful
        $update_sql = "UPDATE orders SET status = 'failed' WHERE order_id = :order_id";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute(['order_id' => $order_id]);

        echo "Payment failed.";
    }
} else {
    error_log("Payfast validation failed for ITN.");
}

// Email-sending function
function sendTenderDocumentEmail($userEmail, $tenderTitle, $downloadLink) {
    $postmarkData = [
        "From" => "info@etender.co.bw",
        "To" => $userEmail,
        "Subject" => "Your Tender Document is Ready for Download",
        "HtmlBody" => "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Tender Document Download</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        color: #333;
                        margin: 0;
                        padding: 0;
                    }
                    .email-container {
                        max-width: 600px;
                        margin: 20px auto;
                        background-color: #ffffff;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                    }
                    .email-header {
                        background-color: #f4f4f4;
                        padding: 20px;
                        text-align: center;
                        color: #ffffff;
                    }
                    .email-header img {
                        max-width: 150px;
                        margin-bottom: 10px;
                    }
                    .email-content {
                        padding: 30px;
                    }
                    .email-content h2 {
                        color: #333;
                        font-size: 24px;
                        margin-bottom: 20px;
                    }
                    .email-content p {
                        font-size: 16px;
                        line-height: 1.5;
                        margin-bottom: 20px;
                    }
                    .download-button {
                        display: inline-block;
                        padding: 12px 24px;
                        color: #ffffff;
                        background-color: #4CAF50;
                        border-radius: 5px;
                        text-decoration: none;
                        font-size: 16px;
                        font-weight: bold;
                    }
                    .download-button:hover {
                        background-color: #45a049;
                    }
                    .email-footer {
                        background-color: #f4f4f4;
                        padding: 15px;
                        text-align: center;
                        font-size: 14px;
                        color: #777;
                    }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <img src='https://app.etendersouthafrica.co.za/supplier/img/etender.png' alt='e-Tender'>
                        <h1>Tender Invitation Document</h1>
                    </div>
                    <div class='email-content'>
                        <h2>Thank you for your purchase!</h2>
                        <p>You have successfully purchased the tender: <strong>{$tenderTitle}</strong>.</p>
                        <p>Click the button below to download your document or download it from My Tenders on your Portal:</p>
                        <p style='text-align: center;'>
                            <a href='{$downloadLink}' class='download-button' target='_blank'>Download Document</a>
                        </p>
                    </div>
                    <div class='email-footer'>
                        &copy; " . date("Y") . " e-Tender. All rights reserved.
                    </div>
                </div>
            </body>
            </html>
        ",
        "MessageStream" => "outbound"
    ];

    // cURL for Postmark API request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.postmarkapp.com/email");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postmarkData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: application/json",
        "Content-Type: application/json",
        "X-Postmark-Server-Token: 80946ce0-c815-4a7f-b2d6-ea5e65a2da10"
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log('Postmark error: ' . curl_error($ch));
    } else {
        $response_data = json_decode($response, true);
        if ($response_data['Message'] != 'OK') {
            error_log('Failed to send email via Postmark: ' . $response_data['Message']);
        }
    }
    curl_close($ch);
}
