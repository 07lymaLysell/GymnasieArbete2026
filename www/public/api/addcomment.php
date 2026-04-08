<?php
// No whitespace before this line
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST allowed']);
    exit;
}

try {
    require_once __DIR__ . '/../../model/DbEgyTalk.php';

    if (!class_exists('DbEgyTalk')) {
        throw new Exception('DbEgyTalk class not found');
    }

    $db = new DbEgyTalk();

    $thread_id = (int) ($_POST['thread_id'] ?? 0);
    $uid = (int) ($_POST['uid'] ?? 0);
    $content = trim($_POST['content'] ?? '');

    if ($thread_id <= 0 || $uid <= 0 || strlen($content) < 1) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing or invalid thread_id/uid/content'
        ]);
        exit;
    }

    $success = $db->addComment($thread_id, $uid, $content);

    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Kommentar tillagd' : 'Kunde inte lägga till kommentar (DB-fel?)'
    ]);

} catch (Exception $e) {
    error_log("addcomment.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Serverfel: ' . $e->getMessage()
    ]);
}