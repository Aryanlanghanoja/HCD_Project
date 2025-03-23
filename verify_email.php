<?php
@include './config/db.config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        // Check if the token exists
        $query = "SELECT email FROM students WHERE verification_token = :token LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Update user to set email_verified to true
            $updateQuery = "UPDATE students SET email_verified = 1, verification_token = NULL WHERE verification_token = :token";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bindParam(':token', $token);
            $stmt->execute();

            echo "Your email has been verified! You can now <a href='./view/php/login.php'>log in</a>.";
        } else {
            echo "Invalid verification link or email already verified.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "No verification token provided.";
}
?>
