<?php
session_start();
include '../../config/db.config.php';

$errors = []; // Initialize an empty array to store errors

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enrollment_no = $_POST['enrollment_no'];
    $password = $_POST['password'];

    try {
        // Check Students Table
        $stmt = $conn->prepare("SELECT enrollment_no, name, password FROM Students WHERE enrollment_no = :enrollment_no");
        $stmt->bindParam(':enrollment_no', $enrollment_no, PDO::PARAM_STR);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student && password_verify($password, $student["password"])) {
            $_SESSION['user'] = $student["enrollment_no"];
            $_SESSION['role'] = "student";
            header("Location: ./student_dashboard.php");
            exit();
        }

        // Check Admins Table
        $stmt = $conn->prepare("SELECT admin_id, name, password FROM Admins WHERE emp_id = :enrollment_no");
        $stmt->bindParam(':enrollment_no', $enrollment_no, PDO::PARAM_STR);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin["password"])) {
            $_SESSION['user'] = $admin["admin_id"];
            $_SESSION['role'] = "admin";
            header("Location: ./faculty_dashboard.php");
            exit();
        }

        // If login fails
        $errors[] = "Invalid enrollment number or password";
    } catch (PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <link rel="stylesheet" href="../css/login.css" />
    <link rel="shortcut icon" href="../../assets/images/Fevicon.svg" type="image/x-icon">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <div class="forms">
            <div class="form login">
                <span class="title">Login</span>

                <?php
                if (!empty($errors)) {
                    echo "<div style='color: red; margin-bottom: 10px;'>";
                    foreach ($errors as $error) {
                        echo "<p>$error</p>";
                    }
                    echo "</div>";
                }
                ?>

                <form action="./login.php" method="post">
                    <div class="input-field">
                        <input type="text" name="enrollment_no" placeholder="Enter your Enrollment No" 
                               value="<?php echo htmlspecialchars($_POST['enrollment_no'] ?? ''); ?>" required />
                        <i class="uil uil-user"></i>
                    </div>
                    <div class="input-field">
                        <input type="password" name="password" class="password" placeholder="Enter your password" required />
                        <i class="uil uil-lock icon"></i>
                        <i class="uil uil-eye-slash showHidePw"></i>
                    </div>
                    <div class="checkbox-text">
                        <div class="checkbox-content">
                            <input type="checkbox" id="logCheck" />
                            <label for="logCheck" class="text">Remember me</label>
                        </div>
                        <a href="#" class="text">Forgot password?</a>
                    </div>
                    <div class="input-field button">
                        <input type="submit" name="submit" value="Login" />
                    </div>
                </form>
                <div class="login-signup">
                    <span class="text">
                        Not Registered?
                        <a href="../php/registration.php" class="text signup-link">Register</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/login.js"></script>
</body>
</html>
