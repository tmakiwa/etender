<?php


try {
    // Prepare and execute SQL statement
    $sql = "SELECT tender_id, tender_title, tender_category, tender_created_time, tender_closing_date FROM tenders";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Fetch all rows
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
    <title>View Tenders</title>
    <!-- Include any necessary CSS for styling and DataTables here -->
    <link rel="stylesheet" href="path/to/font-awesome.css">
    <link rel="stylesheet" href="path/to/bootstrap.css">
</head>
<body>

<!-- Example DataTables Card-->
<div class="card mb-3">
    <div class="card-header">
        <i class="fa fa-table"></i> Tenders
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tender ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Date Added</th>
                        <th>Due Date</th>
                        <th>Manage</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Tender ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Date Added</th>
                        <th>Due Date</th>
                        <th>Manage</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php if (!empty($tenders)): ?>
                        <?php foreach ($tenders as $tender): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($tender['tender_id']); ?></td>
                                <td><?php echo htmlspecialchars($tender['tender_title']); ?></td>
                                <td><?php echo htmlspecialchars($tender['tender_category']); ?></td>
                                <td><?php echo htmlspecialchars($tender['tender_created_time']); ?></td>
                                <td><?php echo htmlspecialchars($tender['tender_closing_date']); ?></td>
                                <td>
                                    <a href="index.php?page=view_tender.php&id=<?php echo $tender['tender_id']; ?>">Invite</a> | 
                                    <a href="index.php?page=view_tender.php&id=<?php echo $tender['tender_id']; ?>">View</a> |
                                    <a href="index.php?page=edit_tender.php&id=<?php echo $tender['tender_id']; ?>">Edit</a> |
                                    <a href="index.php?page=delete_tender.php&id=<?php echo $tender['tender_id']; ?>" onclick="return confirm('Are you sure you want to delete this tender?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No tenders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
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
