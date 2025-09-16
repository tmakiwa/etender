<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        // Prepare and execute the SQL statement
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verify password and set session variables if valid
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['email'] = $user['email']; // Set email in session for access in other pages
            $_SESSION['role'] = $user['role'];

            // Set a cookie if "Remember Me" was checked
            if (isset($_POST['remember'])) {
                setcookie("user_id", $user['id'], time() + (86400 * 30), "/"); // Cookie lasts for 30 days
            }

            // Redirect based on user role
            if ($user['role'] == 'admin') {
                header("Location: admin/index.php");
                exit();
            } else {
                header("Location: supplier/index.php");
                exit();
            }
        } else {
            echo "<div class='error'>Invalid email or password!</div>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .remember-me, .signup-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Login</h2>

    <form id="loginForm" method="POST" action="" onsubmit="return validateLoginForm()">
        <input type="email" id="loginEmail" name="email" placeholder="Email" required>
        <span id="loginEmailError" class="error"></span>
        
        <input type="password" id="loginPassword" name="password" placeholder="Password" required>
        <span id="loginPasswordError" class="error"></span>
        
        <div class="remember-me">
            <label><input type="checkbox" name="remember"> Remember Me</label>
            <a href="forgot_password.php">Forgot Password?</a>
        </div>

        <button type="submit" name="login" value="Login">Login</button>
    </form>
    
    <div class="signup-link">
        <p>Don’t have an account? <a href="register.php">Sign up here</a>.</p>
    </div>
</div>

<script>
    function validateLoginForm() {
        let email = document.getElementById("loginEmail").value;
        let password = document.getElementById("loginPassword").value;
        let valid = true;
        
        document.getElementById("loginEmailError").innerText = "";
        document.getElementById("loginPasswordError").innerText = "";

        if (email === "" || !email.includes('@')) {
            document.getElementById("loginEmailError").innerText = "Valid email is required.";
            valid = false;
        }

        if (password.length < 6) {
            document.getElementById("loginPasswordError").innerText = "Password must be at least 6 characters.";
            valid = false;
        }

        return valid;
    }
</script>

</body>
</html>
