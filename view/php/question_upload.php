<?php
require_once '../../config/db.config.php';
require '../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn->beginTransaction();
        // Step 1: Collect form data
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $difficulty = $_POST['difficulty'] ?? 'medium';
        $tags = $_POST['tags'] ?? '';
        $constraints = $_POST['constraints']?? '';
        $testcases = $_POST['testcases']?? '';
        $expected_outcome = $_POST['expected_outcome']?? '';

        $example1_input = $_POST['example1Input'] ?? '';
        $example1_output = $_POST['example1Output'] ?? '';
        $example1_explanation = $_POST['example1Explanation'] ?? '';

        $example2_input = $_POST['example2Input'] ?? '';
        $example2_output = $_POST['example2Output'] ?? '';
        $example2_explanation = $_POST['example2Explanation'] ?? '';

        $example3_input = $_POST['example3Input'] ?? '';
        $example3_output = $_POST['example3Output'] ?? '';
        $example3_explanation = $_POST['example3Explanation'] ?? '';

        // Step 2: Insert initial question (file paths as temporary)
        $sqlInsert = "INSERT INTO questions (
            question_title, description, difficulty, constraints ,tags,
            testcase, expected_output,
            example_testcase_1, example_outcome_1, explanation_1,
            example_testcase_2, example_outcome_2, explanation_2,
            example_testcase_3, example_outcome_3, explanation_3
        ) VALUES (
            :title, :description, :difficulty, :constraints , :tags,
            :testcases, :expected_outcome,
            :ex1_in, :ex1_out, :ex1_exp,
            :ex2_in, :ex2_out, :ex2_exp,
            :ex3_in, :ex3_out, :ex3_exp
        )";

        $stmt = $conn->prepare($sqlInsert);
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':difficulty' => $difficulty,
            ':constraints' => $constraints,
            ':tags' => $tags,
            ':testcases' => $testcases,
            ':expected_outcome' => $expected_outcome,
            ':ex1_in' => $example1_input,
            ':ex1_out' => $example1_output,
            ':ex1_exp' => $example1_explanation,
            ':ex2_in' => $example2_input,
            ':ex2_out' => $example2_output,
            ':ex2_exp' => $example2_explanation,
            ':ex3_in' => $example3_input,
            ':ex3_out' => $example3_output,
            ':ex3_exp' => $example3_explanation,
        ]);

        // $question_id = $conn->lastInsertId();

        // // Step 3: Save uploaded files with new names
        // $testcasePath = '';
        // $outputPath = '';

        // if (isset($_FILES['testcaseFile']) && $_FILES['testcaseFile']['error'] == 0) {
        //     $testcasePath = '../../questions/' . $question_id . '_testcase.txt';
        //     move_uploaded_file($_FILES['testcaseFile']['tmp_name'], $testcasePath);
        // }

        // if (isset($_FILES['outputFile']) && $_FILES['outputFile']['error'] == 0) {
        //     $outputPath = '../../questions/' . $question_id . '_expected_output.txt';
        //     move_uploaded_file($_FILES['outputFile']['tmp_name'], $outputPath);
        // }

        // // Step 4: Update record with file paths
        // $sqlUpdate = "UPDATE questions SET testcase = :testcase, expected_output = :output WHERE question_id = :qid";
        // $stmtUpdate = $conn->prepare($sqlUpdate);
        // $stmtUpdate->execute([
        //     ':testcase' => $testcasePath,
        //     ':output' => $outputPath,
        //     ':qid' => $question_id
        // ]);

        $conn->commit();
        echo "✅ Question uploaded successfully";
        exit;

    } catch (PDOException $e) {
        $conn->rollBack();
        echo "❌ Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/question_upload.css">
    <title>Upload Coding Question</title>
</head>
<body>
    <!-- <header>
        <div class="nav-container">
            <a href="#" class="logo">
                <span class="logo-icon">⚡</span>
                Code<span>Arena</span>
            </a>
        </div>
    </header> -->

    <?php include "../../includes/header.php"?>

    <div class="container">
    <form id="questionForm" action="question_upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-container">
                <!-- Left Column - Basic Info -->
                <div class="form-panel">
                    <div class="form-header">
                        <h1>Upload Coding Question</h1>
                        <p>Create a new coding challenge for the community</p>
                    </div>

                    <div class="scrollable-section">
                        <div class="form-section-title">Basic Information</div>
                        
                        <div class="form-group">
                            <label class="form-label" for="title">Question Title</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="e.g., Two Sum, Palindrome Check" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Difficulty Level</label>
                            <div class="custom-difficulty-select">
                                <div class="custom-difficulty-option easy" data-value="Easy">Easy</div>
                                <div class="custom-difficulty-option medium selected" data-value="Medium">Medium</div>
                                <div class="custom-difficulty-option hard" data-value="Hard">Hard</div>
                            </div>
                            <input type="hidden" name="difficulty" id="difficultyInput" value="Medium">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="description">Problem Description</label>
                            <textarea class="form-control" id="description" name="description" placeholder="Provide a clear description of the problem..." required></textarea>
                        </div>

                        <div class="form-group tags-input">
                            <label class="form-label" for="tags">Tags</label>
                            <input type="text" class="form-control" id="tagInput"  name="tags"  placeholder="Type tag and press Enter (e.g., Arrays, Dynamic Programming)">
                            <div class="tags-container" id="tagsContainer"></div>
                        </div>

                        <div class="form-group tags-input">
                            <label class="form-label" for="tags">Constraints</label>
                            <input type="text" class="form-control" id="tagInput"  name="constraints"  placeholder="Add Constraints Here">
                            <div class="tags-container" id="tagsContainer"></div>
                        </div>
                        
                        <div class="form-group">
                        <label class="form-label" for="testcases">Testcases</label>
                        <textarea class="form-control" id="testcases" name="testcases" placeholder="Enter A Testcases Here" required></textarea>
                        <small id="testcases" style="display: none;"></small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="expected_outcome">Expected Outputs</label>
                            <textarea class="form-control" id="expected_outcome" name="expected_outcome" placeholder="Enter A Outcome Here" required></textarea>
                            <small id="expected_outcome" style="display: none;"></small>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Examples -->
                <div class="form-panel">
                    <div class="form-header">
                        <h1>Example Test Cases</h1>
                        <p>Provide examples to help users understand the problem</p>
                    </div>

                    <div class="scrollable-section">
                        <div id="examplesContainer">
                            <div class="example-container">
                                <div class="example-header">
                                    <span class="example-title">Example 1</span>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="example1Input">Input</label>
                                    <textarea class="form-control code-textarea" id="example1Input"  name="example1Input" placeholder="Input for example 1" required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="example1Output">Output</label>
                                    <textarea class="form-control code-textarea" id="example1Output" name="example1Output"  placeholder="Expected output for example 1" required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="example1Explanation">Explanation</label>
                                    <textarea class="form-control" id="example1Explanation" name="example1Explanation" placeholder="Explain how the output is derived from the input..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div id="examplesContainer">
                            <div class="example-container">
                                <div class="example-header">
                                    <span class="example-title">Example 2</span>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="example1Input">Input</label>
                                    <textarea class="form-control code-textarea" id="example1Input" name="example2Input" placeholder="Input for example 2" required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="example1Output">Output</label>
                                    <textarea class="form-control code-textarea" id="example1Output" name="example2Output" placeholder="Expected output for example 2" required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="example1Explanation">Explanation</label>
                                    <textarea class="form-control" id="example1Explanation" name="example2Explanation"  placeholder="Explain how the output is derived from the input..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div id="examplesContainer">
                            <div class="example-container">
                                <div class="example-header">
                                    <span class="example-title">Example 3</span>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="example1Input">Input</label>
                                    <textarea class="form-control code-textarea" id="example1Input" name="example3Input" placeholder="Input for example 3" required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="example1Output">Output</label>
                                    <textarea class="form-control code-textarea" id="example1Output" name="example3Output" placeholder="Expected output for example 3" required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="example1Explanation">Explanation</label>
                                    <textarea class="form-control" id="example1Explanation" name="example3Explanation" placeholder="Explain how the output is derived from the input..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- <button type="button" class="add-example-btn" id="addExampleBtn">+ Add Another Example</button> -->
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-outline" id="cancelBtn">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Submit Question</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php include "../../includes/footer.php"?>

    <script src="../js/question_upload.js"></script>
</body>
</html>