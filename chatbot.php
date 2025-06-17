<?php
$apiKey = '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';  // Replace with your actual OpenAI API key

$data = [
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "system", "content" => "You are ARIA, a friendly bookstore assistant."],
        ["role" => "user", "content" => "Hello!"]
    ],
    "temperature" => 0.7
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if (!$response || $httpCode !== 200) {
    echo "HTTP $httpCode\n";
    echo "cURL error: $err\n";
    echo "Response:\n$response\n";
    exit;
}

echo "Response:\n$response\n";
