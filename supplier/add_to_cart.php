<?php
session_start();
include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("location:../index");
    exit;
}

$user_id = $_SESSION['user_id'];
$tender_id = $_GET['tender_id'];

try {
    // Check if the tender has already been purchased by the user
    $check_purchase_sql = "SELECT COUNT(*) FROM order_items oi
                           JOIN orders o ON oi.order_id = o.order_id
                           WHERE o.user_id = :user_id AND oi.tender_id = :tender_id AND o.status = 'completed'";
    $check_purchase_stmt = $pdo->prepare($check_purchase_sql);
    $check_purchase_stmt->execute(['user_id' => $user_id, 'tender_id' => $tender_id]);
    $already_purchased = $check_purchase_stmt->fetchColumn();

    if ($already_purchased > 0) {
        // If the tender is already purchased, show an error message and redirect
        echo "<script>alert('You have already purchased this tender.'); window.location.href = 'invited_tenders.php';</script>";
        exit;
    }

    // If not purchased, add to cart
    $sql = "INSERT INTO cart (user_id, tender_id) VALUES (:user_id, :tender_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id, 'tender_id' => $tender_id]);

    // Redirect to the cart or show a success message
    echo "<script>alert('Tender added to cart successfully!'); window.location.href = 'cart.php';</script>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
