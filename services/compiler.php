<?php
// header("Access-Control-Allow-Origin: *"); // This allows all origins
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

//     $language = strtolower($_POST['language']);
//     $code = $_POST['code'];

//     $random = substr(md5(mt_rand()), 0, 7);
//     $filePath = "./submissions/" . $random . "." . $language;
//     $programFile = fopen($filePath, "w");
//     fwrite($programFile, $code);
//     fclose($programFile);

//     $output = "" ;

//     if ($language == "java") {
//         $className = pathinfo($filePath, PATHINFO_FILENAME);
//         $compileOutput = shell_exec("javac $filePath 2>&1");
//         if (!$compileOutput) {
//             $output = shell_exec("java -cp temp $className 2>&1");
//         } else {
//             $output = $compileOutput;
//         }
//     }

//     if ($language == "py") {
//         $output = shell_exec("C:/Users/Aaryan/AppData/Local/Programs/Python/Python310/python.exe $filePath 2>&1");
//     }

//     if ($language == "js") {
//         rename($filePath, $filePath . ".js");
//         $output = shell_exec("node $filePath.js 2>&1");
//     }

//     if ($language == "c") {
//         $outputExe = "./submissions/" . $random . ".exe";
//         $compileOutput = shell_exec("gcc $filePath -o $outputExe 2>&1");
//         if (!$compileOutput) {
//             $output = shell_exec(_DIR_ . "/$outputExe 2>&1");
//         } else {
//             $output = $compileOutput;
//         }
//     }

//     if ($language == "cpp") {
//         $outputExe = "./submissions/" . $random . ".exe";
//         $compileOutput = shell_exec("g++ $filePath -o $outputExe 2>&1");
//         if (!$compileOutput) {
//             $output = shell_exec(_DIR_ . "/$outputExe 2>&1");
//         } else {
//             $output = $compileOutput;
//         }
//     }

//     echo "$output";





// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// $language = strtolower($_POST['language']);
// $code = $_POST['code'];
// $stdin = $_POST['input'] ?? "";

// // Judge0 API endpoint (use HTTP instead of HTTPS)
// $apiUrl = "http://localhost:2358/submissions?base64_encoded=true&wait=false";

// // Mapping language to Judge0 language IDs
// $languageIds = [
//     "c" => 50,
//     "cpp" => 54,
//     "java" => 62,
//     "py" => 71,
//     "js" => 63
// ];

// $languageId = $languageIds[$language] ?? 71;

// // Prepare the request data
// $data = [
//     "source_code" => base64_encode($code),
//     "language_id" => $languageId,
//     "stdin" => base64_encode($stdin),
//     "cpu_time_limit" => 10.0
// ];

// // Initialize cURL to send the submission
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $apiUrl);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_POST, true);
// curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
// curl_setopt($ch, CURLOPT_HTTPHEADER, [
//     "Content-Type: application/json"
// ]);

// // Execute the submission request
// $response = curl_exec($ch);
// curl_close($ch);

// if ($response === false) {
//     echo "Error: Unable to connect to Judge0 API.";
//     exit;
// }

// $result = json_decode($response, true);
// $token = $result['token'] ?? null;

// if (!$token) {
//     echo "Error: Failed to retrieve submission token.";
//     exit;
// }

// // Polling for the result
// $statusUrl = "http://localhost:2358/submissions/$token?base64_encoded=true";
// while (true) {
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $statusUrl);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     $result = curl_exec($ch);
//     curl_close($ch);

//     if ($result === false) {
//         echo "Error: Failed to retrieve submission result.";
//         exit;
//     }

//     $result = json_decode($result, true);
//     $statusId = $result['status']['id'];

//     // Check if the status is "Completed"
//     if (in_array($statusId, [3, 6])) {
//         $output = base64_decode($result['stdout'] ?? $result['stderr'] ?? "No output.");
//         echo $output;
//         break;
//     }

//     // Sleep for a short time to avoid rapid polling
//     usleep(100000); // 100 ms
// }








// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// $language = strtolower($_POST['language']);
// $code = $_POST['code'];
// $stdin = $_POST['input'] ?? "";

// // Judge0 API endpoint (use HTTP instead of HTTPS)
// $apiUrl = "http://localhost:2358/submissions?base64_encoded=true&wait=true";

// // Mapping language to Judge0 language IDs
// $languageIds = [
//     "c" => 50,
//     "cpp" => 54,
//     "java" => 62,
//     "py" => 71,
//     "js" => 63
// ];

