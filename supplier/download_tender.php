<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$tender_id = $_GET['tender_id'];

// Check if the tender is in a completed order for the user
$sql = "SELECT o.status, oi.tender_id, t.tender_document_attachment FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN tenders t ON oi.tender_id = t.tender_id
        WHERE o.user_id = :user_id AND oi.tender_id = :tender_id AND o.status = 'completed'";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id, 'tender_id' => $tender_id]);
$purchased_tender = $stmt->fetch(PDO::FETCH_ASSOC);

if ($purchased_tender) {
    $file_path = $purchased_tender['tender_document_attachment'];
    header("Content-Disposition: attachment; filename=" . basename($file_path));
    readfile($file_path);
} else {
    echo "You do not have access to download this document.";
}
?>
