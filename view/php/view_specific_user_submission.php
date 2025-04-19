<?php
session_start();
require_once '../../config/db.config.php';

// Get user info from session
$loggedInEnrollment = $_SESSION['user'] ?? null;
$userRole = $_SESSION['role'] ?? 'student'; // e.g., set during login
$isAdmin = $userRole === 'admin';

// Decide which enrollment number to fetch
$selectedEnrollment = $loggedInEnrollment;

// Admin can override by typing an enrollment number
if ($isAdmin && isset($_GET['enrollment_no']) && !empty(trim($_GET['enrollment_no']))) {
    $selectedEnrollment = trim($_GET['enrollment_no']);
}

if (!$selectedEnrollment) {
    die("Enrollment number not provided.");
}

// Fetch submission data
$stmt = $conn->prepare("
    SELECT s.submission_id, s.question_id, q.question_title
    FROM submissions s
    JOIN questions q ON s.question_id = q.question_id
    WHERE s.enrollment_no = ?
");
$stmt->execute([$selectedEnrollment]);
$submissions = $stmt->fetchAll();

$judge0_url = "http://10.80.18.41:2358/submissions/";
$results = [];

foreach ($submissions as $sub) {
    $token = $sub['submission_id'];
    $question_title = $sub['question_title'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $judge0_url . $token . "?fields=created_at,time,memory,stdout,status");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    $submissionData = json_decode($response, true);
    $status = $submissionData['status']['description'] ?? 'N/A';
    $time = $submissionData['time'] ?? 'N/A';
    $memory = $submissionData['memory'] ?? 'N/A';
    $stdout = $submissionData['stdout'] ?? '';
    $createdAt = $submissionData['created_at'] ?? null;
    $timestamp = $createdAt ? date("Y-m-d H:i:s", strtotime($createdAt)) : 'N/A';

    $results[] = [
        'question_title' => $question_title,
        'status' => $status,
        'time' => $time,
        'memory' => $memory,
        'stdout' => $stdout,
        'created_at' => $timestamp
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submissions</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px 12px; text-align: left; vertical-align: top; }
        th { background-color: #f0f0f0; cursor: pointer; }
        pre { white-space: pre-wrap; word-break: break-word; margin: 0; }
        .filters { margin-bottom: 10px; }
        .filters select, .filters input { padding: 5px 10px; }
        .status-accepted { background-color: #28a745; color: white; padding: 5px 10px; border-radius: 5px; }
        .status-failed { background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 5px; }
        .status-pending { background-color: #ffc107; color: black; padding: 5px 10px; border-radius: 5px; }
        .status-error { background-color: #6c757d; color: white; padding: 5px 10px; border-radius: 5px; }
        .status-partial { background-color: #17a2b8; color: white; padding: 5px 10px; border-radius: 5px; }
    </style>
</head>
<body>

<h2><?= $isAdmin ? "Viewing Submissions for $selectedEnrollment" : "Your Submissions" ?></h2>

<?php if ($isAdmin): ?>
    <form method="get" style="margin-bottom: 20px;">
        <label>Enter Enrollment No:</label>
        <input type="text" name="enrollment_no" placeholder="Enrollment No" value="<?= htmlspecialchars($selectedEnrollment) ?>" required>
        <button type="submit">Fetch Submissions</button>
    </form>
<?php endif; ?>

<?php if (empty($results)): ?>
    <p><strong>No submissions found.</strong></p>
<?php else: ?>

<div class="filters">
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

<table id="submissionTable">
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
                    $class = match ($status) {
                        'Accepted' => 'status-accepted',
                        'Wrong Answer' => 'status-failed',
                        'Pending' => 'status-pending',
                        'Error' => 'status-error',
                        'Partial' => 'status-partial',
                        default => ''
                    };
                    ?>
                    <span class="<?= $class ?>"><?= $status ?></span>
                </td>
                <td><?= htmlspecialchars($res['time']) ?></td>
                <td><?= htmlspecialchars($res['memory']) ?></td>
                <td><pre><?= htmlspecialchars($res['stdout']) ?></pre></td>
                <td><?= htmlspecialchars($res['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php endif; ?>

<script>
    const questionFilter = document.getElementById('questionFilter');
    const statusFilter = document.getElementById('statusFilter');
    const table = document.getElementById('submissionTable');
    const rows = table.querySelectorAll('tbody tr');

    function filterTable() {
        const qVal = questionFilter.value.toLowerCase().trim();
        const sVal = statusFilter.value.toLowerCase().trim();

        rows.forEach(row => {
            const qText = row.cells[1].textContent.toLowerCase().trim();
            const sSpan = row.cells[2].querySelector('span');
            const sText = sSpan ? sSpan.textContent.toLowerCase().trim() : '';

            const questionMatch = !qVal || qText === qVal;
            const statusMatch = !sVal || sText === sVal;

            row.style.display = (questionMatch && statusMatch) ? "" : "none";
        });
    }

    questionFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
</script>

</body>
</html>
