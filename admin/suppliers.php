<?php
include 'db.php';

try {
    // Prepare and execute SQL statement to fetch suppliers
    $sql = "SELECT id AS user_id, name, company_name, company_number, email AS company_email, company_phone AS company_phone, created_at FROM users WHERE role = 'supplier'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Fetch all suppliers
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Suppliers</title>
    <link rel="stylesheet" href="path/to/font-awesome.css">
    <link rel="stylesheet" href="path/to/bootstrap.css">
    <link rel="stylesheet" href="path/to/dataTables.bootstrap4.min.css">
</head>
<body>

<!-- Example DataTables Card-->
<div class="card mb-3">
    <div class="card-header">
        <i class="fa fa-table"></i> Suppliers
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Company Name</th>
                        <th>Company Number</th>
                        <th>Company Email</th>
                        <th>Company Phone</th>
                        <th>Registered Date</th>
                        <th>Manage</th>
                    </tr>
                </thead>
            
                <tbody>
                    <?php if (!empty($suppliers)): ?>
                        <?php foreach ($suppliers as $supplier): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($supplier['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['name']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['company_name']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['company_number']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['company_email']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['company_phone']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['created_at']); ?></td>
                                <td>
                                    <a href="index.php?page=viewsuppliers.php&id=<?php echo $supplier['user_id']; ?>">View and Update</a> | 
                                    <a href="index.php?page=delete_supplier.php&id=<?php echo $supplier['user_id']; ?>" onclick="return confirm('Are you sure you want to delete this supplier?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No suppliers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- /tables-->

<!-- Include necessary JavaScript for DataTables functionality -->
<script src="path/to/jquery.js"></script>
<script src="path/to/bootstrap.js"></script>
<script src="path/to/jquery.dataTables.min.js"></script>
<script src="path/to/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable(); // Initialize DataTables
    });
</script>

</body>
</html>
