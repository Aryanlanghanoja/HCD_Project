<?php


session_start();
$error = [];

@include '../../config/db.config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();


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
        // $middle_name = htmlspecialchars(trim($_POST['middle_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $mobile_no = trim($_POST['mobile_no']);
        $enrollment_no = trim($_POST['enrollment_no']);
        $gr_no = trim($_POST['gr_no']);
        $semester_id = intval($_POST['semester_id']);
        $class_id = intval($_POST['class_id']);
        $batch_id = intval($_POST['batch_id']);
        $password = $_POST['password'];
        $cpassword = $_POST['confirm_password'];
        $role = 'member';
        $image = $_FILES['profile_pic']['tmp_name'];
        $imgData = file_get_contents($image);
        $profile_photo = base64_encode($imgData);

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
        if (strlen($enrollment_no) < 5) {
            $error[] = 'Enrollment number is too short.';
        }
        if (strlen($gr_no) < 5) {
            $error[] = 'GR number is too short.';
        }
        if (strlen($password) < 6) {
            $error[] = 'Password must be at least 6 characters long.';
        }
        if ($password !== $cpassword) {
            $error[] = 'Passwords do not match!';
        }

        if (empty($error)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $check_email = "SELECT email FROM students WHERE email = :email";
            $stmt = $conn->prepare($check_email);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error[] = 'Email already exists!';
            } else {
                $insert = "INSERT INTO students (name, email, phone_no, enrollment_no, gr_number, semester_id, class_id, batch_id, password , profile_photo) 
                           VALUES (:name, :email, :phone_no, :enrollment_no, :gr_no, :semester_id, :class_id, :batch_id, :password , :profile_photo)";
                $stmt = $conn->prepare($insert);
                $full_name = $first_name . " " . $last_name;
                $stmt->bindParam(':name', $full_name);                
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':phone_no', $mobile_no);
                $stmt->bindParam(':enrollment_no', $enrollment_no);
                $stmt->bindParam(':gr_no', $gr_no);
                $stmt->bindParam(':semester_id', $semester_id);
                $stmt->bindParam(':class_id', $class_id);
                $stmt->bindParam(':batch_id', $batch_id);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':profile_photo', $profile_photo);

                if ($stmt->execute()) {
                    // Generate a verification token
                    $verification_token = bin2hex(random_bytes(32));
                
                    // Store the token in the database
                    $updateToken = "UPDATE students SET verification_token = :token WHERE email = :email";
                    $stmt = $conn->prepare($updateToken);
                    $stmt->bindParam(':token', $verification_token);
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();
                
                    
                
                    $mail = new PHPMailer(true);

                        try {
                            // Server settings
                            $mail->isSMTP();
                            $mail->Host = $_ENV['MAIL_HOST']; // Change based on your email provider
                            $mail->SMTPAuth = true;
                            $mail->Username = $_ENV['MAIL_USERNAME']; // Change based on your email provider 
                            $mail->Password = $_ENV['MAIL_PASSWORD']; // Change based on your email provider 
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port = $_ENV['MAIL_PORT']; // Change based on your email provider

                            // Recipients
                            $mail->setFrom($_ENV['MAIL_FROM_EMAIL'], $_ENV['MAIL_FROM_NAME']);
                            $mail->addAddress($email, $full_name);

                            // Email content
                            $mail->isHTML(true);
                            $mail->Subject = "Verify Your Email";
                            $mail->Body = "<p>Click the link below to verify your email:</p>
                                        <p><a href='http://10.80.2.206/hcd_project/verify_email.php?token=$verification_token'>Verify Email</a></p>";

                            $mail->send();
                            echo "A verification email has been sent to your email address. Please verify your email before logging in.";
                        } catch (Exception $e) {
                            echo "Email could not be sent. Error: {$mail->ErrorInfo}";
                        }

                
                    echo "A verification email has been sent to your email address. Please verify your email before logging in.";
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

        <form action="" method="post" enctype="multipart/form-data">
            <div class="form">
                <div class="fields">
                    <div class="input-field">
                        <label>First Name</label>
                        <input type="text" name="first_name" placeholder="Enter Your First Name" required>
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
                        <label>Profile Photo</label>
                        <input type="file" name="profile_pic" id="profile_pic" placeholder="Upload Your Profile Photo" required>
                    </div>
                    <div class="input-field">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter Your Password" required>
                    </div>
                    <div class="input-field">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" placeholder="Confirm Your Password" required>
                    </div>
                    <small style="color: red; display: block; margin-top: 1px;">Size should be less than 200 KB and <br>format must be JPG, JPEG, or PNG.</small>
                </div>
                <div class="buttons-container">
                    <button type="submit" name="submit" class="submit">
                        <span class="btnText">Submit</span>
                        <i class="uil uil-navigator"></i>
                    </button>
                    <div class="login-signup">
                        <span class="text">
                            Already a member?
                            <a href="./login.php" class="text signup-link">Login Now</a>
                        </span>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script src="../js/registration.js"></script>
</body>
</html>
