<?php
class PerchContent_AI
{
    private $api_key;

    public function __construct()
    {
        $this->api_key = getenv('OPENAI_API_KEY');
    }

    public function generate($prompt)
    {
        if (!$this->api_key) {
            return 'OpenAI API key is not set.';
        }

        $ch = curl_init('https://api.openai.com/v1/completions');
        $data = [
            'model' => 'text-davinci-003',
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
        if ($response === false) {
            return 'Failed to contact AI service.';
        }

        $resp = json_decode($response, true);
        if (isset($resp['choices'][0]['text'])) {
            return trim($resp['choices'][0]['text']);
        }

        return 'No content generated.';
    }
}
?>
