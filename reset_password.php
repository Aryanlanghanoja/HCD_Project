<?php
session_start();
require './config/db.config.php';

$errors = [];
$success = "";

// Check if token is provided
if (!isset($_GET['token'])) {
    die("Invalid request.");
}

$token = $_GET['token'];

try {
    // Check for valid token in Students table
    $stmt = $conn->prepare("SELECT * FROM Students WHERE reset_token = :token AND token_expiry > NOW()");
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check in Admins table if not found
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

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_password = $_POST['password'];
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $identifier = $user["enrollment_no"] ?? $user["emp_id"];
        // Update password in database
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
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <?php if (!empty($errors)) echo "<p style='color:red'>" . implode("<br>", $errors) . "</p>"; ?>
    <?php if (!empty($success)) echo "<p style='color:green'>$success</p>"; ?>

    <form action="" method="post">
        <label>New Password:</label>
        <input type="password" name="password" required>
        <input type="submit" value="Update Password">
    </form>
</body>
</html>
