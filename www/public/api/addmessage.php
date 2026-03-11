<?php
session_start();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST allowed']);
    exit;
}

include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();

$result = ['success' => false, 'message' => 'Failed to send message'];

$from = isset($_POST['from_id']) ? intval($_POST['from_id']) : 0;
$to = isset($_POST['to_id']) ? intval($_POST['to_id']) : 0;
$text = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($from && $to && $text !== '') {
    if ($db->addMessage($from, $to, $text)) {
        $result['success'] = true;
        $result['message'] = 'Message sent';
    } else {
        $result['message'] = 'Database error';
    }
} else {
    $result['message'] = 'Invalid parameters';
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>