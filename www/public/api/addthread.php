<?php
/**
 * API-ENDPOINT: Skapa ny tråd/inlägg
 * ==================================
 * Syftet: Låta en användare skapa ett nytt inlägg/forum-tråd
 * HTTP-metod: POST
 * Indata: uid (användar-ID), content (inläggets innehål), CSRFToken
 * Utdata: JSON med success-status och meddelande
 * SÄKERHET: Har CSRF-token validering! (Best practice)
 */

session_start();

// ========== ERROR HANDLING OCH LOGGING: Logga fel men visa dem ej för användaren ==========
ini_set('display_errors', '0'); // Dölj fel från användaren (säkerhet)
ini_set('display_startup_errors', '0');
error_reporting(E_ALL); // Logga alla fel för admin-granskning

// ========== HEADERS: JSON-format och CORS ==========
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Hantera preflight-request (webbläsare skickar detta före POST)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ========== VALIDERING: Endast POST-requests ==========
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST allowed']);
    exit;
}

try {
    // ========== SÄKERHET - CSRF-SKYDD: Verifiera CSRF-token ==========
    // CSRF (Cross-Site Request Forgery) förhindrar att andere webbplatser kan göra förfrågningar på användarens vägnars
    // Token genereras av get-token.php och lagras i $_SESSION['CSRFToken']
    $_postToken = $_POST['CSRFToken'] ?? '';

    if (empty($_postToken) || !isset($_SESSION['CSRFToken']) || $_postToken !== $_SESSION['CSRFToken']) {
        // Token matchar inte - detta är troligtvis ett CSRF-attack
        echo json_encode(['success' => false, 'message' => 'inte okej (CSRFToken matchar ej']);
        exit;
    }

    // ========== INLADDNING: Ladda databasklass ==========
    require_once __DIR__ . '/../../model/DbEgyTalk.php';

    // Verifiera att klassen laddades korrekt
    if (!class_exists('DbEgyTalk')) {
        throw new Exception('DbEgyTalk class not found - include failed');
    }

    $db = new DbEgyTalk();

    // ========== HÄMTA OCH VALIDERA INDATA ==========
    // uid: ID på användaren som skaper tråden
    // content: Själva inläggets text
    $uid = (int) ($_POST['uid'] ?? 0);
    $content = trim($_POST['content'] ?? '');

    // ========== VALIDERING: Kontrollera att data är giltiga ==========
    if ($uid <= 0 || strlen($content) < 1) {
        echo json_encode(['success' => false, 'message' => 'Missing or invalid uid/content']);
        exit;
    }

    // ========== DATABAS-OPERATION: Skapa tråden ==========
    $success = $db->addThread($uid, $content);

    // ========== RESULTAT: Skicka svar ==========
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Inlägg skapat' : 'Kunde inte skapa inlägg (DB-fel?)'
    ]);

} catch (Exception $e) {
    // ========== ERROR-HANTERING: Logga fel utan att exponera detaljer ==========
    error_log("addthread.php error: " . $e->getMessage()); // Loggad för admin
    http_response_code(500); // HTTP 500: Internal Server Error
    echo json_encode([
        'success' => false,
        'message' => 'Serverfel: ' . $e->getMessage()
    ]);
}