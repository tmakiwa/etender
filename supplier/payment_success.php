<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection file
include 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch the last successful order (or relevant details for this payment)
$order_sql = "SELECT order_id, total_amount FROM orders WHERE user_id = :user_id ORDER BY order_date DESC LIMIT 1";
$stmt = $pdo->prepare($order_sql);
$stmt->execute(['user_id' => $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

// Retrieve the total amount for display
$total_amount = number_format($order['total_amount'], 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
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
            color: #5cb85c; /* Bootstrap success color */
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
            background-color: #5cb85c; /* Bootstrap success color */
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .message-container a:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>

<div class="message-container">
    <h2>Payment Successful</h2>
    <p>Thank you! Your payment of ZAR <?php echo htmlspecialchars($total_amount); ?> was successful.</p>
    <p>You can now access the tender document.</p>
    <br><br>
    <a href="index.php">Return to Home</a>
</div>

</body>
</html>
