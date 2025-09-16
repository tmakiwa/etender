<?php


// Check if the form is submitted
if (isset($_POST['addSupplier'])) {
    // Retrieve and sanitize form data
    $company_name = htmlspecialchars(trim($_POST['company_name']));
    $registration_number = htmlspecialchars(trim($_POST['registration_number']));
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session after login

    // Check if the user has already added their information
    $check_sql = "SELECT * FROM COR_14_3 WHERE user_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$user_id]);
    $existing_entry = $check_stmt->fetch();

    if ($existing_entry) {
        // User has already added information, show a message and prevent further submission
        echo "<div class='error'>You have already added your information and cannot submit again.</div>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php?page=companyprofile.php'; // Replace with the page you want to redirect to
                }, 3000); // Redirects after 3 seconds
              </script>";
        exit;
    }

    // File validation for Document Attachment
    $document_file = $_FILES['document'];
    $allowed_types = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    $max_file_size = 10 * 1024 * 1024; // 10MB

    // Check for upload errors in Document Attachment
    if ($document_file['error'] !== UPLOAD_ERR_OK) {
        switch ($document_file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                die("Document file is too large. Please upload a file under 10MB.");
            case UPLOAD_ERR_PARTIAL:
                die("Document file was only partially uploaded.");
            case UPLOAD_ERR_NO_FILE:
                die("No document file was uploaded.");
            case UPLOAD_ERR_NO_TMP_DIR:
                die("Missing temporary folder for document file.");
            case UPLOAD_ERR_CANT_WRITE:
                die("Failed to write document file to disk.");
            case UPLOAD_ERR_EXTENSION:
                die("Document file upload stopped by a PHP extension.");
            default:
                die("Unknown error occurred while uploading document file.");
        }
    }

    // Validate file type and size for Document Attachment
    $document_file_extension = strtolower(pathinfo($document_file['name'], PATHINFO_EXTENSION));
    if ($document_file['size'] > $max_file_size || !in_array($document_file_extension, $allowed_types)) {
        die("Invalid document file. File must be less than 10MB and in a valid format.");
    }

    // Move uploaded file to the desired directory (ensure directory exists and is writable)
    $upload_dir = 'uploads/';
    $document_file_path = $upload_dir . uniqid() . '_' . basename($document_file['name']);

    if (!move_uploaded_file($document_file['tmp_name'], $document_file_path)) {
        die("Failed to upload document file.");
    }

    // Insert data into the database
    $sql = "INSERT INTO COR_14_3 (company_name, registration_number, document, date_of_upload, user_id)
            VALUES (?, ?, ?, NOW(), ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $company_name,
        $registration_number,
        $document_file_path,
        $user_id
    ]);

    if ($result) {
        // Display success message and add a delayed redirection
        echo "<div class='success'>Supplier information added successfully!</div>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'success_page.php'; // Replace with your desired URL
                }, 3000); // Redirects after 3 seconds
              </script>";
        exit;
    } else {
        echo "Failed to add supplier information. Please try again.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier Information</title>
    <link rel="stylesheet" href="path/to/font-awesome.css">
</head>
<body>

<div class="box_general padding_bottom">
    <div class="header_box version_2">
        <h2><i class="fa fa-file"></i> Company Information</h2>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Company Name</label>
                    <input type="text" name="company_name" class="form-control" placeholder="Company Name" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Registration Number</label>
                    <input type="text" name="registration_number" class="form-control" placeholder="Registration Number" required>
                </div>
            </div>    
        </div>  

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Upload Document COR 13.3 Form (Max 10MB)</label>
                    <input type="file" name="document" class="form-control" required>
                </div>
            </div>
        </div>

        <button type="submit" name="addSupplier" class="btn_1 medium">Add Company Info</button>
    </form>
</div>

</body>
</html>
