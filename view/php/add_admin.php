<?php
session_start();
include '../../config/db.config.php';

$errors = [];
$success = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $emp_id = trim($_POST["emp_id"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $phone_no = trim($_POST["phone_no"]);

    // Validation
    if (empty($name) || empty($emp_id) || empty($email) || empty($password)) {
        $errors[] = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }

    if (empty($errors)) {
        try {
            // Check for duplicate Employee ID or Email
            $stmt = $conn->prepare("SELECT * FROM Admins WHERE emp_id = :emp_id OR email = :email");
            $stmt->execute(['emp_id' => $emp_id, 'email' => $email]);
            $existing_admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_admin) {
                $errors[] = "Admin with this Employee ID or Email already exists!";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Insert new admin
                $stmt = $conn->prepare("INSERT INTO Admins (name, emp_id, email, password, phone_no) 
                                      VALUES (:name, :emp_id, :email, :password, :phone_no)");
                $stmt->execute([
                    'name' => $name,
                    'emp_id' => $emp_id,
                    'email' => $email,
                    'password' => $hashed_password,
                    'phone_no' => $phone_no
                ]);

                $success = "Admin added successfully!";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="../css/add_admin.css">
    <link rel="shortcut icon" href="../../assets/images/Fevicon.svg" type="image/x-icon">
    <title>Add Admin</title>
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
                        <input type="text" name="name" placeholder="Enter the Admin Name" 
                               required />
                        <i class="uil uil-user icon"></i>
                    </div>
                    
                    <div class="input-field">
                        <input type="text" name="emp_id" placeholder="Enter the Employment ID" 
                                required />
                               <i class="uil uil-briefcase-alt icon"></i>
                    </div>
                    
                    <div class="input-field">
                        <input type="email" name="email" placeholder="Enter the Email Address" 
                               required />
                        <i class="uil uil-envelope icon"></i>
                    </div>
                    
                    <div class="input-field">
                        <input type="text" name="phone_no" placeholder="Enter the Phone Number" 
                               required/>
                        <i class="uil uil-phone icon"></i>
                    </div>
                    
                    <div class="input-field">
                        <input type="password" name="password" class="password" placeholder="Enter your password" required />
                        <i class="uil uil-lock icon"></i>
                        <i class="uil uil-eye-slash showHidePw"></i>
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