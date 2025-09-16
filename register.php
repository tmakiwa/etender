<?php
// Include database connection file
include 'db.php';

if (isset($_POST['register'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = htmlspecialchars(trim($_POST['name']));
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $company_name = htmlspecialchars(trim($_POST['company_name']));
        $company_number = htmlspecialchars(trim($_POST['company_number']));
        $company_phone = htmlspecialchars(trim($_POST['company_phone']));

        // Check if email already exists
        $checkEmail = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $checkEmail->execute([$email]);

        if ($checkEmail->rowCount() > 0) {
            echo "<div class='error'>Email already exists!</div>";
        } else {
            // Insert user into database
            $sql = "INSERT INTO users (name, email, password, company_name, company_number, company_phone) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$name, $email, $password, $company_name, $company_number, $company_phone])) {
                echo "<div class='success'>Registration successful!</div>";
            } else {
                echo "<div class='error'>Error: Could not register user.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .error {
            color: red;
            font-size: 14px;
        }
        .success {
            color: green;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Register</h2>
    <form id="registrationForm" method="POST" action="" onsubmit="return validateForm()">
        <input type="text" id="name" name="name" placeholder="Full Name" required>
        <span id="nameError" class="error"></span>
             
        <input type="text" id="company_name" name="company_name" placeholder="Company Name" required>
        <input type="text" id="company_number" name="company_number" placeholder="Company Registration Number" required>
        <input type="text" id="company_phone" name="company_phone" placeholder="Company Phone" required>
        
        <input type="email" id="email" name="email" placeholder="Email" required>
        <span id="emailError" class="error"></span>
        
        <input type="password" id="password" name="password" placeholder="Password" required>
        <span id="passwordError" class="error"></span>
   
        <button type="submit" name="register">Register</button>
    </form>
</div>

<script>
    function validateForm() {
        let name = document.getElementById("name").value;
        let email = document.getElementById("email").value;
        let password = document.getElementById("password").value;
        let valid = true;
        
        document.getElementById("nameError").innerText = "";
        document.getElementById("emailError").innerText = "";
        document.getElementById("passwordError").innerText = "";

        if (name === "") {
            document.getElementById("nameError").innerText = "Name is required.";
            valid = false;
        }

        if (email === "" || !email.includes('@')) {
            document.getElementById("emailError").innerText = "Valid email is required.";
            valid = false;
        }

        if (password.length < 6) {
            document.getElementById("passwordError").innerText = "Password must be at least 6 characters.";
            valid = false;
        }

        return valid;
    }
</script>

</body>
</html>
