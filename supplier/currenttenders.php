<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("location:../index");
    exit;
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

try {
    // Fetch tenders to which the supplier (user_id) has been invited, including the invitation document path
    $sql = "SELECT t.tender_id, t.tender_title,t.tender_number, t.tender_category, t.tender_created_time, 
                   t.tender_closing_date, t.tender_price, t.tender_invitation_attachment 
            FROM tenders t
            JOIN invited_tenders it ON t.tender_id = it.tender_id
            WHERE it.user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch invited tenders
    $tenders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invited Tenders</title>
    <link rel="stylesheet" href="path/to/font-awesome.css">
    <link rel="stylesheet" href="path/to/bootstrap.css">
</head>
<body>

<div class="card mb-3">
    <div class="card-header">
        <i class="fa fa-table"></i> Invited Tenders
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        
                        <th>Name</th>
                        <th>Category</th>
                        <th>Date Added</th>
                        <th>Due Date</th>
                        <th>Price ZAR</th>
                        <th>Act</th>
                       
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($tenders)): ?>
                        <?php foreach ($tenders as $tender): ?>
                            <tr>
                                
                                <td><?php echo htmlspecialchars($tender['tender_title']); ?> <br><b> <?php echo htmlspecialchars($tender['tender_number']); ?></b></td>
                                <td><?php echo htmlspecialchars($tender['tender_category']); ?></td>
                                <td><?php echo htmlspecialchars($tender['tender_created_time']); ?></td>
                                <td><?php echo htmlspecialchars($tender['tender_closing_date']); ?></td>
                                <td><?php echo htmlspecialchars($tender['tender_price']); ?></td>
                                
                                    <!-- Link to the actual invitation document -->
                                    
                                    
   <?php                                 // Array to store IDs of purchased tenders
$purchased_tenders = [];

// Query to check for tenders already purchased by the user
$purchased_sql = "SELECT oi.tender_id FROM order_items oi
                  JOIN orders o ON oi.order_id = o.order_id
                  WHERE o.user_id = :user_id AND o.status = 'completed'";
$purchased_stmt = $pdo->prepare($purchased_sql);
$purchased_stmt->execute(['user_id' => $user_id]);
$purchased_tenders = $purchased_stmt->fetchAll(PDO::FETCH_COLUMN);?>
<td>

    <!-- Tender Invitation Document Link -->
    <?php if (!empty($tender['tender_invitation_attachment'])): ?>
        <a href="<?php echo htmlspecialchars('../admin/' . $tender['tender_invitation_attachment']); ?>" target="_blank">Tender Invitation</a>
    <?php else: ?>
        <span>No Invitation Document</span>
    <?php endif; ?>

    <!-- Tender Document Download Link (after successful purchase) -->
    <?php if (in_array($tender['tender_id'], $purchased_tenders)): ?>
        <?php 
        // Fetch the tender document link if available
        $tender_document_sql = "SELECT tender_document_attachment FROM tenders WHERE tender_id = :tender_id";
        $tender_document_stmt = $pdo->prepare($tender_document_sql);
        $tender_document_stmt->execute(['tender_id' => $tender['tender_id']]);
        $tender_document = $tender_document_stmt->fetchColumn();
        ?>

        <!-- Check if the tender document exists and display the download link -->
        <?php if (!empty($tender_document)): ?>
            | <a href="<?php echo htmlspecialchars('../admin/' . $tender_document); ?>" target="_blank">Tender Document</a>
        <?php else: ?>
            | <span class="text-muted">No Tender Document</span>
        <?php endif; ?>
        
        <!-- Mark as Purchased -->
         <span class="text-muted">Purchased</span>
    <?php else: ?>
        <!-- Show Buy Tender option if not yet purchased -->
        | <a href="add_to_cart.php?tender_id=<?php echo $tender['tender_id']; ?>" onclick="return confirm('YOU ARE ABOUT TO PURCHASE THIS TENDER');">Buy The Tender</a>
    <?php endif; ?>



 </td>
                              
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No tenders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
</div>

<script src="path/to/jquery.js"></script>
<script src="path/to/bootstrap.js"></script>
<script src="path/to/jquery.dataTables.min.js"></script>
<script src="path/to/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>

</body>
</html>
