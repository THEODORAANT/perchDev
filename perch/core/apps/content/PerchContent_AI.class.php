<?php
class PerchContent_AI
{
    private $api_key;

    public function __construct($api_key = null)
    {
        if ($api_key !== null) {
            $this->api_key = $api_key;
        } elseif (defined('OPENAI_API_KEY')) {
            $this->api_key = OPENAI_API_KEY;
        } else {
            $this->api_key = '';
        }
    }

    public function generate($prompt)
    {
        if (!$this->api_key) {
            return 'OpenAI API key is not set.';
        }

        $ch = curl_init('https://api.openai.com/v1/completions');
        $data = [
            'model' => 'gpt-3.5-turbo-instruct',
            'prompt' => $prompt,
            'max_tokens' => 150,
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key,
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($response === false) {
            PerchUtil::debug('OpenAI cURL error: ' . curl_error($ch));
            return 'Failed to contact AI service.';
        }

        $resp = json_decode($response, true);
        if (isset($resp['error']['message'])) {
            return $resp['error']['message'];
        }
        if (isset($resp['choices'][0]['text'])) {
            return trim($resp['choices'][0]['text']);
        }

        PerchUtil::debug('OpenAI HTTP status: ' . $http_status);
        return 'No content generated.';
    }
}
?>
