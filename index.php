<?php
// -----------------------------
// ⚡ Simple Secure Proxy API
// -----------------------------

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Allow from all origins

// 1️⃣ Get query parameter
if (!isset($_GET['q']) || empty($_GET['q'])) {
    echo json_encode(["error" => "No query provided"]);
    exit;
}

$query = urlencode($_GET['q']);

// 2️⃣ Your real backend server (hidden)
$real_api = "http://84.247.146.20:5781/search?q={$query}";

// 3️⃣ Forward request using cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $real_api);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

// Optional: Forward user-agent
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "User-Agent: Vercel-Proxy/1.0",
    "Accept: application/json"
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode(["error" => "Request failed", "details" => curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// 4️⃣ Pass through response from real API
http_response_code($http_code);
echo $response;
?>
