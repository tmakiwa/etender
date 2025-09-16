<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Include database connection file
include 'db.php';

if (isset($_POST['addtender'])) {
    // Retrieve and sanitize form data
    $tender_number = htmlspecialchars(trim($_POST['tender_number']));
    $tender_title = htmlspecialchars(trim($_POST['tender_title']));
	$tender_price = htmlspecialchars(trim($_POST['tender_price']));
    $tender_category = htmlspecialchars(trim($_POST['tender_category']));
    $tender_description = htmlspecialchars(trim($_POST['tender_description']));
    $tender_briefing_date = $_POST['tender_briefing_date'];
    $tender_briefing_location = htmlspecialchars(trim($_POST['tender_briefing_location']));
    $tender_closing_date = $_POST['tender_closing_date'];
    $tender_created_time = date('Y-m-d H:i:s');

    // File validation for Tender Invitation Attachment
    $invitation_file = $_FILES['tender_invitation_attachment'];
    $document_file = $_FILES['tender_document_attachment'];
    $allowed_types = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    $max_file_size = 10 * 1024 * 1024; // 10MB

    // Check for upload errors in Tender Invitation Attachment
    if ($invitation_file['error'] !== UPLOAD_ERR_OK) {
        switch ($invitation_file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                die("Invitation Attachment file is too large. Please upload a file under 10MB.");
                break;
            case UPLOAD_ERR_PARTIAL:
                die("Invitation Attachment file was only partially uploaded.");
                break;
            case UPLOAD_ERR_NO_FILE:
                die("No Invitation Attachment file was uploaded.");
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                die("Missing temporary folder for Invitation Attachment file.");
                break;
            case UPLOAD_ERR_CANT_WRITE:
                die("Failed to write Invitation Attachment file to disk.");
                break;
            case UPLOAD_ERR_EXTENSION:
                die("Invitation Attachment file upload stopped by a PHP extension.");
                break;
            default:
                die("Unknown error occurred while uploading Invitation Attachment file.");
                break;
        }
    }

    // Check for upload errors in Tender Document Attachment
    if ($document_file['error'] !== UPLOAD_ERR_OK) {
        switch ($document_file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                die("Document Attachment file is too large. Please upload a file under 10MB.");
                break;
            case UPLOAD_ERR_PARTIAL:
                die("Document Attachment file was only partially uploaded.");
                break;
            case UPLOAD_ERR_NO_FILE:
                die("No Document Attachment file was uploaded.");
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                die("Missing temporary folder for Document Attachment file.");
                break;
            case UPLOAD_ERR_CANT_WRITE:
                die("Failed to write Document Attachment file to disk.");
                break;
            case UPLOAD_ERR_EXTENSION:
                die("Document Attachment file upload stopped by a PHP extension.");
                break;
            default:
                die("Unknown error occurred while uploading Document Attachment file.");
                break;
        }
    }

    // Validate file type and size for Tender Invitation Attachment
    $invitation_file_extension = strtolower(pathinfo($invitation_file['name'], PATHINFO_EXTENSION));
    if ($invitation_file['size'] > $max_file_size || !in_array($invitation_file_extension, $allowed_types)) {
        die("Invalid Invitation Attachment file. File must be less than 10MB and in a valid format.");
    }

    // Validate file type and size for Tender Document Attachment
    $document_file_extension = strtolower(pathinfo($document_file['name'], PATHINFO_EXTENSION));
    if ($document_file['size'] > $max_file_size || !in_array($document_file_extension, $allowed_types)) {
        die("Invalid Document Attachment file. File must be less than 10MB and in a valid format.");
    }

    // Move uploaded files to the desired directory (ensure directory exists and is writable)
    $upload_dir = 'uploads/';
    $invitation_file_path = $upload_dir . uniqid() . '_' . basename($invitation_file['name']);
    $document_file_path = $upload_dir . uniqid() . '_' . basename($document_file['name']);

    if (!move_uploaded_file($invitation_file['tmp_name'], $invitation_file_path)) {
        die("Failed to upload Invitation Attachment.");
    }
    if (!move_uploaded_file($document_file['tmp_name'], $document_file_path)) {
        die("Failed to upload Document Attachment.");
    }

    // Insert data into the database
    $sql = "INSERT INTO tenders (tender_number, tender_title, tender_category, tender_description, tender_price,
            tender_briefing_date, tender_briefing_location, tender_invitation_attachment, 
            tender_document_attachment, tender_closing_date, tender_created_time) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $tender_number,
        $tender_title,
        $tender_category,
		$tender_price,
        $tender_description,
        $tender_briefing_date,
        $tender_briefing_location,
        $invitation_file_path,
        $document_file_path,
        $tender_closing_date,
        $tender_created_time
    ]);

    if ($result) {
        echo "Tender added successfully!";
    } else {
        echo "Failed to add tender. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Tender</title>
    <link rel="stylesheet" href="path/to/font-awesome.css">
</head>
<body>

<div class="box_general padding_bottom">
    <div class="header_box version_2">
        <h2><i class="fa fa-file"></i> Tender Information</h2>
    </div>
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tender Number</label>
                    <input type="text" name="tender_number" class="form-control" placeholder="Tender Number" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Tender Title</label>
                    <input type="text" name="tender_title" class="form-control" placeholder="Tender Title" required>
                </div>
            </div>	
        </div>	

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tender Category</label>
                    <select id="tender_category" name="tender_category" class="form-control" required>
                        <option value="">Select Category</option>
                        <option value="accounting-business-legal">Accounting Business Legal</option>
                        <option value="building-construction">Building Construction</option>
                        <option value="civil-mining">Civil Mining</option>
                        <option value="cleaning-management">Cleaning Management</option>
                        <option value="consultants">Consultants</option>
                        <!-- Add other options here as needed -->
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Closing Date</label>
                    <input type="datetime-local" name="tender_closing_date" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Tender Description</label>
            <textarea name="tender_description" class="form-control editor" placeholder="Tender Description" rows="4" required></textarea>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tender Invitation Attachment (Max 10MB)</label>
                    <input type="file" name="tender_invitation_attachment" class="form-control" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Tender Document Attachment (Max 10MB)</label>
                    <input type="file" name="tender_document_attachment" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Briefing Session</label>
                    <input type="datetime-local" name="tender_briefing_date" class="form-control" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Briefing Session Location</label>
                    <input type="text" name="tender_briefing_location" class="form-control" required>
                </div>
            </div>
        </div>

		<div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Selling Price ZAR</label>
                    <input type="text" name="selling_price" class="form-control" required>
                </div>
            </div>

            <div class="col-md-6">
          
            </div>
        </div>

        <button type="submit" name="addtender" class="btn_1 medium">Add Tender</button>
    </form>
</div
