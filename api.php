<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['titre'], $input['text'], $input['iden'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Champs manquants']);
    exit;
}

$titre = trim($input['titre']);
$text = trim($input['text']);
$iden = trim($input['iden']);

if ($titre === '' || $text === '' || $iden === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Champs vides']);
    exit;
}

$token = 'o.IuroXD5QHaBx4bMrvvBQgYsTd9pBVzCJ'; // Ton token

$data = [
    'type' => 'note',
    'title' => $titre,
    'body' => $text,
    'device_iden' => $iden
];

$ch = curl_init('https://api.pushbullet.com/v2/pushes');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Access-Token: ' . $token,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo json_encode(['success' => true, 'message' => 'Notification envoyée']);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Erreur API Pushbullet',
        'http_code' => $httpCode,
        'details' => json_decode($response, true)
    ]);
}