<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Suppliers</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <style>
        /* General styling for the table */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
    </style>
</head>
<body>

<?php include 'admin_nav.php'; ?>

<h2>Registered Suppliers</h2>
<table id="suppliersTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Company Name</th>
            <th>Registration Date</th>
            <th>Update Supplier</th>
        </tr>
    </thead>
    <tbody>
        <?php
        include 'db.php';
        $stmt = $pdo->query("SELECT * FROM users WHERE role = 'supplier'");
        while ($row = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['company_name'] . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            // In the table row loop, add Edit/Delete buttons
echo "<td><a href='edit_supplier.php?id=" . $row['id'] . "'>Edit</a> | <a href='delete_supplier.php?id=" . $row['id'] . "'>Delete</a></td>";

            echo "</tr>";
        }
        ?>
    </tbody>
</table>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#suppliersTable').DataTable();
    });
</script>

</body>
</html>
