<?php
/**
 * API-ENDPOINT: Hämta CSRF-token
 * ==============================
 * Syftet: Generera och returnera en CSRF-token som måste skickas med POST-requests
 * HTTP-metod: GET
 * Indata: Ingen
 * Utdata: JSON med csrfToken
 * SÄKERHET: Skyddar mot CSRF-attackar (Cross-Site Request Forgery)
 */

session_start(); // Starta session så vi kan lagra token

// ========== HEADERS: JSON-format och CORS ==========
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

// ========== token-generering: Skapa ny CSRF-token om den inte redan finns ==========
// bin2hex(random_bytes(32)) = 64 hex-chars = 256 bitar = mycket säkert
if (empty($_SESSION['CSRFToken'])) {
    $_SESSION['CSRFToken'] = bin2hex(random_bytes(32));
}

// Returnera token till frontend - frontend skickar denna token när den gör POST-requests
echo json_encode(['csrfToken' => $_SESSION['CSRFToken']]);