document.addEventListener('DOMContentLoaded', function() {
    // Difficulty selection
    const difficultyOptions = document.querySelectorAll('.difficulty-option');
    const difficultyInput = document.getElementById('difficulty');
    
    difficultyOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            difficultyOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class to clicked option
            this.classList.add('selected');
            
            // Update hidden input value
            difficultyInput.value = this.dataset.difficulty;
        });
    });
    
    // Set medium as default selected
    // const mediumOption = document.querySelector('[data-difficulty="medium"]');
    // mediumOption.classList.add('selected');
    
    // Tags input
    const tagInput = document.getElementById('tagInput');
    const tagsContainer = document.getElementById('tagsContainer');
    let tags = [];
    
    tagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const tag = tagInput.value.trim();
            
            if (tag && !tags.includes(tag)) {
                tags.push(tag);
                renderTags();
            }
            
            tagInput.value = '';
        }
    });
    
    function renderTags() {
        tagsContainer.innerHTML = '';
        
        tags.forEach((tag, index) => {
            const tagElement = document.createElement('div');
            tagElement.className = 'tag';
            tagElement.innerHTML = `
                ${tag}
                <button type="button" data-index="${index}">×</button>
            `;
            tagsContainer.appendChild(tagElement);
        });
        
        // Add event listeners to remove buttons
        document.querySelectorAll('.tag button').forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                tags.splice(index, 1);
                renderTags();
            });
        });
    }
    
    // Add example button
    // const addExampleBtn = document.getElementById('addExampleBtn');
    // const examplesContainer = document.getElementById('examplesContainer');
    // let exampleCount = 1;
    
    // addExampleBtn.addEventListener('click', function() {
    //     exampleCount++;
        
    //     const newExample = document.createElement('div');
    //     newExample.className = 'example-container';
    //     newExample.innerHTML = `
    //         <div class="example-header">
    //             <span class="example-title">Example ${exampleCount}</span>
    //         </div>
            
    //         <div class="form-group">
    //             <label class="form-label" for="example${exampleCount}Input">Input</label>
    //             <textarea class="form-control code-textarea" id="example${exampleCount}Input" placeholder="Input for example ${exampleCount}" required></textarea>
    //         </div>
            
    //         <div class="form-group">
    //             <label class="form-label" for="example${exampleCount}Output">Output</label>
    //             <textarea class="form-control code-textarea" id="example${exampleCount}Output" placeholder="Expected output for example ${exampleCount}" required></textarea>
    //         </div>
            
    //         <div class="form-group">
    //             <label class="form-label" for="example${exampleCount}Explanation">Explanation</label>
    //             <textarea class="form-control" id="example${exampleCount}Explanation" placeholder="Explain how the output is derived from the input..."></textarea>
    //         </div>
    //     `;
        
    //     examplesContainer.appendChild(newExample);
        
    //     // Add event listener to remove button
    //     // newExample.querySelector('.remove-example').addEventListener('click', function() {
    //     //     examplesContainer.removeChild(newExample);
    //     // });
    // });
    
    // File uploads
    const testcaseUpload = document.getElementById('testcaseUpload');
    const testcaseFile = document.getElementById('testcaseFile');
    const testcaseFileName = document.getElementById('testcaseFileName');
    
    // testcaseUpload.addEventListener('click', function() {
    //     testcaseFile.click();
    // });
    
    // testcaseFile.addEventListener('change', function() {
    //     if (this.files.length > 0) {
    //         testcaseFileName.textContent = `Selected file: ${this.files[0].name}`;
    //         testcaseFileName.style.display = 'block';
    //     }
    // });
    
    // const outputUpload = document.getElementById('outputUpload');
    // const outputFile = document.getElementById('outputFile');
    // const outputFileName = document.getElementById('outputFileName');
    
    // outputUpload.addEventListener('click', function() {
    //     outputFile.click();
    // });
    
    // outputFile.addEventListener('change', function() {
    //     if (this.files.length > 0) {
    //         outputFileName.textContent = `Selected file: ${this.files[0].name}`;
    //         outputFileName.style.display = 'block';
    //     }
    // });
    
    // Drag and drop for file uploads
    // ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    //     testcaseUpload.addEventListener(eventName, preventDefaults, false);
    //     outputUpload.addEventListener(eventName, preventDefaults, false);
    // });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    // ['dragenter', 'dragover'].forEach(eventName => {
    //     testcaseUpload.addEventListener(eventName, highlight, false);
    //     outputUpload.addEventListener(eventName, highlight, false);
    // });
    
    // ['dragleave', 'drop'].forEach(eventName => {
    //     testcaseUpload.addEventListener(eventName, unhighlight, false);
    //     outputUpload.addEventListener(eventName, unhighlight, false);
    // });
    
    function highlight(e) {
        this.style.borderColor = '#4361ee';
        this.style.backgroundColor = 'rgba(67, 97, 238, 0.1)';
    }
    
    function unhighlight(e) {
        this.style.borderColor = '';
        this.style.backgroundColor = '';
    }
    
    // testcaseUpload.addEventListener('drop', function(e) {
    //     const dt = e.dataTransfer;
    //     const files = dt.files;
        
    //     if (files.length > 0) {
    //         testcaseFile.files = files;
    //         testcaseFileName.textContent = `Selected file: ${files[0].name}`;
    //         testcaseFileName.style.display = 'block';
    //     }
    // });
    
    // outputUpload.addEventListener('drop', function(e) {
    //     const dt = e.dataTransfer;
    //     const files = dt.files;
        
    //     if (files.length > 0) {
    //         outputFile.files = files;
    //         outputFileName.textContent = `Selected file: ${files[0].name}`;
    //         outputFileName.style.display = 'block';
    //     }
    // });
    
    // Form submission
    const questionForm = document.getElementById('questionForm');
    const cancelBtn = document.getElementById('cancelBtn');
    
    // questionForm.addEventListener('submit', function(e) {
    //     e.preventDefault();
        
    //     // Collect form data
    //     const formData = new FormData();
    //     formData.append('title', document.getElementById('title').value);
    //     formData.append('difficulty', difficultyInput.value);
    //     formData.append('description', document.getElementById('description').value);
    //     formData.append('tags', JSON.stringify(tags));
        
    //     // Collect examples
    //     const examples = [];
    //     const exampleElements = document.querySelectorAll('.example-container');
        
    //     exampleElements.forEach((element, index) => {
    //         const exampleNumber = index + 1;
    //         const inputId = `example${exampleNumber}Input`;
    //         const outputId = `example${exampleNumber}Output`;
    //         const explanationId = `example${exampleNumber}Explanation`;
            
    //         examples.push({
    //             input: document.getElementById(inputId).value,
    //             output: document.getElementById(outputId).value,
    //             explanation: document.getElementById(explanationId).value
    //         });
    //     });
        
    //     formData.append('examples', JSON.stringify(examples));
        
    //     // Add files
    //     if (testcaseFile.files.length > 0) {
    //         formData.append('testcaseFile', testcaseFile.files[0]);
    //     }
        
    //     if (outputFile.files.length > 0) {
    //         formData.append('outputFile', outputFile.files[0]);
    //     }
        
    //     // Here you would normally send the formData to your server
    //     // For demonstration, we'll just show an alert
    //     alert('Question submitted successfully!');
        
    //     // Log form data for debugging
    //     console.log('Form submitted with the following data:');
    //     for (let pair of formData.entries()) {
    //         console.log(pair[0] + ': ' + pair[1]);
    //     }
        
    //     // Reset form
    //     questionForm.reset();
    //     tags = [];
    //     renderTags();
        
    //     // Reset example containers
    //     while (examplesContainer.children.length > 1) {
    //         examplesContainer.removeChild(examplesContainer.lastChild);
    //     }
    //     exampleCount = 1;
        
    //     // Reset file names
    //     testcaseFileName.style.display = 'none';
    //     outputFileName.style.display = 'none';
        
    //     // Reset difficulty selection
    //     difficultyOptions.forEach(opt => opt.classList.remove('selected'));
    //     mediumOption.classList.add('selected');
    //     difficultyInput.value = 'medium';
    // });
    
    cancelBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to cancel? All your progress will be lost.')) {
            window.location.href = '#'; // Replace with your desired redirect
        }
    });
});

// For the custom button-style difficulty selector
document.addEventListener('DOMContentLoaded', function() {
    const difficultyOptions = document.querySelectorAll('.custom-difficulty-option');
    const difficultyInput = document.getElementById('difficultyInput');
    
    difficultyOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            difficultyOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class to clicked option
            this.classList.add('selected');
            
            // Update hidden input value
            difficultyInput.value = this.getAttribute('data-value');
        });
    });
});