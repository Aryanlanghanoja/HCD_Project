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
    <title>Your Submissions</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px 12px; text-align: left; vertical-align: top; }
        th { background-color: #f0f0f0; cursor: pointer; }
        pre { white-space: pre-wrap; word-break: break-word; margin: 0; }
        .filters { margin-bottom: 10px; }
        .filters select { padding: 5px 10px; }
        .export-buttons { margin-top: 10px; }
        
        /* Colored status badges */
        .status-accepted { background-color: #28a745; color: white; padding: 5px 10px; border-radius: 5px; }
        .status-failed { background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 5px; }
        .status-pending { background-color: #ffc107; color: black; padding: 5px 10px; border-radius: 5px; }
        .status-error { background-color: #6c757d; color: white; padding: 5px 10px; border-radius: 5px; }
        .status-partial { background-color: #17a2b8; color: white; padding: 5px 10px; border-radius: 5px; }
    </style>
</head>
<body>

<h2>Your Submissions</h2>

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

<div class="export-buttons">
    <button onclick="exportTableToCSV()">Export to CSV</button>
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

<script>
    const questionFilter = document.getElementById('questionFilter');
    const statusFilter = document.getElementById('statusFilter');
    const table = document.getElementById('submissionTable');
    const rows = table.querySelectorAll('tbody tr');

    function filterTable() {
        const qVal = questionFilter.value.toLowerCase();
        const sVal = statusFilter.value.toLowerCase();

        rows.forEach(row => {
            const qText = row.cells[1].textContent.toLowerCase();
            const sText = row.cells[2].textContent.toLowerCase();

            row.style.display = (
                (qVal === "" || qText === qVal) &&
                (sVal === "" || sText === sVal)
            ) ? "" : "none";
        });
    }

    questionFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);

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
