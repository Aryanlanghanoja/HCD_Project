<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeArena | Competitive Programming Platform</title>
    
    <!-- External Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <!-- CodeMirror CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
    
    <!-- CodeMirror JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/closebrackets.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/matchbrackets.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/clike/clike.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/python/python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
    
    <link rel="stylesheet" href="../css/code_runner.css">
</head>
<body>

    <?php include "../../includes/header.php" ?>

    <div class="main-container">
        <div class="split-layout">
            <!-- Code Editor Section -->
            <div class="editor-section">
                <div class="editor-header">
                    <div class="language-select">
                        <span>Language:</span>
                        <select id="language-selector" class="language-selector" title="Select programming language">
                            <option value="c">C</option>
                            <option value="cpp">C++</option>
                            <option value="java">Java</option>
                            <option value="py">Python</option>
                            <option value="js">JavaScript</option>
                        </select>
                    </div>
                    <div class="editor-actions">
                        <button class="reset">Reset</button>
                        <button class="run" onclick="executeCode()">Run</button>
                        <!-- <button class="submit">Submit</button> -->
                    </div>
                </div>
                <div class="editor-container">
                    <textarea id="code-editor" title="Code Editor" placeholder="Write your code here..."></textarea>
                </div>
            </div>
            
            <!-- Output Panel Section -->
            <div class="output-section">
                <div class="output-header">Input</div>
                <textarea class="output-panel" id="input" title="Input Area" placeholder="Enter your input here"></textarea>
                <div class="output-header">Output</div>
                <div class="output-panel" id="output-panel">Run your code to see output
                </div>
            </div>
        </div>
    </div>

    <script src="../js/code_runner.js"></script>
    <?php include "../../includes/footer.php"?>
</body>
</html>
