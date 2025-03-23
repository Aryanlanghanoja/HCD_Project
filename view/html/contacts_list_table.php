<?php
session_start();
require '../../config/db.config.php';

// Check if the user is an admin
if (!isset($_SESSION['user']) && $_SESSION['role']=='admin') {
    header("Location: ../login.php");
    exit();
}

// Pagination Setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search Query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchQuery = $search ? "WHERE name LIKE :search OR enrollment_no LIKE :search" : "";

// Fetch Students
$sql = "SELECT * FROM Students $searchQuery ORDER BY name LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);

if ($search) {
    $searchTerm = "%$search%";
    $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
}
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total Students Count
$countSql = "SELECT COUNT(*) FROM Students $searchQuery";
$countStmt = $conn->prepare($countSql);
if ($search) {
    $countStmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
}
$countStmt->execute();
$totalStudents = $countStmt->fetchColumn();
$totalPages = ceil($totalStudents / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Student List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/student_list.css">
</head>
<body>

<div class="container">
    <h5 class="my-3">Student List <span class="text-muted">(<?= $totalStudents ?>)</span></h5>

    <div class="d-flex justify-content-between mb-3">
        <form class="d-flex" method="GET">
            <input class="form-control me-2" type="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by Name or Enrollment No">
            <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>
        <a href="add_student.php" class="btn btn-primary">+ Add Student</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Enrollment No</th>
                    <th>Batch</th>
                    <th>Semester</th>
                    <th>Email</th>
                    <th>Submissions</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['name']) ?></td>
                    <td><?= htmlspecialchars($student['enrollment_no']) ?></td>
                    <td><?= htmlspecialchars($student['batch_id']) ?></td>
                    <td><?= htmlspecialchars($student['semester_id']) ?></td>
                    <td><?= htmlspecialchars($student['email']) ?></td>
                    <td><?= $student['submissions'] ?? 0 ?></td>
                    <td>
                        <a href="edit_student.php?id=<?= $student['enrollment_no'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_student.php?id=<?= $student['enrollment_no'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= $search ?>">Previous</a></li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= $search ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= $search ?>">Next</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
