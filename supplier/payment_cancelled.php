<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled</title>
    <link rel="stylesheet" href="path/to/bootstrap.css"> <!-- Include your Bootstrap CSS path here -->
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }
        .message-container {
            text-align: center;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        .message-container h2 {
            color: #d9534f; /* Bootstrap danger color */
            font-size: 24px;
            margin-bottom: 20px;
        }
        .message-container p {
            color: #333;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .message-container a {
            text-decoration: none;
            color: white;
            background-color: #d9534f; /* Bootstrap danger color */
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .message-container a:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>

<div class="message-container">
    <h2>Payment Cancelled</h2>
    <p>Your payment was cancelled. If this was unintentional, please try again.</p>
    <a href="cart.php">Return to Cart</a>
</div>

</body>
</html>
