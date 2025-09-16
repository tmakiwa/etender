<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Check if the form is submitted
if (isset($_POST['addBEE'])) {
    // Retrieve and sanitize form data
    $company_number = htmlspecialchars(trim($_POST['company_number']));
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session after login


    // Check if the user has already uploaded their BEE information
    $check_sql = "SELECT * FROM BEE WHERE user_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$user_id]);
    $existing_entry = $check_stmt->fetch();

    if ($existing_entry) {
        // User has already uploaded BEE information, show a message and prevent further submission
        echo "<div class='error'>You have already uploaded your BEE information and cannot submit again.</div>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'dashboard.php'; // Replace with the page you want to redirect to
                }, 3000); // Redirects after 3 seconds
              </script>";
        exit;
    }

    // File validation for BEE Document
    $bee_document_file = $_FILES['bee_document'];
    $allowed_types = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    $max_file_size = 10 * 1024 * 1024; // 10MB

    if ($bee_document_file['error'] !== UPLOAD_ERR_OK) {
        die("Error uploading the document. Please try again.");
    }

    $file_extension = strtolower(pathinfo($bee_document_file['name'], PATHINFO_EXTENSION));
    if ($bee_document_file['size'] > $max_file_size || !in_array($file_extension, $allowed_types)) {
        die("Invalid file. File must be less than 10MB and in a valid format.");
    }

    // Move uploaded file to the desired directory (ensure directory exists and is writable)
    $upload_dir = 'uploads/';
    $bee_document_path = $upload_dir . uniqid() . '_' . basename($bee_document_file['name']);

    if (!move_uploaded_file($bee_document_file['tmp_name'], $bee_document_path)) {
        die("Failed to upload the BEE document.");
    }

    // Insert data into the database
    $sql = "INSERT INTO BEE (company_name, company_number, bee_document, date_of_upload, user_id, status)
            VALUES (?, ?, ?, NOW(), ?, 'Pending')";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $company_name,
        $company_number,
        $bee_document_path,
        $user_id
    ]);

    if ($result) {
        // Redirect to the success page
        echo "<div class='success'>BEE information uploaded successfully!</div>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'success_upload.php'; // Replace with your success page URL
                }, 3000); // Redirects after 3 seconds
              </script>";
        exit;
    } else {
        echo "Failed to add BEE information. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add BEE Information</title>
    <link rel="stylesheet" href="path/to/font-awesome.css">
</head>
<body>

<div class="box_general padding_bottom">
    <div class="header_box version_2">
        <h2><i class="fa fa-file"></i> BEE Information</h2>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Company Registration Number</label>
                    <input type="text" name="company_number" class="form-control" placeholder="Company Registration Number" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>BEE Document (Max 10MB)</label>
                    <input type="file" name="bee_document" class="form-control" required>
                </div>
            </div>
        </div>

        <button type="submit" name="addBEE" class="btn_1 medium">Submit BEE Information</button>
    </form>
</div>

</body>
</html>
