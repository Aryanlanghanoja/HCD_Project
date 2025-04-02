<?php
session_start();
require './config/db.config.php';

$errors = [];
$success = "";

if (!isset($_GET['token'])) {
    die("Invalid request.");
}

$token = $_GET['token'];

try {
    $stmt = $conn->prepare("SELECT * FROM Students WHERE reset_token = :token AND token_expiry > NOW()");
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $stmt = $conn->prepare("SELECT * FROM Admins WHERE reset_token = :token AND token_expiry > NOW()");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $user = $student ?: $admin;
    $role = $student ? "student" : ($admin ? "admin" : null);

    if (!$user) {
        die("Invalid or expired token.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_password = $_POST['password'];
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $identifier = $user["enrollment_no"] ?? $user["emp_id"];

        $stmt = $conn->prepare("UPDATE " . ($role == "student" ? "Students" : "Admins") . " 
                                SET password = :password, reset_token = NULL, token_expiry = NULL 
                                WHERE " . ($role == "student" ? "enrollment_no" : "emp_id") . " = :enrollment_no");
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':enrollment_no', $identifier);
        $stmt->execute();

        $success = "Password updated successfully. <a href='./view/php/login.php'>Login here</a>";
    }
} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
     <!-- Add your existing sidebar CSS file -->
    <style>
/* ===== Google Font Import - Poppins ===== */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #02959F;
}

.container {
    position: relative;
    max-width: 500px;
    width: 100%;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin: 0 20px;
}

.container .forms {
    display: flex;
    align-items: center;
    width: 100%;
}

.container .form {
    width: 100%;
    padding: 30px;
    background-color: #fff;
}

.container .form .title {
    position: relative;
    font-size: 27px;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
}

.form .title::before {
    content: '';
    position: absolute;
    left: 0;
    bottom: -5px;
    height: 3px;
    width: 30px;
    background-color: #D44C4C;
    border-radius: 25px;
}

/* Error messages styling */
.form .error-messages {
    margin-top: 15px;
    color: #D44C4C;
    margin-bottom: 15px;
    background-color: rgba(212, 76, 76, 0.1);
    padding: 10px;
    border-radius: 5px;
    font-size: 14px;
}

.form .success-message {
    max-width: 15px;
    color: #28a745;
    margin-bottom: 15px;
    background-color: rgba(40, 167, 69, 0.1);
    padding: 10px;
    border-radius: 5px;
    font-size: 14px;
}

.form .input-field {
    position: relative;
    height: 50px;
    width: 100%;
    margin-top: 20px;
}

.input-field input {
    position: absolute;
    height: 100%;
    width: 100%;
    padding: 0 35px;
    outline: none;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.input-field input:focus {
    border-color: #D44C4C;
}

.input-field i {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 20px;
    transition: all 0.2s ease;
    z-index: 1;
}

.input-field input:focus ~ i {
    color: #D44C4C;
}

.input-field i.icon {
    left: 10px;
}

.input-field i.showHidePw {
    right: 10px;
    cursor: pointer;
}

.form .button {
    margin-top: 30px;
}

.form .button input {
    border: none;
    color: #fff;
    font-size: 17px;
    font-weight: 500;
    letter-spacing: 1px;
    border-radius: 6px;
    background-color: #D44C4C;
    cursor: pointer;
    transition: all 0.3s ease;
    height: 50px;
    width: 100%;
}

.button input:hover {
    background-color: #780000;
}

.form .login-signup {
    margin-top: 25px;
    text-align: center;
}

.form .login-signup .text {
    color: #D44C4C;
    font-size: 14px;
}

.form a.text {
    color: #D44C4C;
    font-weight: 500;
    text-decoration: none;
}

.form a:hover {
    text-decoration: none;
}

.form .login-signup {
    margin-top: 30px;
    text-align: center;
    text-decoration: none;
}

.login-signup {
            margin-top: 25px;
            text-align: center;
        }

        .login-signup a {
            color: #D44C4C;
            font-weight: 500;
            text-decoration: none;
        }

        .login-signup a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>


<!-- Main Content -->
<div class="container">
    <div class="form">
        <span class="title">Reset Password</span>
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
            <div class="success-message">
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>
        <form action="" method="post">
            <div class="input-field">
                <input type="password" name="password" class="password" placeholder="Enter your password" required />
                <i class="uil uil-lock icon"></i>
                <i class="uil uil-eye-slash showHidePw"></i>
            </div>
            
            <div class="button">
                <input type="submit" value="Reset Password" />
            </div>
        </form>
        
        <div class="login-signup">
            <a href="./login.php">Return to Login</a>
        </div>
    </div>
</div>

<script>
            // Show/hide password functionality
            const pwField = document.querySelector(".password");
        const pwShowHide = document.querySelector(".showHidePw");
        
        pwShowHide.addEventListener("click", () => {
            if (pwField.type === "password") {
                pwField.type = "text";
                pwShowHide.classList.replace("uil-eye-slash", "uil-eye");
            } else {
                pwField.type = "password";
                pwShowHide.classList.replace("uil-eye", "uil-eye-slash");
            }
        });
</script>
</body>
</html>
