<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Read and decode JSON input
$input = file_get_contents("php://input");
$data = json_decode($input, true);
$from = $data['data']['from'] ?? '';
$body = strtolower(trim($data['data']['body'] ?? ''));

// State management per user
$stateFile = "state_" . md5($from) . ".txt";
$state = file_exists($stateFile) ? file_get_contents($stateFile) : 'start';

switch ($state) {
    case 'start':
        $reply = "Welcome to Customer Support.\nPlease reply with a number:\n1️⃣ Report a Complaint\n2️⃣ Check Complaint Status\n3️⃣ Product Inquiry\n4️⃣ Talk to Support Agent\n5️⃣ Company Information";
        file_put_contents($stateFile, 'menu');
        break;

    case 'menu':
        switch ($body) {
            case '1':
                $reply = "Please describe your complaint in detail.";
                file_put_contents($stateFile, 'awaiting_complaint');
                break;
            case '2':
                $reply = "🔍 Complaint status checking is under development. Please try again later.";
                unlink($stateFile);
                break;
            case '3':
                $reply = "📦 Please type your product-related question.";
                file_put_contents($stateFile, 'product_inquiry');
                break;
            case '4':
                $reply = "👨‍💼 A support agent will contact you shortly. Thank you!";
                unlink($stateFile);
                break;
            case '5':
                $reply = "🏢 We are a customer-focused company offering quality products and support. Visit our website for more info.";
                unlink($stateFile);
                break;
            default:
                $reply = "Invalid input. Please reply with a number:\n1️⃣ Report a Complaint\n2️⃣ Check Complaint Status\n3️⃣ Product Inquiry\n4️⃣ Talk to Support Agent\n5️⃣ Company Information";
                break;
        }
        break;

    case 'awaiting_complaint':
        $complaint = htmlspecialchars($body);
        $log = "[" . date("Y-m-d H:i:s") . "] From: $from\nComplaint: $complaint\n\n";
        file_put_contents("complaints_log.txt", $log, FILE_APPEND);
        $reply = "✅ Your complaint has been recorded. Our team will get back to you shortly.";
        unlink($stateFile);
        break;

    case 'product_inquiry':
        $inquiry = htmlspecialchars($body);
        $log = "[" . date("Y-m-d H:i:s") . "] From: $from\nProduct Inquiry: $inquiry\n\n";
        file_put_contents("inquiries_log.txt", $log, FILE_APPEND);
        $reply = "📨 Thank you! Your inquiry has been received. We'll respond soon.";
        unlink($stateFile);
        break;

    default:
        $reply = "Session expired or invalid input. Please reply with a number:\n1️⃣ Report a Complaint\n2️⃣ Check Complaint Status\n3️⃣ Product Inquiry\n4️⃣ Talk to Support Agent\n5️⃣ Company Information";
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