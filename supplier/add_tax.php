<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



// Check if the form is submitted
if (isset($_POST['addTax'])) {
    // Retrieve and sanitize form data
    $tax_number = htmlspecialchars(trim($_POST['tax_number']));
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session after login

    // Check if the user has already uploaded their tax information
    $check_sql = "SELECT * FROM tax WHERE user_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$user_id]);
    $existing_entry = $check_stmt->fetch();

    if ($existing_entry) {
        // User has already uploaded tax information, show a message and prevent further submission
        echo "<div class='error'>You have already uploaded your tax information and cannot submit again.</div>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'dashboard.php'; // Replace with the page you want to redirect to
                }, 3000); // Redirects after 3 seconds
              </script>";
        exit;
    }

    // File validation for Tax Clearance Document
    $tax_clearance_file = $_FILES['tax_clearance'];
    $allowed_types = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    $max_file_size = 10 * 1024 * 1024; // 10MB

    if ($tax_clearance_file['error'] !== UPLOAD_ERR_OK) {
        die("Error uploading the document. Please try again.");
    }

    $file_extension = strtolower(pathinfo($tax_clearance_file['name'], PATHINFO_EXTENSION));
    if ($tax_clearance_file['size'] > $max_file_size || !in_array($file_extension, $allowed_types)) {
        die("Invalid file. File must be less than 10MB and in a valid format.");
    }

    // Move uploaded file to the desired directory (ensure directory exists and is writable)
    $upload_dir = 'uploads/';
    $tax_clearance_path = $upload_dir . uniqid() . '_' . basename($tax_clearance_file['name']);

    if (!move_uploaded_file($tax_clearance_file['tmp_name'], $tax_clearance_path)) {
        die("Failed to upload the tax clearance document.");
    }

    // Insert data into the database
    $sql = "INSERT INTO tax (company_name, tax_number, tax_clearance, date_of_upload, user_id, status)
            VALUES (?, ?, ?, NOW(), ?, 'Pending')";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $company_name, // Assuming company name is stored in session
        $tax_number,
        $tax_clearance_path,
        $user_id
    ]);

    if ($result) {
        // Redirect to the success page
        echo "<div class='success'>Tax information uploaded successfully!</div>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'success_upload.php'; // Replace with your success page URL
                }, 3000); // Redirects after 3 seconds
              </script>";
        exit;
    } else {
        echo "Failed to add tax information. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Tax Information</title>
    <link rel="stylesheet" href="path/to/font-awesome.css">
</head>
<body>

<div class="box_general padding_bottom">
    <div class="header_box version_2">
        <h2><i class="fa fa-file"></i> Tax Information</h2>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tax Number</label>
                    <input type="text" name="tax_number" class="form-control" placeholder="Tax Number" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Tax Clearance Document (Max 10MB)</label>
                    <input type="file" name="tax_clearance" class="form-control" required>
                </div>
            </div>
        </div>

        <button type="submit" name="addTax" class="btn_1 medium">Submit Tax Information</button>
    </form>
</div>

</body>
</html>
