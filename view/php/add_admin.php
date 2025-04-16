<?php
session_start();
require '../../vendor/autoload.php'; // Composer autoload
include '../../config/db.config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION["user"])) {
    // User not logged in
    header("Location: ./login.php");
    exit();
}

// Allow only user with ID 603 to access this page
if ($_SESSION["user"] !== "603") {
    if($_SESSION['role'] == "student"){
        header("Location: ./student_dashboard.php");
                exit();
    }

    elseif($_SESSION['role'] == "admin"){
        header("Location: ./faculty_dashboard.php");
        exit();
    }
}


// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$errors = [];
$success = "";

// Function to generate random password
function generateRandomPassword($length = 10) {
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%'), 0, $length);
}

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $emp_id = trim($_POST["emp_id"]);
    $email = trim($_POST["email"]);
    $phone_no = trim($_POST["phone_no"]);
    $password = generateRandomPassword();

    if (empty($name) || empty($emp_id) || empty($email)) {
        $errors[] = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }

    if (empty($errors)) {
        try {
            // Check for existing admin
            $stmt = $conn->prepare("SELECT * FROM Admins WHERE emp_id = :emp_id OR email = :email");
            $stmt->execute(['emp_id' => $emp_id, 'email' => $email]);
            $existing_admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_admin) {
                $errors[] = "Admin with this Employee ID or Email already exists!";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Insert admin
                $stmt = $conn->prepare("INSERT INTO Admins (name, emp_id, email, password, phone_no) 
                                        VALUES (:name, :emp_id, :email, :password, :phone_no)");
                $stmt->execute([
                    'name' => $name,
                    'emp_id' => $emp_id,
                    'email' => $email,
                    'password' => $hashed_password,
                    'phone_no' => $phone_no
                ]);

                // Send email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = $_ENV['MAIL_HOST'];
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $_ENV['MAIL_USERNAME'];
                    $mail->Password   = $_ENV['MAIL_PASSWORD'];
                    $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
                    $mail->Port       = $_ENV['MAIL_PORT'];

                    $mail->setFrom($_ENV['MAIL_FROM_EMAIL'], $_ENV['MAIL_FROM_NAME']);
                    $mail->addAddress($email, $name);

                    $mail->isHTML(true);
                    $mail->Subject = "Your Admin Account Credentials";
                    $mail->Body    = "
                        <h3>Hello $name,</h3>
                        <p>Your admin account has been successfully created.</p>
                        <p><strong>Login ID:</strong> $emp_id</p>
                        <p><strong>Temporary Password:</strong> $password</p>
                        <p>Please change your password after your first login.</p>
                        <br>
                        <p>Regards,<br>{$_ENV['MAIL_FROM_NAME']}</p>
                    ";

                    $mail->send();
                    $success = "Admin added successfully! Credentials have been emailed.";
                } catch (Exception $e) {
                    $errors[] = "Admin added, but email failed: {$mail->ErrorInfo}";
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!-- HTML Part -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin</title>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="../css/add_admin.css">
    <link rel="shortcut icon" href="../../assets/images/Fevicon.svg" type="image/x-icon">
</head>
<body>
    <div class="container">
        <div class="forms">
            <div class="form">
                <span class="title">Add Admin</span>

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

                <form action="./add_admin.php" method="post">
                    <div class="input-field">
                        <input type="text" name="name" placeholder="Enter the Admin Name" required />
                        <i class="uil uil-user icon"></i>
                    </div>
                    <div class="input-field">
                        <input type="text" name="emp_id" placeholder="Enter the Employment ID" required />
                        <i class="uil uil-briefcase-alt icon"></i>
                    </div>
                    <div class="input-field">
                        <input type="email" name="email" placeholder="Enter the Email Address" required />
                        <i class="uil uil-envelope icon"></i>
                    </div>
                    <div class="input-field">
                        <input type="text" name="phone_no" placeholder="Enter the Phone Number" required />
                        <i class="uil uil-phone icon"></i>
                    </div>
                    <div class="input-field button">
                        <input type="submit" value="Add Admin" />
                    </div>
                </form>

                <div class="login-signup">
                    <span class="text">
                        <a href="./faculty_dashboard.php" class="text signup-link">Return to Dashboard</a>
                    </span>
                </div>
            </div>
        </div>
    </div>

    
</body>
</html>
