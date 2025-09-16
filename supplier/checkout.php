<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id']; // Ensure this is set

// Retrieve total from the cart
$sql = "SELECT SUM(t.tender_price) AS total_price FROM cart c JOIN tenders t ON c.tender_id = t.tender_id WHERE c.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$total_price = $stmt->fetchColumn();

// Check if total price is valid
if ($total_price <= 0) {
    die("Your cart is empty or there's an error in calculating the total.");
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // 1. Insert the order into the `orders` table
    $order_sql = "INSERT INTO orders (user_id, total_amount, order_date) VALUES (:user_id, :total_price, NOW())";
    $order_stmt = $pdo->prepare($order_sql);
    $order_stmt->execute([
        'user_id' => $user_id,
        'total_price' => $total_price
    ]);

    // Get the last inserted `order_id`
    $order_id = $pdo->lastInsertId();

    // 2. Retrieve cart items for this user to insert into `order_items`
    $cart_sql = "SELECT c.tender_id, t.tender_price FROM cart c JOIN tenders t ON c.tender_id = t.tender_id WHERE c.user_id = :user_id";
    $cart_stmt = $pdo->prepare($cart_sql);
    $cart_stmt->execute(['user_id' => $user_id]);
    $cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Insert each cart item as an order item in `order_items`
    $order_item_sql = "INSERT INTO order_items (order_id, tender_id, price) VALUES (:order_id, :tender_id, :price)";
    $order_item_stmt = $pdo->prepare($order_item_sql);

    foreach ($cart_items as $item) {
        $order_item_stmt->execute([
            'order_id' => $order_id,
            'tender_id' => $item['tender_id'],
            'price' => $item['tender_price']
        ]);
    }

    // 4. Clear the cart after inserting into `order_items`
    $clear_cart_sql = "DELETE FROM cart WHERE user_id = :user_id";
    $clear_cart_stmt = $pdo->prepare($clear_cart_sql);
    $clear_cart_stmt->execute(['user_id' => $user_id]);

    // Commit transaction
    $pdo->commit();

    // Payfast sandbox integration starts here
    $sandbox_merchant_id = "10035845";
    $sandbox_merchant_key = "a757i1bdd6d6h";
    $sandbox_url = "https://sandbox.payfast.co.za/eng/process";
    $return_url = "http://127.0.0.1/etender/supplier/payment_success.php";
    $cancel_url = "http://127.0.0.1/etender/supplier/payment_cancelled.php";
    $notify_url = "http://127.0.0.1/etender/supplier/supplier/payment_callback.php";

    // Unique identifier for Payfast
   // $order_payment_id = uniqid("ORDER_" . $order_id . "_");

    // Payfast data setup
    $payfast_data = array(
        'merchant_id' => $sandbox_merchant_id,
        'merchant_key' => $sandbox_merchant_key,
        'return_url' => $return_url,
        'cancel_url' => $cancel_url,
        'notify_url' => $notify_url,
        'm_payment_id' => $order_id, // Unique order ID
        'amount' => number_format($total_price, 2, '.', ''), // Format amount to 2 decimals
        'item_name' => "Tender Purchase",
        'item_description' => "Purchase of tenders from e-tender platform"
    );

    // Redirect user to Payfast sandbox
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Redirecting to Payfast</title>
    </head>
    <body>
        <form action="<?php echo $sandbox_url; ?>" method="post" id="payfast_form">
            <?php foreach ($payfast_data as $name => $value): ?>
                <input type="hidden" name="<?php echo $name; ?>" value="<?php echo htmlspecialchars($value); ?>">
            <?php endforeach; ?>
            <p>Redirecting to Payfast...</p>
        </form>
        <script>
            document.getElementById('payfast_form').submit();
        </script>
    </body>
    </html>
    <?php

} catch (PDOException $e) {
    // Rollback the transaction if something failed
    $pdo->rollBack();
    echo "Failed to place order: " . $e->getMessage();
}
?>
