<?php
session_start();
$error = [];

@include '../../config/db.config.php';

$semesters = [];
$classes = [];
$batches = [];

try {
    $stmt = $conn->query("SELECT semester_id, semester_number FROM semesters");
    $semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $conn->query("SELECT class_id, class_name FROM classes");
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $conn->query("SELECT batch_id, batch_name FROM batches");
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error[] = 'Database error: ' . $e->getMessage();
}

if (isset($_POST['submit'])) {
    try {
        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $mobile_no = trim($_POST['mobile_no']);
        $linkedin = filter_var(trim($_POST['linkedin']), FILTER_VALIDATE_URL);
        $github = filter_var(trim($_POST['github']), FILTER_VALIDATE_URL);
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $university_id = intval($_POST['university_id']);
        $role = 'member';

        if (!preg_match("/^[a-zA-Z]+$/", $first_name)) {
            $error[] = 'First name should contain only letters.';
        }
        if (!preg_match("/^[a-zA-Z]+$/", $last_name)) {
            $error[] = 'Last name should contain only letters.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error[] = 'Invalid email format.';
        }
        if (!preg_match("/^[0-9]{10}$/", $mobile_no)) {
            $error[] = 'Mobile number must be exactly 10 digits.';
        }
        if (!$linkedin) {
            $error[] = 'Invalid LinkedIn URL.';
        }
        if (!$github) {
            $error[] = 'Invalid Github URL.';
        }
        if (strlen($password) < 6) {
            $error[] = 'Password must be at least 6 characters long.';
        }
        if ($password !== $cpassword) {
            $error[] = 'Passwords do not match!';
        }

        if (empty($error)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $check_email = "SELECT email FROM users WHERE email = :email";
            $stmt = $conn->prepare($check_email);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error[] = 'Email already exists!';
            } else {
                $insert = "INSERT INTO users (first_name, last_name, mobile_no, email, linkedin, github, password, university_id, role) 
                           VALUES (:first_name, :last_name, :mobile_no, :email, :linkedin, :github, :password, :university_id, :role)";
                $stmt = $conn->prepare($insert);
                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':last_name', $last_name);
                $stmt->bindParam(':mobile_no', $mobile_no);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':linkedin', $linkedin);
                $stmt->bindParam(':github', $github);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':university_id', $university_id);
                $stmt->bindParam(':role', $role);

                if ($stmt->execute()) {
                    header('Location: ../Login/login.php');
                    exit();
                } else {
                    $error[] = 'Registration failed! Please try again.';
                }
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
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/registration.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="shortcut icon" href="../../assets/images/Fevicon.svg" type="image/x-icon">
    <title>Registration</title>
</head>
<body>
    <div class="container">
        <header>Registration</header>
        <?php
        if (!empty($error)) {
            foreach ($error as $err) {
                echo "<p style='color: red;'>$err</p>";
            }
        }
        ?>
        <form action="" method="post">
            <div class="form">
                <div class="fields">
                    <div class="input-field">
                        <label>First Name</label>
                        <input type="text" name="first_name" placeholder="Enter Your First Name" required>
                    </div>
                    <div class="input-field">
                        <label>Middle Name</label>
                        <input type="text" name="middle_name" placeholder="Enter Your Middle Name" required>
                    </div>
                    <div class="input-field">
                        <label>Last Name</label>
                        <input type="text" name="last_name" placeholder="Enter Your Last Name" required>
                    </div>
                    <div class="input-field">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Enter Your Email" required>
                    </div>
                    <div class="input-field">
                        <label>Mobile Number</label>
                        <input type="tel" name="mobile_no" placeholder="Enter Mobile Number" required>
                    </div>
                    <div class="input-field">
                        <label>Enrollment No</label>
                        <input type="tel" name="enrollment_no" placeholder="Enter Enrollement No" required>
                    </div>
                    <div class="input-field">
                        <label>GR No</label>
                        <input type="tel" name="gr_no" placeholder="Enter GR No" required>
                    </div>
                    <div class="input-field">
                        <label>Semester</label>
                        <select name="semester_id" required>
                            <option disabled selected>Select Semester</option>
                            <?php foreach ($semesters as $semester) { ?>
                                <option value="<?php echo $semester['semester_id']; ?>"><?php echo $semester['semester_number']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="input-field">
                        <label>Class</label>
                        <select name="class_id" required>
                            <option disabled selected>Select Class</option>
                            <?php foreach ($classes as $class) { ?>
                                <option value="<?php echo $class['class_id']; ?>"><?php echo $class['class_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="input-field">
                        <label>Batch</label>
                        <select name="batch_id" required>
                            <option disabled selected>Select Batch</option>
                            <?php foreach ($batches as $batch) { ?>
                                <option value="<?php echo $batch['batch_id']; ?>"><?php echo $batch['batch_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="input-field">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter Your Password" required>
                    </div>
                    <div class="input-field">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" placeholder="Confirm Your Password" required>
                    </div>
                </div>
                <div class="buttons-container">
                    <button type="submit" name="submit" class="submit">
                        <span class="btnText">Submit</span>
                        <i class="uil uil-navigator"></i>
                    </button>
                    <div class="login-signup">
                        <span class="text">
                            Already a member?
                            <a href="../Login/login.php" class="text signup-link">Login Now</a>
                        </span>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script src="../js/registration.js"></script>
</body>
</html>
