<?php
/**
 * Advanced POST & Method Debugger
 */
header('Content-Type: text/html');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // If it's a POST, we just return the JSON for the AJAX to see
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'SUCCESS - POST RECEIVED',
        'method' => $_SERVER['REQUEST_METHOD'],
        'post_data' => $_POST,
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
        'headers' => getallheaders()
    ], JSON_PRETTY_PRINT);
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>POST Debugger</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-10">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-xl shadow-lg">
        <h1 class="text-2xl font-bold mb-4">POST Method Tester</h1>
        <p class="text-gray-600 mb-6">Click the button below to see if your server allows POST requests to hit this
            script without redirecting them to GET.</p>

        <div class="space-y-4">
            <button onclick="testPost()"
                class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700">
                🚀 Send Test POST Request
            </button>

            <div id="resultBox" class="hidden mt-6">
                <h3 class="font-bold text-sm uppercase text-gray-500 mb-2">Result:</h3>
                <pre id="result" class="bg-black text-green-400 p-4 rounded-lg overflow-auto text-xs"></pre>
            </div>
        </div>
    </div>

    <script>
        async function testPost() {
            const resBox = document.getElementById('resultBox');
            const resText = document.getElementById('result');
            resBox.classList.remove('hidden');
            resText.textContent = "Sending...";

            try {
                const formData = new FormData();
                formData.append('test_key', 'test_value');
                formData.append('time', new Date().toISOString());

                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                const text = await response.text();
                resText.textContent = text;
            } catch (err) {
                resText.textContent = "JS ERROR: " + err.message;
            }
        }
    </script>
</body>

</html>