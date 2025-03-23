<?php
session_start();
include '../../config/db.config.php';



$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $emp_id = trim($_POST["emp_id"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $phone_no = trim($_POST["phone_no"]);

    // Validate input fields
    if (empty($name) || empty($emp_id) || empty($email) || empty($password)) {
        $errors[] = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }

    if (empty($errors)) {
        try {
            // Check for duplicate emp_id or email
            $stmt = $conn->prepare("SELECT * FROM Admins WHERE emp_id = :emp_id OR email = :email");
            $stmt->execute(['emp_id' => $emp_id, 'email' => $email]);
            $existing_admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_admin) {
                $errors[] = "Admin with this Employee ID or Email already exists!";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Insert into Admins table
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Add Admin</h2>

        <?php if (!empty($errors)): ?>
            <div style="color: red;">
                <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="color: green;">
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>

        <form action="./add_admin.php" method="post">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="emp_id">Employee ID:</label>
            <input type="text" id="emp_id" name="emp_id" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="phone_no">Phone Number (Optional):</label>
            <input type="text" id="phone_no" name="phone_no">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Add Admin</button>
        </form>
        <a href="faculty_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
