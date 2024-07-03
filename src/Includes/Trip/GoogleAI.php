<?php 
namespace EcabVendasta\Includes\Trip;

class GoogleAI{

    public static function fetchData($prompt) {
        $api_key = get_option('vcab_google_ai_studio_key');
        
        $prompt = sanitize_text_field($prompt);
        $api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$api_key";
        $data = array(
            'contents' => array(
                'parts' => array(
                    array(
                        'text' => $prompt
                    )
                )
            )
        );

        $args = array(
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => wp_json_encode($data),
            'timeout' => 60
        );

        $response = wp_remote_post($api_url, $args);

        if (is_wp_error($response)) {
            return ['error' => $response->get_error_message()];
        }

        $body = wp_remote_retrieve_body($response);
        return self::getTextFromParts($body);
    }

    public static function getTextFromParts($aiOutput) {
    
        $data = json_decode($aiOutput, true);

        if (isset($data['candidates']) && isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }

        // Return null if the required keys are not found
        return null;
    
    }

}