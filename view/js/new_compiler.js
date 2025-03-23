const starterCode = {
    javascript: `/**
* @param {number[]} nums
* @return {boolean}
*/
var containsDuplicate = function(nums) {
    // Write your solution here
};`,
    python: `class Solution:
def containsDuplicate(self, nums: List[int]) -> bool:
    # Write your solution here
    pass`,
    java: `class Solution {
public boolean containsDuplicate(int[] nums) {
    // Write your solution here
}
}`,
    cpp: `class Solution {
public:
    bool containsDuplicate(vector<int>& nums) {
        // Write your solution here
    }
};`
};

// CodeMirror mode mapping
const codeMirrorModes = {
    javascript: "javascript",
    python: "python",
    java: "text/x-java",
    cpp: "text/x-c++src"
};

// Initialize CodeMirror with Consolas font and font size 14px
const editor = CodeMirror.fromTextArea(document.getElementById("code-editor"), {
    lineNumbers: true,
    theme: "monokai",
    mode: "javascript",
    indentUnit: 4,
    indentWithTabs: false,
    smartIndent: true,
    lineWrapping: true,
    extraKeys: {
        "Tab": (cm) => {
            if (cm.somethingSelected()) {
                cm.indentSelection("add");
            } else {
                cm.replaceSelection("    ", "end");
            }
        }
    }
});

// Apply Consolas font and font size 14px
editor.getWrapperElement().style.fontFamily = 'Consolas, monospace';
editor.getWrapperElement().style.fontSize = '14px';

// Set initial code
editor.setValue(starterCode.javascript);

// Language selector
const languageSelector = document.getElementById('language-selector');
languageSelector.addEventListener('change', () => {
    const language = languageSelector.value;
    editor.setOption("mode", codeMirrorModes[language]);
    editor.setValue(starterCode[language]);
});

// Tab switching
const tabs = document.querySelectorAll('.test-case-tab');
tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        const isCustom = tab.textContent === 'Custom';
        const testCaseContent = document.querySelector('.test-case-content');

        if (isCustom) {
            testCaseContent.innerHTML = `
                <textarea class="custom-input" placeholder="Enter your test case here...">[1,2,3,1]</textarea>`;
        } else {
            const testCases = {
                'Case 1': '[1,2,3,1]',
                'Case 2': '[1,2,3,4]',
                'Case 3': '[1,1,1,3,3,4,3,2,4,2]'
            };
            testCaseContent.innerHTML = `
                <textarea class="custom-input" readonly>${testCases[tab.textContent]}</textarea>`;
        }
    });
});

// Run button
const runButton = document.querySelector('.run');
runButton.addEventListener('click', () => {
    const outputPanel = document.querySelector('.output-panel');
    outputPanel.textContent = '> Running...';

    // Simulate execution delay
    setTimeout(() => {
        const activeTab = document.querySelector('.test-case-tab.active');
        const input = document.querySelector('.custom-input').value;

        // Mock execution output
        if (activeTab.textContent === 'Case 1' || input.includes('1,2,3,1')) {
            outputPanel.textContent = '> Output: true\n> Execution time: 52 ms\n> Memory usage: 46.2 MB';
        } else if (activeTab.textContent === 'Case 2' || input.includes('1,2,3,4')) {
            outputPanel.textContent = '> Output: false\n> Execution time: 48 ms\n> Memory usage: 45.8 MB';
        } else {
            outputPanel.textContent = '> Output: true\n> Execution time: 64 ms\n> Memory usage: 47.5 MB';
        }
    }, 800);
});

// Submit button
const submitButton = document.querySelector('.submit');
submitButton.addEventListener('click', () => {
    const outputPanel = document.querySelector('.output-panel');
    outputPanel.textContent = '> Submitting solution...';

    // Simulate submission delay
    setTimeout(() => {
        outputPanel.innerHTML = `
            > <span style="color: #4cc9f0;">Submission successful!</span>
            > All test cases passed.
            > Runtime: 56 ms (faster than 85.32% of submissions)
            > Memory: 46.8 MB (less than 67.21% of submissions)`;
        
        // Show a notification
        alert('Solution submitted successfully! All test cases passed.');
    }, 1500);
});

// Reset button
const resetButton = document.querySelector('.reset');
resetButton.addEventListener('click', () => {
    const language = languageSelector.value;
    editor.setValue(starterCode[language]);
    
    const outputPanel = document.querySelector('.output-panel');
    outputPanel.textContent = '> Code reset. Run your code to see output';
});
