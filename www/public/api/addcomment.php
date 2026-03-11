<?php
session_start();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

// ... headers ...

$content = trim($_POST['content'] ?? '');
$thread_id = (int) ($_POST['thread_id'] ?? 0);
$uid = (int) ($_POST['uid'] ?? 0);

if ($thread_id > 0 && $uid > 0 && strlen($content) >= 1) {
    if ($db->addComment($thread_id, $uid, $content)) {
        $result['success'] = true;
        $result['message'] = 'Comment added';
    } else {
        $result['message'] = 'Database error';
    }
} else {
    $result['message'] = 'Missing/invalid parameters';
}