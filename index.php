<?php
    file_put_contents("incoming_log.txt", date("Y-m-d H:i:s")." => ".file_get_contents("php://input")."\n\n", FILE_APPEND);

    ini_set('display_errors', 1);
error_reporting(E_ALL);
$token = "uuhtafn320uvv57j";
$instanceId = "instance147651";

$to = "916292285570"; // recipient's phone number with country code
$message = "Hello! This is my first WhatsApp bot using UltraMsg!";

$url = "https://api.ultramsg.com/$instanceId/messages/chat?token=$token";
$data = [
    "to" => $to,
    "body" => $message
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
