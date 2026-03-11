<?php
session_start();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

$thread_id = isset($_GET['thread_id']) ? (int) $_GET['thread_id'] : 0;

$result = ['success' => false, 'message' => 'Missing thread_id'];

if ($thread_id > 0) {
    $comments = $db->getCommentsForThread($thread_id);
    $result = [
        'success' => true,
        'comments' => $comments,
        'message' => empty($comments) ? 'No comments yet' : 'Comments loaded'
    ];
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);