let editor;

window.onload = function() {
    editor = CodeMirror(document.getElementById("editor"), {
        mode: "text/x-csrc", // Default to C
        theme: "default",
        lineNumbers: true,
        autoCloseBrackets: true,
        matchBrackets: true
    });

    // ✅ Attach event listener after CodeMirror initializes
    document.querySelector('.CodeMirror').addEventListener("wheel", function (e) {
        e.preventDefault();
    }, { passive: false });
};


function changeLanguage() {
    let language = $("#languages").val();
    let mode = "text/x-csrc";

    if (language == 'cpp') mode = "text/x-c++src";
    else if (language == 'java') mode = "text/x-java";
    else if (language == 'php') mode = "application/x-httpd-php";
    else if (language == 'py') mode = "text/x-python";
    else if (language == 'javascript') mode = "text/javascript";

    editor.setOption("mode", mode);
}

function executeCode() {
    $.ajax({
        url: "http://10.80.2.166/PHP_Projects/HCD_Project/services/compiler.php",
        method: "POST",
        data: {
            language: $("#languages").val(),
            code: editor.getValue()
        },
        success: function(response) {
            $("#output").html(response.replace(/\n/g, "<br>"));
        }
    });
}
