<?php


$tender_category = $_GET['category'];
$subject = 'You have been invited to tender';

$sender_email = 'info@etender.co.bw';
$sender_name = 'e-Tender';

// Fetch email addresses of users who favorited the tender category
$rsv = mysqli_query(conn(), "SELECT * FROM users,favourite_category WHERE favourite_category.favourite = '$tender_category' AND users.username=favourite_category.username") or die(mysqli_error());
//$rsv = mysqli_query(conn(), "SELECT * FROM users,favourite_category WHERE favourite_category.favourite = '$tender_category' AND users.username=favourite_category.username AND users.activate='activated'") or die(mysqli_error());

while ($row = mysqli_fetch_array($rsv)) {
    $recipient = $row['username'];

    // Fetch tenders in the specified category
    $rs = mysqli_query(conn(), "SELECT * FROM tenders WHERE tender_category = '$tender_category' AND tender_added_date='$date'");
    $tenders_html = "";
    while ($tender_row = mysqli_fetch_array($rs)) {
        // Build the email content here
        $tender_company = $tender_row['tender_company'];
        $tender_title = $tender_row['tender_name'];
        $tender_number = $tender_row['tender_number'];
        $tender_due = $tender_row['tender_due'];
        $tender_added_date = $tender_row['tender_added_date'];

        $tenders_html .= "
        <div style='background-color: #f9f9f9; padding: 20px; margin-bottom: 20px; border-radius: 5px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);'>
            <strong>$tender_company:</strong> $tender_title<br>
            <strong>Tender Ref/Number:</strong> $tender_number<br>
            <strong>Tender Closing Date:</strong> $tender_due<br>
            <strong>Tender Submitted:</strong> $tender_added_date
        </div>";
    }

    // Construct HTML email content
    $message = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Tender Invitation</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 600px;
                margin: 20px auto;
                padding: 20px;
                background-color: #fff;
                border: 1px solid #ccc;
                border-radius: 5px;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .tenders {
                margin-bottom: 20px;
            }
            .button {
                display: inline-block;
                padding: 10px 20px;
                background-color: #000;
                color: #fff;
                text-decoration: none;
                border-radius: 5px;
            }
            .button:hover {
                background-color: #333;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <img src='https://etender.co.bw/e-tender.png' alt='e-Tender Botswana' style='display: block; margin: 0 auto; max-width: 40%;
  height: auto;'>
            </div>
            <div class='tenders'>
                $tenders_html
            </div>
            <center><a href='https://etender.co.bw/tenders?tender_category=$tender_category' class='button' style='display: block; margin-bottom: 20px; text-align: center;width: 40%; color: white;'>VIEW TENDERS</a></center>
            <p style='color: #666; font-size: 12px; text-align: center;'>This email was sent by e-Tender Botswana. Please do not reply to this email.</p>
        </div>
    </body>
    </html>";

    // Construct JSON payload for Postmark API
    $postmark_data = array(
        "From" => "info@etendersouthafrica.co.za",
        "To" => $recipient,
        "Subject" => $subject,
        "HtmlBody" => $message,
        "MessageStream" => "broadcast"
    );

    // Encode payload to JSON
    $postmark_data = json_encode($postmark_data);

    // Set cURL options
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.postmarkapp.com/email");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postmark_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/json",
        "Content-Type: application/json",
        "X-Postmark-Server-Token: 80946ce0-c815-4a7f-b2d6-ea5e65a2da10"
    ));

    // Execute cURL request
    $result = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    } else {
        // Decode JSON response
        $response = json_decode($result, true);

        // Check if email was sent successfully
        if ($response['Message'] == 'OK') {
            echo "<script>window.location='emailsent.php';</script>";
        } else {
            echo "<script>alert('Failed to send email to $recipient');</script>";
        }
    }

    // Close cURL session
    curl_close($ch);
}

// Redirect to index.php
echo "<script>window.location='index.php';</script>";
?>
