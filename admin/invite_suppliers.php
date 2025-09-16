<?php


// Get the tender ID from the URL parameter
$tender_id = isset($_GET['tender_id']) ? intval($_GET['tender_id']) : 0;

// Check if the tender_id is valid
if ($tender_id == 0) {
    echo "Invalid tender ID.";
    exit;
}

// Fetch tender details
try {
    $tender_sql = "SELECT tender_title, tender_category, tender_created_time, tender_closing_date FROM tenders WHERE tender_id = ?";
    $tender_stmt = $pdo->prepare($tender_sql);
    $tender_stmt->execute([$tender_id]);
    $tender_details = $tender_stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Fetch all suppliers
try {
    $suppliers_sql = "SELECT id, name, company_name, email FROM users WHERE role = 'supplier'";
    $suppliers_stmt = $pdo->prepare($suppliers_sql);
    $suppliers_stmt->execute();
    $suppliers = $suppliers_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Process form submission to invite selected suppliers
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['suppliers'])) {
    $selected_suppliers = $_POST['suppliers'];
    $invited_count = 0;

    try {
        // Prepare SQL statements
        $check_invite_sql = "SELECT COUNT(*) FROM invited_tenders WHERE tender_id = :tender_id AND user_id = :user_id";
        $check_invite_stmt = $pdo->prepare($check_invite_sql);

        $invite_sql = "INSERT INTO invited_tenders (tender_id, user_id) VALUES (:tender_id, :user_id)";
        $invite_stmt = $pdo->prepare($invite_sql);

        foreach ($selected_suppliers as $supplier_id) {
            // Check if the supplier has already been invited for this tender
            $check_invite_stmt->execute([':tender_id' => $tender_id, ':user_id' => intval($supplier_id)]);
            $already_invited = $check_invite_stmt->fetchColumn();

            if (!$already_invited) {
                // Invite supplier if they haven't been invited yet
                $invite_stmt->execute([':tender_id' => $tender_id, ':user_id' => intval($supplier_id)]);
                $invited_count++;
                
                // Send email to each invited supplier
                foreach ($suppliers as $supplier) {
                    if ($supplier['id'] == $supplier_id) {
                        $recipient = $supplier['email'];
                        $postmark_data = [
                            "From" => "info@etender.co.bw", // Replace with your sender email
                            "To" => $recipient,
                            "Subject" => "Invitation to Tender: " . $tender_details['tender_title'],
                            "TextBody" => "Dear " . $supplier['name'] . ",\n\nYou have been invited to participate in the following tender:\n\n" .
                                          "Title: " . $tender_details['tender_title'] . "\n" .
                                          "Category: " . $tender_details['tender_category'] . "\n" .
                                          "Created On: " . $tender_details['tender_created_time'] . "\n" .
                                          "Closing Date: " . $tender_details['tender_closing_date'] . "\n\n" .
                                          "Please log in to the portal to review the details and submit your response.\n\nBest regards,\netender"
                        ];

                        // Encode payload to JSON
                        $postmark_data = json_encode($postmark_data);

                        // Set cURL options
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "https://api.postmarkapp.com/email");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postmark_data);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            "Accept: application/json",
                            "Content-Type: application/json",
                            "X-Postmark-Server-Token: 80946ce0-c815-4a7f-b2d6-ea5e65a2da10" // Replace with your Postmark Server Token
                        ));

                        // Execute cURL request
                        $result = curl_exec($ch);

                        // Check for errors
                        if (curl_errno($ch)) {
                            echo 'Error:' . curl_error($ch);
                        } else {
                            // Decode JSON response
                            $response = json_decode($result, true);

                            // Check if email was sent successfully
                            if ($response['Message'] == 'OK') {
                                echo "<div class='success'>Email sent successfully to $recipient.</div>";
                            } else {
                                echo "<script>alert('Failed to send email to $recipient');</script>";
                            }
                        }

                        // Close cURL session
                        curl_close($ch);
                    }
                }
            }
        }

        if ($invited_count > 0) {
            echo "<div class='success'>{$invited_count} suppliers have been invited successfully!</div>";
        } else {
            echo "<div class='info'>All selected suppliers have already been invited to this tender.</div>";
        }
    } catch (PDOException $e) {
        echo "Error inviting suppliers: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite Suppliers</title>
    <link rel="stylesheet" href="path/to/font-awesome.css">
    <link rel="stylesheet" href="path/to/bootstrap.css">
</head>
<body>

<div class="container mt-5">
    <h2>Invite Suppliers for Tender ID: <?php echo htmlspecialchars($tender_id); ?></h2>

    <form method="POST" action="">
        <div class="form-group">
            <label for="suppliers">Select Suppliers to Invite:</label>
            <select id="suppliers" name="suppliers[]" class="form-control" multiple required>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?php echo htmlspecialchars($supplier['id']); ?>">
                        <?php echo htmlspecialchars($supplier['company_name']) . " (" . htmlspecialchars($supplier['name']) . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Invite Selected Suppliers</button>
        <a href="index.php?page=tendermanagement.php" class="btn btn-secondary">Back to Tenders</a>
    </form>
</div>

<!-- Include necessary JS for Bootstrap and other libraries -->
<script src="path/to/jquery.js"></script>
<script src="path/to/bootstrap.js"></script>
</body>
</html>
