<?php

@include '../../config/db.config.php';
require '../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

session_start();
$error = [];

// Fetch subjects, classes, semesters, batches, locations, and invigilators from the database
$subjects = [];
$classes = [];
$semesters = [];
$batches = [];
$locations = [];
$invigilators = [];

try {
    $stmt = $conn->query("SELECT semester_id, semester_number FROM semesters");
    $semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT class_id, class_name FROM classes");
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT batch_id, batch_name FROM batches");
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT subject_id, subject_name FROM subjects");
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT location_id, location_name FROM locations");
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT admin_id, name FROM admins");
    $invigilators = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error[] = 'Database error: ' . $e->getMessage();
}

if (isset($_POST['submit'])) {
    $subject = $_POST['subject'];
    $class = $_POST['class'];
    $semester = $_POST['semester'];
    $batch = $_POST['batch'];
    $location = $_POST['location'];
    $invigilator = $_POST['invigilator'];
    $examDate = $_POST['examDate'];
    $startTimeRaw = $_POST['startTime'];  // e.g., "10:30"
    $duration = $_POST['duration'];

    // Combine date and time to a full timestamp (YYYY-MM-DD HH:MM:SS)
    $startDateTime = date('Y-m-d H:i:s', strtotime("$examDate $startTimeRaw"));

    // Validate the form data
    if (empty($subject) || empty($class) || empty($semester) || empty($batch) || empty($location) || empty($invigilator) || empty($examDate) || empty($startTimeRaw) || empty($duration)) {
        $error[] = 'All fields are required.';
    }

    // Check if the exam date is in the past
    $currentDate = date('Y-m-d');
    if ($examDate < $currentDate) {
        $error[] = 'Exam date cannot be in the past.';
    }

    try {
        $conn->beginTransaction();

        // Check for time conflicts
        $stmt = $conn->prepare("SELECT * FROM exams WHERE subject_id = :subject AND class_id = :class AND semester_id = :semester AND batch_id = :batch AND location_id = :location AND DATE(start_time) = :examDate");
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':class', $class);
        $stmt->bindParam(':semester', $semester);
        $stmt->bindParam(':batch', $batch);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':examDate', $examDate);
        $stmt->execute();
        $existingExams = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($existingExams as $existingExam) {
            $existingStartTime = $existingExam['start_time'];
            $existingEndTime = date('Y-m-d H:i:s', strtotime($existingStartTime) + ($existingExam['duration'] * 60));

            $newExamStart = strtotime($startDateTime);
            $newExamEnd = $newExamStart + ($duration * 60);

            $existingStart = strtotime($existingStartTime);
            $existingEnd = strtotime($existingEndTime);

            if (($newExamStart < $existingEnd) && ($newExamEnd > $existingStart)) {
                $error[] = 'The selected time conflicts with an existing exam.';
                break;
            }
        }

        if (empty($error)) {
            $insert = "INSERT INTO exams (subject_id, class_id, semester_id, batch_id, location_id, invigilator_id, exam_date, start_time, duration) 
                       VALUES (:subject, :class, :semester, :batch, :location, :invigilator, :examDate, :startTime, :duration)";
            $stmt = $conn->prepare($insert);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':class', $class);
            $stmt->bindParam(':semester', $semester);
            $stmt->bindParam(':batch', $batch);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':invigilator', $invigilator);
            $stmt->bindParam(':examDate', $examDate);
            $stmt->bindParam(':startTime', $startDateTime);  // full timestamp
            $stmt->bindParam(':duration', $duration);
            $stmt->execute();

            $conn->commit();
            echo "<div class='alert alert-success'>✅ Exam added successfully</div>";
            exit;
        } else {
            $conn->rollBack();
            foreach ($error as $err) {
                echo '<div class="alert alert-danger">' . $err . '</div>';
            }
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        echo '<div class="alert alert-danger">Database error: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Registration Form</title>
    <link rel="stylesheet" href="../css/exam_registration.css">
</head>
<body>
    <div class="container">
    <?php include 'header.php'; ?>
        <div class="form-header">
            <h1>Exam Registration</h1>
        </div>

        <div class="form-content">
            <form id="examRegistrationForm" method="POST" action="" enctype="multipart/form-data">
                <div class="grid-layout">
                    <!-- Exam Details -->
                    <div class="card">
                        <h3 class="card-title">Exam Details</h3>



                        <!-- <div class="two-columns"> -->
                            <div class="form-group">
                                <label for="class">Class</label>
                                <!-- <input type="text" id="class" name="class" placeholder="Enter class" required> -->
                                <select id="class" name="class" required>
                                    <option value="" disabled selected>Class</option>
                                    <?php foreach ($classes as $class) { ?>
                                        <option value="<?php echo $class['class_id']; ?>"><?php echo $class['class_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="semester">Semester</label>
                                <select id="semester" name="semester" required>
                                    <option value="" disabled selected>Select Semester</option>
                                    <?php foreach ($semesters as $semester) { ?>
                                        <option value="<?php echo $semester['semester_id']; ?>"><?php echo $semester['semester_number']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <!-- </div> -->

                        <div class="form-group">
                            <label for="batch">Batch</label>
                            <select id="batch" name="batch" required>
                                <option value="" disabled selected>Select Batch</option>
                                <?php foreach ($batches as $batch) { ?>
                                    <option value="<?php echo $batch['batch_id']; ?>"><?php echo $batch['batch_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Venue & Supervision -->
                    <div class="card">
                        <h3 class="card-title">Venue & Supervision</h3>

                        <div class="form-group">
                            <label for="location">Location</label>
                            <select id="location" name="location" required>
                                <option value="" disabled selected>Location</option>
                                <?php foreach ($locations as $location) { ?>
                                    <option value="<?php echo $location['location_id']; ?>"><?php echo $location['location_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="invigilator">Invigilator</label>
                            <select id="invigilator" name="invigilator" required>
                                <option value="" disabled selected>Select Invigilator</option>
                                <?php foreach ($invigilators as $invigilator) { ?>
                                    <option value="<?php echo $invigilator['admin_id']; ?>"><?php echo $invigilator['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <!-- <input type="text" id="subject" name="subject" placeholder="Enter subject name" required> -->
                            <select id="subject" name="subject" required>
                                <option value="" disabled selected>Subjects</option>
                                <?php foreach ($subjects as $subject) { ?>
                                <option value="<?php echo $subject['subject_id']; ?>"><?php echo $subject['subject_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="card">
                        <h3 class="card-title">Schedule</h3>
                        <div class="form-group">
                            <label for="examDate">Exam Date</label>
                            <input type="date" id="examDate" name="examDate" required>
                        </div>
                        
                        <!-- <div class="two-columns"> -->
                            <div class="form-group">
                                <label for="startTime">Start Time</label>
                                <input type="time" id="startTime" name="startTime" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="duration">Duration (minutes)</label>
                                <input type="number" id="duration" name="duration" placeholder="Duration" min="30" max="240" step="15" required>
                            </div>
                        <!-- </div> -->
                    </div>
                </div>

                <button type="submit"  name="submit">Register Exam</button>
            </form>
        </div>

        <?php include 'footer.php'; ?>


    </div>
</body>
</html>
