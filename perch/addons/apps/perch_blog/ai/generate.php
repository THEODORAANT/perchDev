<?php
require_once __DIR__ . '/../lib/PerchBlog_AI.class.php';

$input = json_decode(file_get_contents('php://input'), true);
$prompt = isset($input['prompt']) ? $input['prompt'] : '';

$ai = new PerchBlog_AI();
$content = $ai->generate($prompt);

echo json_encode(['content' => $content]);
?>
