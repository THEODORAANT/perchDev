<?php
header('Content-Type: application/json; charset=utf-8');

include __DIR__ . '/../../../../../core/inc/api.php';

$Perch       = Perch::fetch();
$Users       = new PerchUsers;
$CurrentUser = $Users->get_current_user();

if (!$CurrentUser || !$CurrentUser->logged_in()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Not authenticated']);
    exit;
}

$config_path = __DIR__ . '/config.php';
if (!file_exists($config_path)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Copy config.sample.php to config.php and set PERCH_AI_API_KEY.']);
    exit;
}
require_once $config_path;

if (!defined('PERCH_AI_API_KEY') || !PERCH_AI_API_KEY) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'PERCH_AI_API_KEY not set in config.php']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid JSON']);
    exit;
}

$action = isset($data['action']) ? $data['action'] : '';
$html   = isset($data['html']) ? trim($data['html']) : '';

if ($html === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Empty content']);
    exit;
}

if (strlen($html) > 60000) {
    http_response_code(413);
    echo json_encode(['ok' => false, 'error' => 'Content too long']);
    exit;
}

$prompts = [
    'improve'   => 'You revise HTML fragments for clarity, grammar, and flow. Return ONLY the revised HTML fragment. Preserve the original language, meaning, and tag structure where reasonable. Do not wrap the output in code fences, explanations, or <html>/<body> tags.',
    'summarize' => 'You summarize HTML fragments into a concise paragraph or short bullet list using HTML (<p> or <ul><li>). Return ONLY the summary HTML fragment. Do not wrap in code fences, explanations, or <html>/<body> tags.'
];

if (!isset($prompts[$action])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Unknown action']);
    exit;
}

$model = defined('PERCH_AI_MODEL') ? PERCH_AI_MODEL : 'claude-opus-4-7';

$payload = [
    'model'      => $model,
    'max_tokens' => 2048,
    'system'     => $prompts[$action],
    'messages'   => [
        ['role' => 'user', 'content' => $html]
    ]
];

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'x-api-key: ' . PERCH_AI_API_KEY,
        'anthropic-version: 2023-06-01'
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT    => 60
]);
$response = curl_exec($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err      = curl_error($ch);
curl_close($ch);

if ($response === false) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => 'Upstream error: ' . $err]);
    exit;
}

$parsed = json_decode($response, true);
if ($status !== 200 || !isset($parsed['content'][0]['text'])) {
    $msg = isset($parsed['error']['message']) ? $parsed['error']['message'] : 'Unexpected API response';
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

$out = trim($parsed['content'][0]['text']);
$out = preg_replace('/^```(?:html)?\s*|\s*```$/m', '', $out);

echo json_encode(['ok' => true, 'html' => $out]);
