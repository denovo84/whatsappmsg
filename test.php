<?php
header("Content-Type: application/json");

// Path to your log file (in the same folder as this PHP file)
$logFile = __DIR__ . "/log.txt";

// Text to write
$logText = "success" . PHP_EOL;

// Append "success" to log.txt every time API is called
file_put_contents($logFile, $logText, FILE_APPEND);
  file_put_contents("log_1.txt", date("Y-m-d H:i:s")." => ".file_get_contents("php://input")."\n\n", FILE_APPEND);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode([
        "status" => "success",
        "message" => "Hello POST method" . ($data['name'] ?? 'Guest')
    ]);
}
    elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode([
        "status" => "success",
        "message" => "Hello GET method" . ($data['name'] ?? 'Guest')
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Only POST method allowed"
    ]);
}
    
  //  echo json_encode([
  //  "status" => "success",
  //  "message" => "API called and logged successfully"
//]);
?>
