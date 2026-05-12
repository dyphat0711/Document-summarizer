<?php
// backend/models/ScraperService.php

class ScraperService {
    public function fetchUrlContent($url) {
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception("Invalid URL format.");
        }

        // Initialize cURL to fetch webpage content
        $ch = curl_init();
        
        // Mimic a standard browser to avoid basic blocks
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("Failed to fetch URL: " . $error);
        }
        
        curl_close($ch);

        if ($httpCode >= 400 || empty($html)) {
            throw new Exception("Failed to retrieve content. HTTP Status: " . $httpCode);
        }

        // Basic DOM Parsing using PHP's DOMDocument
        $doc = new DOMDocument();
        libxml_use_internal_errors(true); // Suppress HTML5 parsing warnings
        $doc->loadHTML($html);
        libxml_clear_errors();

        // Extract <p> tags
        $paragraphs = $doc->getElementsByTagName('p');
        $extractedText = "";
        
        foreach ($paragraphs as $p) {
            $text = trim($p->nodeValue);
            // Filter out very short UI snippets
            if (strlen($text) > 40) {
                $extractedText .= $text . "\n\n";
            }
        }

        $extractedText = trim($extractedText);

        // Cloudflare detection
        if (strpos($extractedText, 'Please enable JS') !== false || strpos($extractedText, 'disable any ad blocker') !== false || strpos($extractedText, 'Cloudflare') !== false) {
            throw new Exception('This website has strong anti-bot protection (like Cloudflare). Please copy and paste the text manually.');
        }

        if (empty($extractedText)) {
            throw new Exception("Could not extract readable paragraphs from this website.");
        }

        return $extractedText;
    }
}
