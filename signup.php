<?php
// Include database connection file
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $company_name = $_POST['company_name'];
    $company_number = $_POST['company_number'];
    $company_phone = $_POST['company_phone'];

    // Check if email already exists
    $checkEmail = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $checkEmail->execute([$email]);

    if ($checkEmail->rowCount() > 0) {
        echo "Email already exists!";
    } else {
        // Insert user into database
        $sql = "INSERT INTO users (name, email, password, company_name) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$name, $email, $password, $company_name])) {
            echo "Registration successful!";
        } else {
            echo "Error: Could not register user.";
        }
    }
}
?>
