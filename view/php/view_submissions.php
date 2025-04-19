<?php
session_start();
require_once '../../config/db.config.php'; // This sets up $conn (PDO)

$enrollment_no = $_SESSION['user'] ?? null;

if (!$enrollment_no) {
    die("User not logged in.");
}

// Fetch submission tokens for the user
$stmt = $conn->prepare("SELECT submission_id, question_id FROM submissions WHERE enrollment_no = ?");
$stmt->execute([$enrollment_no]);
$submissions = $stmt->fetchAll();

// Judge0 API endpoint
$judge0_url = "http://10.80.18.41:2358/submissions/";
$results = [];

foreach ($submissions as $sub) {
    $token = $sub['submission_id'];
    $question_id = $sub['question_id'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $judge0_url . $token);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    $submissionData = json_decode($response, true);

    $results[] = [
        'question_id'     => $question_id,
        'token'           => $token,
        'status'          => $submissionData['status']['description'] ?? 'N/A',
        'time'            => $submissionData['time'] ?? 'N/A',
        'memory'          => $submissionData['memory'] ?? 'N/A',
        'stdout'          => $submissionData['stdout'] ?? '',
        'stderr'          => $submissionData['stderr'] ?? '',
        'compile_output'  => $submissionData['compile_output'] ?? ''
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
        th { background-color: #f0f0f0; }
        pre { white-space: pre-wrap; word-break: break-word; margin: 0; }
    </style>
</head>
<body>

<h2>Your Submissions</h2>

<table>
    <thead>
        <tr>
            <th>Question ID</th>
            <th>Token</th>
            <th>Status</th>
            <th>Time (s)</th>
            <th>Memory (KB)</th>
            <th>Output</th>
            <th>Error</th>
            <th>Compile Output</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $res): ?>
            <tr>
                <td><?= htmlspecialchars($res['question_id']) ?></td>
                <td><?= htmlspecialchars($res['token']) ?></td>
                <td><?= htmlspecialchars($res['status']) ?></td>
                <td><?= htmlspecialchars($res['time']) ?></td>
                <td><?= htmlspecialchars($res['memory']) ?></td>
                <td><pre><?= htmlspecialchars($res['stdout']) ?></pre></td>
                <td><pre><?= htmlspecialchars($res['stderr']) ?></pre></td>
                <td><pre><?= htmlspecialchars($res['compile_output']) ?></pre></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
