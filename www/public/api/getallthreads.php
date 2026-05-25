<?php
/**
 * API-ENDPOINT: Hämta alla trådar/inlägg
 * ======================================
 * Syftet: Hämta en list av alla inlägg/trådar från forumparten (feed)
 * HTTP-metod: GET
 * Indata: Ingen
 * Utdata: JSON med lista av alla trådar
 * SÄKERHET: OK - detta är en offentlig feed (som Twitter/Facebook feed)
 */

session_start();

// ========== CORS-HEADERS ==========
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS'); // Obs: Här står POST men denna är GET - detta är ett bug!
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

// ========== VALIDERING: Endast GET-requests ==========
// OBS! Här är ett bug - CORS-headern säger POST men denna endpoint är GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only GET allowed']);
    exit;
}

// ========== INLADDNING AV DATABASKLASS ==========
include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();

// ========== DATABAS-OPERATION: Hämta alla trådar ==========
$threads = $db->getAllThreads();

// ========== RESULTAT: Sätt lämpligt svar ==========
$result = [
    'success' => !empty($threads), // true om det finns trådar, false om ingen
    'message' => empty($threads) ? 'No posts yet' : 'Posts loaded',
    'threads' => $threads
];

echo json_encode($result, JSON_UNESCAPED_UNICODE);