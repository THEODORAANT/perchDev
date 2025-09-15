<?php
require_once __DIR__ . '/../PerchContent_AI.class.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    echo json_encode(['content' => 'Invalid request']);
    exit;
}
$prompt = isset($input['prompt']) ? $input['prompt'] : '';

$ai = new PerchContent_AI();
$content = $ai->generate($prompt);

echo json_encode(['content' => $content]);
?>
