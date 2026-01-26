<?php
$response['data'] = "Hej";
// Behövs för session-cookies och anger att formatet är json
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// Gör om arrayen till en array med json-objekt
echo json_encode($response, JSON_UNESCAPED_UNICODE);