// $languageId = $languageIds[$language] ?? 71;

// // Prepare the request data
// $data = [
//     "source_code" => base64_encode($code),
//     "language_id" => $languageId,
//     "stdin" => base64_encode($stdin),
//     "cpu_time_limit" => 10.0
// ];

// // Initialize cURL to send the submission
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $apiUrl);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_POST, true);
// curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
// curl_setopt($ch, CURLOPT_HTTPHEADER, [
//     "Content-Type: application/json"
// ]);

// // Execute the submission request
// $response = curl_exec($ch);
// curl_close($ch);

// if ($response === false) {
//     echo "Error: Unable to connect to Judge0 API.";
//     exit;
// }

// $result = json_decode($response, true);
// $token = $result['token'] ?? null;

// if (!$token) {
//     echo "Error: Failed to retrieve submission token.";
//     exit;
// }

// // Polling for the result
// $statusUrl = "http://localhost:2358/submissions/$token?base64_encoded=true";
// while (true) {
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $statusUrl);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     $result = curl_exec($ch);
//     curl_close($ch);

//     if ($result === false) {
//         echo "Error: Failed to retrieve submission result.";
//         exit;
//     }

//     $result = json_decode($result, true);
//     $statusId = $result['status']['id'];
//     $statusDescription = $result['status']['description'];

//     // Check if the status is "Completed" or an error occurred
//     if (in_array($statusId, [3, 6])) {
//         $output = base64_decode($result['stdout'] ?? $result['stderr'] ?? "No output.");
//         echo $output;
//         break;
//     }

//     // Handle compilation or runtime errors
//     if (in_array($statusId, [4, 5, 7, 8, 9, 10, 11, 12])) {
//         $errorMessage = base64_decode($result['stderr'] ?? $result['compile_output'] ?? "Unknown error.");
//         echo "Error: $statusDescription\n$errorMessage";
//         break;
//     }

//     // Sleep for a short time to avoid rapid polling
//     usleep(100000); // 100 ms
// }






header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

$language = strtolower($_POST['language']);
$code = $_POST['code'];
$stdin = $_POST['input'] ?? "";

// Judge0 API endpoint (use HTTP instead of HTTPS)
$apiUrl = "http://10.80.21.246:2358/submissions?base64_encoded=true&wait=true";

// Mapping language to Judge0 language IDs
$languageIds = [
    "c" => 50,
    "cpp" => 54,
    "java" => 62,
    "py" => 71,
    "js" => 63
];

$languageId = $languageIds[$language] ?? 71;

// Prepare the request data
// Prepare the request data
$data = [
    "source_code" => base64_encode($code),
    "language_id" => $languageId,
    "stdin" => base64_encode($stdin),
    "cpu_time_limit" => 1.0,
    "max_output_size" => 10240,  // Limit stdout to 10 KB
    "memory_limit" => 128000,    // 128 MB memory limit
    "stack_limit" => 64000       // 64 MB stack size limit
];


// Initialize cURL to send the submission
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);

// Execute the submission request
$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
    echo "Error: Unable to connect to Judge0 API.";
    exit;
}

$result = json_decode($response, true);
$token = $result['token'] ?? null;

if (!$token) {
    echo "Error: Failed to retrieve submission token.";
    exit;
}

// Polling for the result
$statusUrl = "http://10.80.21.246:2358/submissions/$token?base64_encoded=true";
while (true) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $statusUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    if ($result === false) {
        echo "Error: Failed to retrieve submission result.";
        exit;
    }

    $result = json_decode($result, true);
    $statusId = $result['status']['id'];
    $statusDescription = $result['status']['description'];

    // Handle successful execution
    if ($statusId == 3) {
        $output = base64_decode($result['stdout'] ?? "No output.");
        echo "$output";
        break;
    }

    // Handle compilation or runtime errors
    if (in_array($statusId, [6, 7, 8, 9, 10, 11, 12])) {
        $errorMessage = base64_decode($result['stderr'] ?? $result['compile_output'] ?? "Unknown error.");
        echo "Error ($statusDescription): $errorMessage";
        break;
    }

    // Handle timeouts
    if ($statusId == 5) {
        echo "Error (Time Limit Exceeded): Execution took too long.";
        break;
    }

    // Handle internal errors
    if ($statusId == 13) {
        echo "Internal Error: An unexpected error occurred.";
        break;
    }

    // Sleep for a short time to avoid rapid polling
    usleep(100000); // 100 ms
}


?>