<?php
// Simple package creation API
// Reads JSON payload with 'billing' field and stores package info

header('Content-Type: application/json');

// Read input JSON
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$billing = isset($input['billing']) ? $input['billing'] : null;
if (!in_array($billing, ['prepaid', 'monthly'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid billing type']);
    exit;
}

// Prepare package data
$package = [
    'billing' => $billing,
    'created_at' => date('c'),
];

// Store packages in a JSON file for demo purposes
$storage = __DIR__ . '/packages.json';
$packages = [];
if (file_exists($storage)) {
    $contents = file_get_contents($storage);
    if ($contents !== '') {
        $packages = json_decode($contents, true) ?: [];
    }
}

$packages[] = $package;
file_put_contents($storage, json_encode($packages, JSON_PRETTY_PRINT));

echo json_encode(['status' => 'ok', 'package' => $package]);
