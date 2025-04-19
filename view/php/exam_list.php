<?php
session_start();
require_once '../../config/db.config.php';
require '../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();
if (!isset($_SESSION["user"])) {
    // User not logged in
    header("Location: ./login.php");
    exit();
}

$subjects = [];
$classes = [];
$semesters = [];
$batches = [];
$locations = [];
$invigilators = [];
$exams = [];

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

    $stmt = $conn->query('SELECT 
        exams.exam_id,
        subjects.subject_name,
        locations.location_name,
        batches.batch_name,
        classes.class_name,
        semesters.semester_number,
        admins.name AS invigilator_name,
        exams.exam_date,
        exams.start_time,
        exams.duration
        FROM exams
        JOIN subjects ON exams.subject_id = subjects.subject_id
        JOIN locations ON exams.location_id = locations.location_id
        JOIN batches ON exams.batch_id = batches.batch_id
        JOIN classes ON exams.class_id = classes.class_id
        JOIN semesters ON exams.semester_id = semesters.semester_id
        JOIN admins ON exams.invigilator_id = admins.admin_id
        ORDER BY exams.exam_id ASC'
    );
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/question_list.css">
</head>
<body>

    <?php include "../../includes/header.php" ?>

    <div class="page-wrapper">
        <div class="container">
                <div class="panel">
                    <div class="panel-header">
                        <h1 class="panel-title">Exams</h1>
                        <div class="panel-actions">
                            <!-- <a href="#" class="btn btn-outline">My Submissions</a> -->
                            <a href="./exam_registration.php" class="btn btn-primary">Create Exam</a>
                        </div>
                    </div>

                    <div class="search-filter-bar">
                        <div class="search-input">
                            <input type="text" placeholder="Search exam..." id="searchBox">
                            <button onclick="filterQuestions()">🔍</button>
                        </div>
                        <div class="filter-controls">
                            <select class="filter-select">
                                <option value="" disabled selected>Subject</option>
                                <?php foreach ($subjects as $subject) { ?>
                                    <option value="<?php echo $subject['subject_id']; ?>"><?php echo $subject['subject_name']; ?></option>
                                <?php } ?>
                            </select>
                            <select class="filter-select">
                            <option value="" disabled selected>Location</option>
                                <?php foreach ($locations as $location) { ?>
                                    <option value="<?php echo $location['location_id']; ?>"><?php echo $location['location_name']; ?></option>
                                <?php } ?>
                            </select>
                            <select class="filter-select">
                            <option value="" disabled selected>Batch</option>
                                <?php foreach ($batches as $batch) { ?>
                                    <option value="<?php echo $batch['batch_id']; ?>"><?php echo $batch['batch_name']; ?></option>
                                <?php } ?>
                            </select>
                            <select class="filter-select">
                            <option value="" disabled selected>Class</option>
                                <?php foreach ($classes as $class) { ?>
                                    <option value="<?php echo $class['class_id']; ?>"><?php echo $class['class_name']; ?></option>
                                <?php } ?>
                            </select>
                            <select class="filter-select">
                            <option value="" disabled selected>Semester</option>
                                <?php foreach ($semesters as $semester) { ?>
                                    <option value="<?php echo $semester['semester_id']; ?>"><?php echo $semester['semester_number']; ?></option>
                                <?php } ?>
                            </select>
                            <select class="filter-select">
                            <option value="" disabled selected>Invigilator</option>
                                <?php foreach ($invigilators as $invigilator) { ?>
                                    <option value="<?php echo $invigilator['admin_id']; ?>"><?php echo $invigilator['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <table class="questions-table">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 30%">Subject</th>
                                <th style="width: 5%">Location</th>
                                <th style="width: 5%">Batch</th>
                                <th style="width: 5%">Class</th>
                                <th style="width: 5%">Semester</th>
                                <th style="width: 25%">Invigilator</th>
                                <th style="width: 45%">Date</th>
                                <th style="width: 25%">Time</th>
                                <th style="width: 25%">Duration</th>
                                <!-- <th style="width: 25%">Actions</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exams as $index => $q): ?>

                                <?php

                                    

                                ?>
                                <tr key=<?= htmlspecialchars($q['exam_id']) ?>>
                                    <td><?= htmlspecialchars($q['exam_id']) ?></td>
                                    <td class="question-title"><?= htmlspecialchars($q['subject_name']) ?></td>
                                    <td><?= htmlspecialchars($q['location_name']) ?></td>
                                    <td><?= htmlspecialchars($q['batch_name']) ?></td>
                                    <td><?= htmlspecialchars($q['class_name']) ?></td>
                                    <td><?= htmlspecialchars($q['semester_number']) ?></td>
                                    <td><?= htmlspecialchars($q['invigilator_name']) ?></td>
                                    <td><?= htmlspecialchars($q['exam_date']) ?></td>
                                    <td><?= htmlspecialchars($q['start_time'])?></td>
                                    <td><?= htmlspecialchars($q['duration']) ?></td>
                                    <!-- <td>
                                        <div class="actions">
                                            <a href="./exam_registration.php?exam_id=<?= htmlspecialchars($q['exam_id']) ?>"><button class="edit" title="Edit Profile"><i class="fas fa-edit"></i></button></a>
                                            <button class="delete" title="Delete Record"><i class="fas fa-trash-alt"></i></button>
                                        </div>
                                    </td> -->
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>

                    <div class="table-footer">
                        <div class="items-per-page">
                            <span>Show:</span>
                            <select>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span>items per page</span>
                        </div>
                        <div class="pagination">
                            <span class="page-item">«</span>
                            <span class="page-item active">1</span>
                            <span class="page-item">2</span>
                            <span class="page-item">3</span>
                            <span class="page-item">4</span>
                            <span class="page-item">5</span>
                            <span class="page-item">»</span>
                        </div>
                    </div>
                </div>
        </div>        
    </div>
    <script src="../js/question_list.js"></script>

    <?php include "../../includes/footer.php" ?>
</body>
</html>