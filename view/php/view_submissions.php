<?php
session_start();
require_once '../../config/db.config.php';

$enrollment_no = $_SESSION['user'] ?? null;
if (!$enrollment_no) {
    die("User not logged in.");
}

// Fetch submission data with question title
$stmt = $conn->prepare("
    SELECT s.submission_id, s.question_id, q.question_title
    FROM submissions s
    JOIN questions q ON s.question_id = q.question_id
    WHERE s.enrollment_no = ?
");
$stmt->execute([$enrollment_no]);
$submissions = $stmt->fetchAll();

$judge0_url = "http://10.80.18.41:2358/submissions/";
$results = [];

foreach ($submissions as $sub) {
    $token = $sub['submission_id'];
    $question_title = $sub['question_title'];

    // Request submission status from Judge0 with fields parameter
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $judge0_url . $token . "?fields=created_at,time,memory,stdout,status");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the response from Judge0
    $submissionData = json_decode($response, true);

    // Fetch the necessary details: status, time, memory, and created_at
    $status = $submissionData['status']['description'] ?? 'N/A';
    $time = $submissionData['time'] ?? 'N/A';
    $memory = $submissionData['memory'] ?? 'N/A';
    $stdout = $submissionData['stdout'] ?? '';
    $createdAt = $submissionData['created_at'] ?? null;
    
    // Format the created_at timestamp
    $timestamp = $createdAt ? date("Y-m-d H:i:s", strtotime($createdAt)) : 'N/A';

    // Store the results
    $results[] = [
        'question_title' => $question_title,
        'status'         => $status,
        'time'           => $time,
        'memory'         => $memory,
        'stdout'         => $stdout,
        'created_at'     => $timestamp
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/view_submission.css">
    <title>Your Submissions</title>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand"><img src="../../assets/images/Logo_Black_Text-removebg_resized.png" alt="" srcset=""></a>
        <ul class="side-menu">
            <li><a href="./student_dashboard.php"><i class='bx bxs-dashboard icon'></i> Dashboard</a></li>
            <li class="divider" data-text="main">Main</li>
            
            <li><a href="./code_runner.php"><i class='bx bx-code-alt icon'></i> Compiler</a></li>
            <li><a href="./question_list.php"><i class='bx bx-question-mark icon'></i> Questions</a></li>
            <li><a href="#"><i class='bx bx-table icon'></i> Exam</a></li>
            <li><a href="./view_submissions.php" class="active"><i class='bx bx-code-curly icon'></i> All Submissions</a></li>
        </ul>
    </section>
    <!-- SIDEBAR -->

    <!-- NAVBAR -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu toggle-sidebar'></i>
            <form action="#">
                <div class="form-group">
                    <input type="text" placeholder="Search...">
                    <i class='bx bx-search icon'></i>
                </div>
            </form>
            <a href="#" class="nav-link">
                <i class='bx bxs-bell icon'></i>
                <span class="badge">5</span>
            </a>
            <a href="#" class="nav-link">
                <i class='bx bxs-message-square-dots icon'></i>
                <span class="badge">8</span>
            </a>
            <span class="divider"></span>
            <div class="profile">
                <img src="../../assets/images/Admin.jpg" alt="">
                <ul class="profile-link">
                    <li><a href="#"><i class='bx bxs-user-circle icon'></i> Profile</a></li>
                    <li><a href="#"><i class='bx bxs-cog'></i> Settings</a></li>
                    <li><a href="./logout.php"><i class='bx bxs-log-out-circle'></i> Logout</a></li>
                </ul>
            </div>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <h1 class="title">Your Submissions</h1>
            <ul class="breadcrumbs">
                <li><a href="./student_dashboard.php">Home</a></li>
                <li class="divider">/</li>
                <li><a href="#" class="active">Submissions</a></li>
            </ul>
            
            <div class="submissions-container">
                <div class="submissions-header">
                    <h2>Submission History</h2>
                </div>
                
                <div class="filters">
                    <div class="filter-group">
                        <label for="questionFilter">Filter by Question:</label>
                        <select id="questionFilter">
                            <option value="">All</option>
                            <?php
                            $questionTitles = array_unique(array_column($results, 'question_title'));
                            sort($questionTitles);
                            foreach ($questionTitles as $qt) {
                                echo "<option value=\"$qt\">$qt</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="statusFilter">Filter by Status:</label>
                        <select id="statusFilter">
                            <option value="">All</option>
                            <?php
                            $statuses = array_unique(array_column($results, 'status'));
                            sort($statuses);
                            foreach ($statuses as $status) {
                                echo "<option value=\"$status\">$status</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="export-buttons">
                    <button class="export-btn" onclick="exportTableToCSV()">Export to CSV</button>
                </div>
                
                <table id="submissionTable" class="submissions-table">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Question Title</th>
                            <th>Status</th>
                            <th>Time (s)</th>
                            <th>Memory (KB)</th>
                            <th>Output</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $index => $res): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($res['question_title']) ?></td>
                                <td>
                                    <?php
                                    $status = htmlspecialchars($res['status']);
                                    $statusClass = '';
                                    switch ($status) {
                                        case 'Accepted':
                                            $statusClass = 'status-accepted';
                                            break;
                                        case 'Wrong Answer':
                                            $statusClass = 'status-failed';
                                            break;
                                        case 'Pending':
                                            $statusClass = 'status-pending';
                                            break;
                                        case 'Error':
                                            $statusClass = 'status-error';
                                            break;
                                        case 'Partial':
                                            $statusClass = 'status-partial';
                                            break;
                                        default:
                                            $statusClass = '';
                                            break;
                                    }
                                    ?>
                                    <span class="<?= $statusClass ?>"><?= $status ?></span>
                                </td>
                                <td><?= htmlspecialchars($res['time']) ?></td>
                                <td><?= htmlspecialchars($res['memory']) ?></td>
                                <td><pre><?= htmlspecialchars($res['stdout']) ?></pre></td>
                                <td><?= htmlspecialchars($res['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // SIDEBAR TOGGLE
        const toggleSidebar = document.querySelector('.toggle-sidebar');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');

        toggleSidebar.addEventListener('click', function() {
            sidebar.classList.toggle('hide');
        });

        // PROFILE DROPDOWN
        const profile = document.querySelector('.profile');
        const profileLink = document.querySelector('.profile-link');

        profile.addEventListener('click', function() {
            profileLink.style.display = profileLink.style.display === 'block' ? 'none' : 'block';
        });

        // Click outside to close profile dropdown
        document.addEventListener('click', function(event) {
            if (!profile.contains(event.target)) {
                profileLink.style.display = 'none';
            }
        });

        // TABLE FILTERING
        const questionFilter = document.getElementById('questionFilter');
        const statusFilter = document.getElementById('statusFilter');
        const table = document.getElementById('submissionTable');
        const rows = table.querySelectorAll('tbody tr');

        function filterTable() {
            const qVal = questionFilter.value.toLowerCase().trim();
            const sVal = statusFilter.value.toLowerCase().trim();

            rows.forEach(row => {
                const qText = row.cells[1].innerText.toLowerCase().trim();
                const sText = row.cells[2].innerText.toLowerCase().trim();

                const showRow =
                    (qVal === "" || qText === qVal) &&
                    (sVal === "" || sText === sVal);

                row.style.display = showRow ? "" : "none";
            });
        }

        questionFilter.addEventListener('change', filterTable);
        statusFilter.addEventListener('change', filterTable);

        // CSV EXPORT
        function exportTableToCSV() {
            let csv = '';
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => `"${th.innerText}"`).join(',');
            csv += headers + '\n';

            rows.forEach(row => {
                if (row.style.display === "none") return;
                const cols = Array.from(row.cells).map(td => `"${td.innerText.replace(/\n/g, " ").trim()}"`);
                csv += cols.join(',') + '\n';
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.setAttribute('href', URL.createObjectURL(blob));
            link.setAttribute('download', 'submissions.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>