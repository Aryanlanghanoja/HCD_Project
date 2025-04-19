<?php
session_start();
require_once '../../config/db.config.php';
require '../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();
if (!isset($_SESSION["user"]) && $_SESSION["role"] != "student") {
    // User not logged in
    header("Location: ./login.php");
    exit();
}
try {
    $stmt = $conn->query('SELECT * FROM questions ORDER BY question_id  ASC');
    $questions = $stmt->fetchAll();
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
    <title>Coding Questions List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/question_list.css">
</head>
<body>

    <?php include "../../includes/header.php" ?>

    <div class="page-wrapper">
        <div class="container">
                <div class="panel">
                    <div class="panel-header">
                        <h1 class="panel-title">Coding Questions</h1>
                        <div class="panel-actions">
                            <!-- <a href="#" class="btn btn-outline">My Submissions</a> -->
                            <a href="./question_upload.php" class="btn btn-primary">Upload Question</a>
                        </div>
                    </div>

                    <div class="search-filter-bar">
                        <div class="search-input">
                            <input type="text" placeholder="Search questions..." id="searchBox" onchange="filterQuestions()">
                            <button onclick="filterQuestions()">🔍</button>
                        </div>
                        <div class="filter-controls">
                            <select class="filter-select">
                                <option value="">All Difficulties</option>
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                            <select class="filter-select">
                                <option value="">All Tags</option>
                                <option value="arrays">Arrays</option>
                                <option value="strings">Strings</option>
                                <option value="linked-lists">Linked Lists</option>
                                <option value="dynamic-programming">Dynamic Programming</option>
                                <option value="trees">Trees</option>
                            </select>
                            <select class="filter-select">
                                <option value="newest">Newest</option>
                                <option value="oldest">Oldest</option>
                                <option value="most-solved">Most Solved</option>
                                <option value="least-solved">Least Solved</option>
                            </select>
                        </div>
                    </div>

                    <table class="questions-table">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 35%">Title</th>
                                <th style="width: 15%">Difficulty</th>
                                <th style="width: 25%">Tags</th>
                                <th style="width: 25%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($questions as $index => $q): ?>
                                <tr key=<?= htmlspecialchars($q['question_id']) ?>>
                                    <td><?= htmlspecialchars($q['question_id']) ?></td>
                                    <td><a href="./compiler.php?question_id=<?= htmlspecialchars($q['question_id']) ?>" class="question-title"><?= htmlspecialchars($q['question_title']) ?></a></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($q['difficulty']) ?>">
                                            <?= htmlspecialchars($q['difficulty']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php foreach (explode(',', $q['tags']) as $tag): ?>
                                            <span class="tag"><?= htmlspecialchars(trim($tag)) ?></span>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <!-- <button class="view" title="View Profile"><i class="fas fa-eye"></i></button> -->
                                            <!-- <button class="edit" title="Edit Details"><i class="fas fa-edit"></i></button> -->
                                            <a href="./question_upload.php?question_id=<?= htmlspecialchars($q['question_id']) ?>"><button class="edit" title="Edit Profile"><i class="fas fa-edit"></i></button></a>
                                            <button class="delete" title="Delete Record"><i class="fas fa-trash-alt"></i></button>
                                        </div>
                                    </td>
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