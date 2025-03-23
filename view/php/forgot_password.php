<?php
session_start();
require '../../config/db.config.php';
require '../../vendor/autoload.php'; // Include PHPMailer
date_default_timezone_set('Asia/Kolkata'); // Set timezone

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enrollment_no = trim($_POST['enrollment_no']);

    try {
        // Check in Students table
        $stmt = $conn->prepare("SELECT * FROM Students WHERE enrollment_no = :enrollment_no");
        $stmt->bindParam(':enrollment_no', $enrollment_no, PDO::PARAM_STR);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check in Admins table if not found
        if (!$student) {
            $stmt = $conn->prepare("SELECT * FROM Admins WHERE emp_id = :enrollment_no");
            $stmt->bindParam(':enrollment_no', $enrollment_no, PDO::PARAM_STR);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $user = $student ?: $admin;
        $role = $student ? "student" : ($admin ? "admin" : null);

        if ($user) {
            // Generate secure token and expiry time
            $token = bin2hex(random_bytes(50));
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); 

            // Store token in database
            $stmt = $conn->prepare("
                UPDATE " . ($role === "student" ? "Students" : "Admins") . "
                SET reset_token = :token, token_expiry = :expiry
                WHERE " . ($role === "student" ? "enrollment_no" : "emp_id") . " = :enrollment_no
            ");
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expiry', $expiry);
            $stmt->bindParam(':enrollment_no', $enrollment_no);
            $stmt->execute();

            // Send reset email
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = $_ENV['MAIL_HOST'];
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['MAIL_USERNAME'];
                $mail->Password = $_ENV['MAIL_PASSWORD'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $_ENV['MAIL_PORT'];

                $mail->setFrom($_ENV['MAIL_FROM_EMAIL'], $_ENV['MAIL_FROM_NAME']);
                $mail->addAddress($user['email']);

                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $reset_link = "http://localhost/hcd_project/reset_password.php?token=" . $token;
                $mail->Body = "
                    <p>Click the link below to reset your password:</p>
                    <p><a href='$reset_link'>$reset_link</a></p>
                    <p>This link is valid for 1 hour.</p>";

                $mail->send();
                $success = "A password reset link has been sent to your email.";
            } catch (Exception $e) {
                $errors[] = "Mailer Error: " . $mail->ErrorInfo;
            }
        } else {
            $errors[] = "No account found with that Enrollment No / Employee ID.";
        }
    } catch (PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>

    <!-- Boxicons and Fonts -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    
    <style>
        /* ===== Google Font Import - Poppins ===== */
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
            max-width: 500px;
            width: 100%;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            margin: 0 20px;
            overflow: hidden;
        }

        .form {
            width: 100%;
            padding: 30px;
            background-color: #fff;
        }

        .title {
            font-size: 27px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            position: relative;
        }

        .title::before {
            content: '';
            position: absolute;
            left: 0;
            bottom: -5px;
            height: 3px;
            width: 30px;
            background-color: #D44C4C;
            border-radius: 25px;
        }

        /* Messages Styling */
        .error-messages, .success-message {
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
        }

        .error-messages {
            color: #D44C4C;
            background-color: rgba(212, 76, 76, 0.1);
        }

        .success-message {
            color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
        }

        /* Input Fields */
        .input-field {
            position: relative;
            height: 50px;
            width: 100%;
            margin-top: 20px;
        }

        .input-field input {
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
            z-index: 1;
        }

        .input-field i.icon {
            left: 10px;
        }

        .button {
            margin-top: 30px;
        }

        .button input {
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
<div class="container">
    <div class="form">
        <span class="title">Forgot Password</span>
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
        <form action="./forgot_password.php" method="post">
            <div class="input-field">
                <input type="text" name="enrollment_no" placeholder="Enter Enrollment No / Employee ID" required />
                <i class="uil uil-briefcase-alt icon"></i>
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
</body>
</html>
