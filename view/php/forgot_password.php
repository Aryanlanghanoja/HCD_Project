<?php
session_start();
require '../../config/db.config.php';
require '../../vendor/autoload.php'; // Include PHPMailer
date_default_timezone_set('Asia/Kolkata'); // Change to your timezone

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enrollment_no = $_POST['enrollment_no'];

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
            // Generate a secure token
            $token = bin2hex(random_bytes(50));
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token expires in 1 hour

            // Store token in database
            $stmt = $conn->prepare("UPDATE " . ($role == "student" ? "Students" : "Admins") . " 
                                    SET reset_token = :token, token_expiry = :expiry 
                                    WHERE " . ($role == "student" ? "enrollment_no" : "emp_id") . " = :enrollment_no");
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expiry', $expiry);
            $stmt->bindParam(':enrollment_no', $enrollment_no);
            $stmt->execute();

            // Send reset email using PHPMailer
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = $_ENV['MAIL_HOST']; // Change to your SMTP host
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['MAIL_USERNAME']; // Your SMTP email
                $mail->Password = $_ENV['MAIL_PASSWORD']; // Your SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $_ENV['MAIL_PORT']; //

                $mail->setFrom($_ENV['MAIL_FROM_EMAIL'], $_ENV['MAIL_FROM_NAME']);
                $mail->addAddress($user['email']);

                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $reset_link = "http://localhost/hcd_project/reset_password.php?token=" . $token;
                $mail->Body = "<p>Click the link below to reset your password:</p>
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
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <?php if (!empty($errors)) echo "<p style='color:red'>" . implode("<br>", $errors) . "</p>"; ?>
    <?php if (!empty($success)) echo "<p style='color:green'>$success</p>"; ?>

    <form action="" method="post">
        <label>Enrollment No / Employee ID:</label>
        <input type="text" name="enrollment_no" required>
        <input type="submit" value="Reset Password">
    </form>
</body>
</html>
