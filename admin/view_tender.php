<?php


// Include database connection file


// Check if `id` is set in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $tender_id = $_GET['id'];

    try {
        // Prepare and execute SQL statement
        $sql = "SELECT * FROM tenders WHERE tender_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tender_id]);

        // Fetch the tender information
        $tender = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if tender exists
        if (!$tender) {
            echo "Tender not found.";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    echo "Invalid Tender ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Tender Details</title>
    <link rel="stylesheet" href="path/to/font-awesome.css">
    <link rel="stylesheet" href="path/to/bootstrap.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
        .card-header {
            font-weight: bold;
        }
        .card-body label {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            Tender Details
        </div>
        <div class="card-body">
            <p><label>Tender ID:</label> <?php echo htmlspecialchars($tender['tender_id']); ?></p>
            <p><label>Tender Number:</label> <?php echo htmlspecialchars($tender['tender_number']); ?></p>
            <p><label>Tender Title:</label> <?php echo htmlspecialchars($tender['tender_title']); ?></p>
            <p><label>Tender Category:</label> <?php echo htmlspecialchars($tender['tender_category']); ?></p>
            <p><label>Description:</label> <?php echo htmlspecialchars($tender['tender_description']); ?></p>
            <p><label>Briefing Date:</label> <?php echo htmlspecialchars($tender['tender_briefing_date']); ?></p>
            <p><label>Briefing Location:</label> <?php echo htmlspecialchars($tender['tender_briefing_location']); ?></p>
            <p><label>Closing Date:</label> <?php echo htmlspecialchars($tender['tender_closing_date']); ?></p>
            <p><label>Date Created:</label> <?php echo htmlspecialchars($tender['tender_created_time']); ?></p>
            <p><label>Invitation Attachment:</label> 
                <a href="<?php echo htmlspecialchars($tender['tender_invitation_attachment']); ?>" target="_blank">View Invitation Attachment</a>
            </p>
            <p><label>Document Attachment:</label> 
                <a href="<?php echo htmlspecialchars($tender['tender_document_attachment']); ?>" target="_blank">View Document Attachment</a>
            </p>
        </div>
    </div>
</div>

<!-- Include necessary JS for Bootstrap -->
<script src="path/to/jquery.js"></script>
<script src="path/to/bootstrap.js"></script>

</body>
</html>
