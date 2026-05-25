<?php
/**
 * API-ENDPOINT: Skicka meddelande
 * ===============================
 * Syftet: Skicka ett privat meddelande från en användare till en annan
 * HTTP-metod: POST
 * Indata: from_id (avsändare), to_id (mottagare), message (meddelandetext)
 * Utdata: JSON med success-status och meddelande
 * SÄKERHETSPROBLEM: Ingen autentisering - kan skicka från vilken from_id som helst!
 */

session_start();

// ========== CORS-HEADERS ==========
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

// Hantera preflight-request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ========== VALIDERING: Endast POST-requests ==========
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST allowed']);
    exit;
}

// ========== INLADDNING AV DATABASKLASS ==========
include_once('../../model/DbEgyTalk.php');
$db = new DbEgyTalk();

// Standardsvar
$result = ['success' => false, 'message' => 'Failed to send message'];

// ========== HÄMTA OCH VALIDERA INDATA ==========
// from_id: Avsändarens användar-ID
// to_id: Mottagarens användar-ID
// message: Själva meddelandetexten (trimmas för att ta bort whitespace)
$from = isset($_POST['from_id']) ? intval($_POST['from_id']) : 0;
$to = isset($_POST['to_id']) ? intval($_POST['to_id']) : 0;
$text = isset($_POST['message']) ? trim($_POST['message']) : '';

// ========== KONTROLL: Verifiera att båda ID:n är giltiga och meddelandet inte är tomt ==========
if ($from && $to && $text !== '') {
    // ========== DATABAS-OPERATION: Spara meddelandet ==========
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