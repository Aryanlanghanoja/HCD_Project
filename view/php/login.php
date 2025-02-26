<?php
session_start();



// Redirect users away from login page if they are already logged in
if (isset($_SESSION['id'])) {
    switch ($_SESSION['role']) {
        case 'faculty':
            header('Location: ../php/faculty_dashboard.php"');
            exit();
        case 'student':
            header('Location: ../php/student_dashboard.php"');
            exit();
    }
}


$error = [];

// Include the database connection file
@include '../../config/db.config.php';

if (isset($_POST['submit'])) {
    try {
        // Sanitize inputs
        $enrollment_no = trim($_POST['enrollment_no']);
        $password = trim($_POST['password']);

        // Input Validations
        if (empty($enrollment_no)) {
            $error[] = "Email is required.";
        } elseif (!filter_var($enrollment_no, FILTER_VALIDATE_EMAIL)) {
            $error[] = "Invalid email format.";
        }

        if (empty($password)) {
            $error[] = "Password is required.";
        } elseif (strlen($password) < 6) {
            $error[] = "Password must be at least 6 characters long.";
        }

        // Proceed only if there are no errors
        if (empty($error)) {
            // Prepare the SQL query to fetch user data
            $select = "SELECT * FROM students WHERE enrollment_no = :email";
            $stmt = $conn->prepare($select);
            $stmt->bindParam(':email', $enrollment_no, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verify the password
                if (password_verify($password, $row['password'])) {
                    $_SESSION['first_name'] = $row['first_name'];
                    $_SESSION['last_name'] = $row['last_name'];
                    $_SESSION['enrollment_no'] = $row['enrollment_no'];
                    $_SESSION['university_id'] = $row['university_id'];     
                    $_SESSION['email'] = $row['email'];    
                    
                    // Set session variables based on user type
                    $_SESSION['role'] = $row['role']; 

                    switch ($row['role']) {
                        case 'faculty':
                            header('Location: ../php/faculty_dashboard.php"');
                            break;
                        case 'student':
                            header('Location: ../php/student_dashboard.php"');
                            break;
                        default:
                            $error[] = "Invalid user role.";
                    }
                    exit();
                } else {
                    $error[] = 'Incorrect enrollment no or password!';
                }
            } else {
                $error[] = 'Incorrect enrollment no or password!';
            }
        }

    } catch (PDOException $e) {
        $error[] = 'Database error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- ===== Iconscout CSS ===== -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="../css/login.css" />
    <link rel="shortcut icon" href="../../assets/images/Fevicon.svg" type="image/x-icon">
    <title>Login</title>
</head>
<body>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    
    // Prevent users from navigating back
    window.onload = function () {
        history.pushState(null, null, document.URL);
        window.addEventListener('popstate', function () {
            history.pushState(null, null, document.URL);
        });
    };
    
</script>
    <div class="container">
        <div class="forms">
            <div class="form login">
                <span class="title">Login</span>
                <?php
                if (!empty($error)) {
                    foreach ($error as $err) {
                        echo "<p style='color: red;'>$err</p>";
                    }
                }
                ?>
                <form action="" method="post">
                    <div class="input-field">
                        <input type="text" name="text" placeholder="Enter your Enrollment No" value="<?php echo htmlspecialchars($_POST['enrollment_no'] ?? ''); ?>" required />
                        <i class="uil uil-envelope icon"></i>
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
                        Not a Registered?
                        <a href="../Register/registration.php" class="text signup-link">Register</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <script src="../js/login.js"></script>
    
</body>
</html>
