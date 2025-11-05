<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Log incoming data
file_put_contents("incoming_log.txt", date("Y-m-d H:i:s") . " => " . file_get_contents("php://input") . "\n\n", FILE_APPEND);

// Read and decode JSON
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Check if 'data' exists
if (!isset($data['data'])) {
    file_put_contents("incoming_log.txt", "Invalid payload: " . $input . "\n", FILE_APPEND);
    http_response_code(200);
    exit;
}

$from = $data['data']['from'] ?? '';
$body = strtolower(trim($data['data']['body'] ?? ''));

// Prepare reply
if ($body == "hi" || $body == "hello") {
    $reply = "Hello 👋! How can I help you today?";
} elseif ($body == "time") {
    $reply = "⏰ Current time: " . date("H:i:s");
} elseif ($body == "date") {
    $reply = "📅 Today is: " . date("d M Y");
} else {
    $reply = "I didn’t understand that. Try 'hi', 'time', or 'date'.";
}

// Send reply
sendMessage($from, $reply);

// Function to send message
function sendMessage($to, $message) {
    $token = "uuhtafn320uvv57j";        // Replace with your token
    $instanceId = "instance147651";     // Replace with your instance ID

    $url = "https://api.ultramsg.com/$instanceId/messages/chat?token=$token";
    $data = ["to" => $to, "body" => $message];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
    $response = curl_exec($ch);
    curl_close($ch);

    // Log response
    file_put_contents("send_log.txt", date("Y-m-d H:i:s") . " => " . $response . "\n", FILE_APPEND);
}

// Respond OK
http_response_code(200);
echo "OK";
?>