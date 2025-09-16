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
    <title>Admin Dashboard</title>
    <style>
        /* General styling for dashboard content */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .dashboard-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        .content-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            max-width: 800px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<?php include 'admin_nav.php'; ?>

<div class="dashboard-container">
    <h1>Admin Dashboard</h1>
    <div class="content-section">
        <h2>Welcome to the Admin Dashboard</h2>
        <p>Select an option from the tabs above to manage suppliers or tenders.</p>
    </div>
</div>

</body>
</html>
