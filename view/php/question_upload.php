<?php
require_once '../../config/db.config.php';
require '../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$questions = null;
$is_update = false;
$question = [];

if (isset($_GET['question_id'])) {
    $question_id = $_GET['question_id'];
    $is_update = true;

    $stmt = $conn->prepare("SELECT * FROM questions WHERE question_id = ?");
    $stmt->execute([$question_id]);
    $questions = $stmt->fetchAll();
    if (!empty($questions)) {
        $question = $questions[0];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn->beginTransaction();

        // Fetch data
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $difficulty = $_POST['difficulty'] ?? 'medium';
        $tags = $_POST['tags'] ?? '';
        $constraints = $_POST['constraints'] ?? '';
        $testcases = $_POST['testcases'] ?? '';
        $expected_outcome = $_POST['expected_outcome'] ?? '';

        $example1_input = $_POST['example1Input'] ?? '';
        $example1_output = $_POST['example1Output'] ?? '';
        $example1_explanation = $_POST['example1Explanation'] ?? '';

        $example2_input = $_POST['example2Input'] ?? '';
        $example2_output = $_POST['example2Output'] ?? '';
        $example2_explanation = $_POST['example2Explanation'] ?? '';

        $example3_input = $_POST['example3Input'] ?? '';
        $example3_output = $_POST['example3Output'] ?? '';
        $example3_explanation = $_POST['example3Explanation'] ?? '';

        if(isset($_GET['question_id']) && is_numeric($_GET['question_id']) && $is_update == true) {
            $question_id = intval($_GET['question_id']);

            $sqlUpdate = "UPDATE questions SET 
                question_title = :title,
                description = :description,
                difficulty = :difficulty,
                constraints = :constraints,
                tags = :tags,
                testcase = :testcases,
                expected_output = :expected_outcome,
                example_testcase_1 = :ex1_in,
                example_outcome_1 = :ex1_out,
                explanation_1 = :ex1_exp,
                example_testcase_2 = :ex2_in,
                example_outcome_2 = :ex2_out,
                explanation_2 = :ex2_exp,
                example_testcase_3 = :ex3_in,
                example_outcome_3 = :ex3_out,
                explanation_3 = :ex3_exp
                WHERE question_id = :qid";

            $stmt = $conn->prepare($sqlUpdate);
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
                ':qid' => $question_id
            ]);

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Question Updated successfully']);
            exit;
        }

        if ($is_update == false) {
            $sqlInsert = "INSERT INTO questions (
                question_title, description, difficulty, constraints, tags,
                testcase, expected_output,
                example_testcase_1, example_outcome_1, explanation_1,
                example_testcase_2, example_outcome_2, explanation_2,
                example_testcase_3, example_outcome_3, explanation_3
            ) VALUES (
                :title, :description, :difficulty, :constraints, :tags,
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
                ':ex3_exp' => $example3_explanation
            ]);

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Question Uploaded successfully']);
            exit;
        }

    } catch (PDOException $e) {
        $conn->rollBack();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
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
    <?php include "../../includes/header.php"?>

    <div class="container">
        <form id="questionForm" method="POST" enctype="multipart/form-data">
            <div class="form-container">
                <div class="form-panel">
                    <div class="form-header">
                        <h1>Upload Coding Question</h1>
                        <p>Create a new coding challenge for the community</p>
                    </div>

                    <div class="scrollable-section">
                        <div class="form-section-title">Basic Information</div>
                        
                        <div class="form-group">
                            <label class="form-label" for="title">Question Title</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   placeholder="e.g., Two Sum, Palindrome Check" 
                                   value="<?php echo $is_update ? htmlspecialchars($question['question_title']) : ''; ?>" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Difficulty Level</label>
                            <div class="custom-difficulty-select">
                                <div class="custom-difficulty-option easy" data-value="Easy">Easy</div>
                                <div class="custom-difficulty-option medium selected" data-value="Medium">Medium</div>
                                <div class="custom-difficulty-option hard" data-value="Hard">Hard</div>
                            </div>
                            <input type="hidden" name="difficulty" id="difficultyInput" 
                                   value="<?php echo $is_update ? htmlspecialchars($question['difficulty']) : 'Medium'; ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="description">Problem Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      placeholder="Provide a clear description of the problem..." 
                                      required><?php echo $is_update ? htmlspecialchars($question['description']) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="tags">Tags</label>
                            <input type="text" class="form-control" id="tagInput" name="tags" 
                                   placeholder="Type tags (e.g., Arrays, Dynamic Programming)" 
                                   value="<?php echo $is_update ? htmlspecialchars($question['tags']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="constraints">Constraints</label>
                            <input type="text" class="form-control" id="constraintInput" name="constraints" 
                                   placeholder="Add Constraints Here" 
                                   value="<?php echo $is_update ? htmlspecialchars($question['constraints']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="testcases">Testcases</label>
                            <textarea class="form-control" id="testcases" name="testcases" 
                                      placeholder="Enter Testcases Here" 
                                      required><?php echo $is_update ? htmlspecialchars($question['testcase']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="expected_outcome">Expected Outputs</label>
                            <textarea class="form-control" id="expected_outcome" name="expected_outcome" 
                                      placeholder="Enter Expected Outputs Here"><?php echo $is_update ? htmlspecialchars($question['expected_output']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="form-panel">
                    <div class="form-header">
                        <h1>Example Test Cases</h1>
                        <p>Provide examples to help users understand the problem</p>
                    </div>

                    <div class="scrollable-section">
                        <!-- Example 1 -->
                        <div class="example-container">
                            <div class="example-header">
                                <span class="example-title">Example 1</span>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="example1Input">Input</label>
                                <textarea class="form-control code-textarea" id="example1Input" name="example1Input" 
                                          placeholder="Input for example 1" 
                                          required><?php echo $is_update ? htmlspecialchars($question['example_testcase_1']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="example1Output">Output</label>
                                <textarea class="form-control code-textarea" id="example1Output" name="example1Output" 
                                          placeholder="Expected output for example 1" 
                                          required><?php echo $is_update ? htmlspecialchars($question['example_outcome_1']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="example1Explanation">Explanation</label>
                                <textarea class="form-control" id="example1Explanation" name="example1Explanation" 
                                          placeholder="Explain how the output is derived from the input..."><?php echo $is_update ? htmlspecialchars($question['explanation_1']) : ''; ?></textarea>
                            </div>
                        </div>

                        <!-- Example 2 -->
                        <div class="example-container">
                            <div class="example-header">
                                <span class="example-title">Example 2</span>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="example2Input">Input</label>
                                <textarea class="form-control code-textarea" id="example2Input" name="example2Input" 
                                          placeholder="Input for example 2" 
                                          required><?php echo $is_update ? htmlspecialchars($question['example_testcase_2']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="example2Output">Output</label>
                                <textarea class="form-control code-textarea" id="example2Output" name="example2Output" 
                                          placeholder="Expected output for example 2" 
                                          required><?php echo $is_update ? htmlspecialchars($question['example_outcome_2']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="example2Explanation">Explanation</label>
                                <textarea class="form-control" id="example2Explanation" name="example2Explanation" 
                                          placeholder="Explain how the output is derived from the input..."><?php echo $is_update ? htmlspecialchars($question['explanation_2']) : ''; ?></textarea>
                            </div>
                        </div>

                        <!-- Example 3 -->
                        <div class="example-container">
                            <div class="example-header">
                                <span class="example-title">Example 3</span>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="example3Input">Input</label>
                                <textarea class="form-control code-textarea" id="example3Input" name="example3Input" 
                                          placeholder="Input for example 3" 
                                          required><?php echo $is_update ? htmlspecialchars($question['example_testcase_3']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="example3Output">Output</label>
                                <textarea class="form-control code-textarea" id="example3Output" name="example3Output" 
                                          placeholder="Expected output for example 3" 
                                          required><?php echo $is_update ? htmlspecialchars($question['example_outcome_3']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="example3Explanation">Explanation</label>
                                <textarea class="form-control" id="example3Explanation" name="example3Explanation" 
                                          placeholder="Explain how the output is derived from the input..."><?php echo $is_update ? htmlspecialchars($question['explanation_3']) : ''; ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-outline" id="cancelBtn">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <?php echo $is_update ? "Update Question" : "Create Question"; ?>
                            </button>
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