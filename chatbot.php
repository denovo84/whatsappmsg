<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Read and decode JSON
$input = file_get_contents("php://input");
$data = json_decode($input, true);
$from = $data['data']['from'] ?? '';
$body = strtolower(trim($data['data']['body'] ?? ''));

// Simple state management using a file (for demo purposes)
$stateFile = "state_" . md5($from) . ".txt";
$state = file_exists($stateFile) ? file_get_contents($stateFile) : 'start';

switch ($state) {
    case 'start':
        if (in_array($body, ['hi', 'hello'])) {
            $reply = "Hello 👋! Welcome to Customer Support.\nPlease type the number:\n 1. Report a complaint\n 2. Check complaint status\n 3. Check complaint status";
            file_put_contents($stateFile, 'menu');
        } else {
            $reply = "Please type 'hi' or 'hello' to begin.";
        }
        break;

    case 'menu':
        if ($body == '1') {
            $reply = "Please describe your complaint in one message.";
            file_put_contents($stateFile, 'awaiting_complaint');
        } elseif ($body == '2') {
            $reply = "🔍 Complaint status checking is under development. Please try again later.";
            unlink($stateFile);
        } else {
          //  $reply = "Invalid option. Please type 1 or 2.";
        }
        break;

    case 'awaiting_complaint':
        $complaint = htmlspecialchars($body);
        $log = "[" . date("Y-m-d H:i:s") . "] From: $from\nComplaint: $complaint\n\n";
        file_put_contents("complaints_log.txt", $log, FILE_APPEND);
        $reply = "✅ Thank you! Your complaint has been recorded. Our team will get back to you shortly.";
        unlink($stateFile);
        break;

    default:
        $reply = "Something went wrong. Please type 'hi' to start again.";
        unlink($stateFile);
        break;
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
    file_put_contents("send_log.txt", date("Y-m-d H:i:s") . " => " . $response . "\n", FILE_APPEND);
}

http_response_code(200);
echo "OK";
?>