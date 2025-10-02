<?php
    include(realpath(__DIR__ . '/../../..') . '/inc/pre_config.php');
    include(realpath(__DIR__ . '/../../../..') . '/config/config.php');
    include(PERCH_CORE . '/inc/loader.php');
    include(PERCH_CORE . '/runtime/core.php');
    include(PERCH_CORE . '/inc/apps.php');
    $Perch = PerchAdmin::fetch();
    include(PERCH_CORE . '/inc/auth_light.php');

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    echo json_encode(['content' => 'Invalid request']);
    exit;
}
$prompt = isset($input['prompt']) ? $input['prompt'] : '';

    $api_key = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : getenv('OPENAI_API_KEY');
    if (!class_exists('PerchUtil')) {
        include_once PERCH_CORE . '/lib/PerchUtil.class.php';
    }


    require_once __DIR__ . '/../PerchContent_AI.class.php';

    $input  = json_decode(file_get_contents('php://input'), true);
    $prompt = isset($input['prompt']) ? $input['prompt'] : '';

    $ai      = new PerchContent_AI($api_key);
    $content = $ai->generate($prompt);

    echo json_encode(['content' => $content]);
?>
