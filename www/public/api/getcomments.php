<?php
/**
 * API-ENDPOINT: Hämta kommentarer för en tråd
 * ============================================
 * Syftet: Hämta alla kommentarer på en specifik inlägg/tråd
 * HTTP-metod: GET
 * Indata: thread_id (ID på tråden)
 * Utdata: JSON med lista av kommentarer
 * SÄKERHET: OK - kommentarer är offentliga på den specifika tråden
 */

session_start();

// ========== CORS-HEADERS ==========
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

// Kommentarer är offentliga - ingen behörighetskontroll behövs här

// ========== INLADDNING AV DATABASKLASS ==========
include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();

// ========== HÄMTA OCH VALIDERA INDATA ==========
// thread_id: ID på tråden vi vill se kommentarer för
$thread_id = isset($_GET['thread_id']) ? (int) $_GET['thread_id'] : 0;

$result = ['success' => false, 'message' => 'Missing thread_id'];

// ========== KONTROLL: Verifiera att thread_id är giltigt (större än 0) ==========
if ($thread_id > 0) {
    // ========== DATABAS-OPERATION: Hämta kommentarer för denna tråd ==========
    $comments = $db->getCommentsForThread($thread_id);

    $result = [
        'success' => true,
        'comments' => $comments,
        'message' => empty($comments) ? 'No comments yet' : 'Comments loaded'
    ];
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);