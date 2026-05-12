<?php
// backend/models/GeminiService.php
require_once __DIR__ . '/../config/config.php';

class GeminiService {
    
    public function summarizeDocument($text, $lengthStyle) {
        $promptModifier = "";
        if ($lengthStyle === 'short') {
            $promptModifier = "Provide a very brief summary using bullet points only. Focus only on the absolute core message.";
        } else if ($lengthStyle === 'detailed') {
            $promptModifier = "Provide a comprehensive, detailed breakdown of the document. Include key arguments, supporting evidence, and conclusions.";
        } else {
            $promptModifier = "Provide a clear, medium-length summary in a few paragraphs.";
        }

        $prompt = "You are an expert Document Summarizer.\n"
                . "Instruction: " . $promptModifier . "\n"
                . "CRITICAL: You MUST write the summary in the SAME LANGUAGE as the original document text. If the document is in Vietnamese, you MUST reply in Vietnamese.\n\n"
                . "Document Text to Summarize:\n\"\"\"\n" . $text . "\n\"\"\"";

        $postData = array(
            "contents" => array(
                array(
                    "parts" => array(
                        array("text" => $prompt)
                    )
                )
            ),
            "generationConfig" => array(
                "temperature" => 0.3
            )
        );

        $ch = curl_init(GEMINI_API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local XAMPP development

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL Error: " . $error);
        }
        
        curl_close($ch);

        $data = json_decode($response, true);

        if ($httpCode >= 400) {
            $errorMsg = isset($data['error']['message']) ? $data['error']['message'] : 'Failed to communicate with Gemini API';
            throw new Exception($errorMsg);
        }

        $generatedText = isset($data['candidates'][0]['content']['parts'][0]['text']) ? $data['candidates'][0]['content']['parts'][0]['text'] : null;

        if (!$generatedText) {
            throw new Exception('Received empty response from the model.');
        }

        return $generatedText;
    }
}
