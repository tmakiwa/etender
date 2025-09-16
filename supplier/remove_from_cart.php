<?php
session_start();
include 'db.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the cart_id from the URL
if (isset($_GET['cart_id'])) {
    $cart_id = $_GET['cart_id'];

    try {
        // Prepare the SQL statement to delete the item from the cart
        $sql = "DELETE FROM cart WHERE id = :cart_id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);

        // Bind parameters and execute the statement
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Redirect back to the cart page with a success message
        header("Location: cart.php?message=Item removed from cart");
        exit();

    } catch (PDOException $e) {
        // Handle any errors
        echo "Error: " . $e->getMessage();
    }

} else {
    // If cart_id is not set, redirect to the cart page
    header("Location: cart.php?error=Invalid request");
    exit();
}
?>
