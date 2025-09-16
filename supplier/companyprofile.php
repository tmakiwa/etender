<?php
session_start();
include 'db.php';

// Assume user ID is stored in session after login
$user_id = $_SESSION['user_id']; // Replace with actual session variable for user ID

try {
    // Fetch company registration info (COR_14_3)
    $cor_sql = "SELECT * FROM COR_14_3 WHERE user_id = ?";
    $cor_stmt = $pdo->prepare($cor_sql);
    $cor_stmt->execute([$user_id]);
    $cor_info = $cor_stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch tax info
    $tax_sql = "SELECT * FROM tax WHERE user_id = ?";
    $tax_stmt = $pdo->prepare($tax_sql);
    $tax_stmt->execute([$user_id]);
    $tax_info = $tax_stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch BEE info
    $bee_sql = "SELECT * FROM BEE WHERE user_id = ?";
    $bee_stmt = $pdo->prepare($bee_sql);
    $bee_stmt->execute([$user_id]);
    $bee_info = $bee_stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Company Documents</title>
    <link rel="stylesheet" href="path/to/font-awesome.css">
    <link rel="stylesheet" href="path/to/bootstrap.css">
</head>
<body>

<div class="box_general">
    <div class="header_box">
        <h2 class="d-inline-block">My Company Documents</h2>
    </div>
    <div class="list_general">
        <ul>
            <!-- Company Registration (COR_14_3) -->
            <li>
                <h4>
                    Company Registration (COR_14_3) 
                    <?php if ($cor_info && $cor_info['status'] === 'Verified'): ?>
                        <i class="approved">Verified</i>
                    <?php else: ?>
                        <i class="pending">Pending</i>
                    <?php endif; ?>
                </h4>
                <ul class="booking_details">
                    <li><strong>Company Reg Number:</strong> <?php echo htmlspecialchars($cor_info['company_number'] ?? 'N/A'); ?></li>
                </ul>
                <ul class="buttons">
                    <li><a href="index.php?page=add_cor143.php" class="btn_1 gray approve"><i class="fa fa-fw fa-plus"></i> Add</a></li>
                    <li><a href="index.php?page=update_cor143.php" class="btn_1 gray approve"><i class="fa fa-fw fa-edit"></i> Update</a></li>
                    <?php if (!empty($cor_info['document'])): ?>
                        <li><a href="<?php echo htmlspecialchars($cor_info['document']); ?>" target="_blank" class="btn_1 gray approve"><i class="fa fa-fw fa-file"></i> View</a></li>
                    <?php else: ?>
                        <li><span class="btn_1 gray disabled">No Document</span></li>
                    <?php endif; ?>
                </ul>
            </li>

            <!-- Tax Clearance -->
            <li>
                <h4>
                    Tax Clearance
                    <?php if ($tax_info && $tax_info['status'] === 'Verified'): ?>
                        <i class="approved">Verified</i>
                    <?php else: ?>
                        <i class="pending">Pending</i>
                    <?php endif; ?>
                </h4>
                <ul class="booking_details">
                    <li><strong>Tax Number:</strong> <?php echo htmlspecialchars($tax_info['tax_number'] ?? 'N/A'); ?></li>
                </ul>
                <ul class="buttons">
                    <li><a href="index.php?page=add_tax.php" class="btn_1 gray approve"><i class="fa fa-fw fa-plus"></i> Add</a></li>
                    <li><a href="index.php?page=update_tax.php" class="btn_1 gray approve"><i class="fa fa-fw fa-edit"></i> Update</a></li>
                    <?php if (!empty($tax_info['tax_clearance'])): ?>
                        <li><a href="<?php echo htmlspecialchars($tax_info['tax_clearance']); ?>" target="_blank" class="btn_1 gray approve"><i class="fa fa-fw fa-file"></i> View</a></li>
                    <?php else: ?>
                        <li><span class="btn_1 gray disabled">No Document</span></li>
                    <?php endif; ?>
                </ul>
            </li>

            <!-- BEE -->
            <li>
                <h4>
                    BEE Information
                    <?php if ($bee_info && $bee_info['status'] === 'Verified'): ?>
                        <i class="approved">Verified</i>
                    <?php else: ?>
                        <i class="pending">Pending</i>
                    <?php endif; ?>
                </h4>
                <ul class="booking_details">
                    <li><strong>Company Reg Number:</strong> <?php echo htmlspecialchars($bee_info['company_number'] ?? 'N/A'); ?></li>
                </ul>
                <ul class="buttons">
                    <li><a href="index.php?page=add_bee.php" class="btn_1 gray approve"><i class="fa fa-fw fa-plus"></i> Add</a></li>
                    <li><a href="index.php?page=update_bee.php" class="btn_1 gray approve"><i class="fa fa-fw fa-edit"></i> Update</a></li>
                    <?php if (!empty($bee_info['bee_document'])): ?>
                        <li><a href="<?php echo htmlspecialchars($bee_info['bee_document']); ?>" target="_blank" class="btn_1 gray approve"><i class="fa fa-fw fa-file"></i> View</a></li>
                    <?php else: ?>
                        <li><span class="btn_1 gray disabled">No Document</span></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    </div>
</div>

<!-- Include necessary JS for Bootstrap -->
<script src="path/to/jquery.js"></script>
<script src="path/to/bootstrap.js"></script>

</body>
</html>
