<?php
session_start();
require_once '../../config/db.config.php';

$enrollment_no = $_SESSION['user'] ?? null;
$question_id = $_GET['question_id'] ?? null;

if (!$enrollment_no || !$question_id) {
    die("User not logged in or question ID not specified.");
}

// Fetch submission data with question title
$stmt = $conn->prepare("
    SELECT s.submission_id, s.question_id, q.question_title
    FROM submissions s
    JOIN questions q ON s.question_id = q.question_id
    WHERE s.enrollment_no = ? AND s.question_id = ?
");
$stmt->execute([$enrollment_no, $question_id]);
$submissions = $stmt->fetchAll();

$judge0_url = "http://10.80.18.41:2358/submissions/";
$results = [];

foreach ($submissions as $sub) {
    $token = $sub['submission_id'];
    $question_title = $sub['question_title'];

    // Request submission status from Judge0
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
        .export-buttons { margin-top: 10px; }

        .status-accepted { background-color: #28a745; color: white; padding: 5px 10px; border-radius: 5px; }
        .status-failed { background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 5px; }
        .status-pending { background-color: #ffc107; color: black; padding: 5px 10px; border-radius: 5px; }
        .status-error { background-color: #6c757d; color: white; padding: 5px 10px; border-radius: 5px; }
        .status-partial { background-color: #17a2b8; color: white; padding: 5px 10px; border-radius: 5px; }
    </style>
</head>
<body>
<h2>Your Submissions for: <?= htmlspecialchars($results[0]['question_title'] ?? 'Unknown') ?></h2>

<div class="export-buttons">
    <button onclick="exportTableToCSV()">Export to CSV</button>
</div>

<table id="submissionTable">
    <thead>
        <tr>
            <th>Sr. No.</th>
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
                <td>
                    <?php
                    $status = htmlspecialchars($res['status']);
                    $statusClass = match ($status) {
                        'Accepted'     => 'status-accepted',
                        'Wrong Answer' => 'status-failed',
                        'Pending'      => 'status-pending',
                        'Error'        => 'status-error',
                        'Partial'      => 'status-partial',
                        default        => ''
                    };
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
    function exportTableToCSV() {
        const table = document.getElementById('submissionTable');
        let csv = '';
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => `"${th.innerText}"`).join(',');
        csv += headers + '\n';

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
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
