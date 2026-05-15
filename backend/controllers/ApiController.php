<?php
// backend/controllers/ApiController.php
require_once __DIR__ . '/../models/GeminiService.php';
require_once __DIR__ . '/../models/ScraperService.php';
require_once __DIR__ . '/../models/HistoryModel.php';

class ApiController {
    
    public function handleRequest() {
        header('Content-Type: application/json');
        
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        try {
            switch ($action) {
                case 'summarize':
                    $this->summarize();
                    break;
                case 'fetch_url':
                    $this->fetchUrl();
                    break;
                case 'get_history':
                    $this->getHistory();
                    break;
                case 'delete_history':
                    $this->deleteHistory();
                    break;
                default:
                    throw new Exception("Invalid action.");
            }
        } catch (Exception $e) {
            echo json_encode(array(
                "status" => "error",
                "message" => $e->getMessage()
            ));
        }
    }

    private function summarize() {
        // Get JSON POST data
        $input = json_decode(file_get_contents('php://input'), true);
        $text = isset($input['text']) ? trim($input['text']) : '';
        $lengthStyle = isset($input['lengthStyle']) ? $input['lengthStyle'] : 'medium';

        if (empty($text)) {
            throw new Exception("Input text cannot be empty.");
        }

        // 1. Call Gemini Model
        $gemini = new GeminiService();
        $summary = $gemini->summarizeDocument($text, $lengthStyle);

        // 2. Save to MySQL
        $history = new HistoryModel();
        $history->saveSummary($text, $summary, $lengthStyle);

        // 3. Return response
        echo json_encode(array(
            "status" => "success",
            "data" => $summary
        ));
    }

    private function fetchUrl() {
        $input = json_decode(file_get_contents('php://input'), true);
        $url = isset($input['url']) ? trim($input['url']) : '';

        if (empty($url)) {
            throw new Exception("URL cannot be empty.");
        }

        $scraper = new ScraperService();
        $text = $scraper->fetchUrlContent($url);

        echo json_encode(array(
            "status" => "success",
            "data" => $text
        ));
    }

    private function getHistory() {
        $history = new HistoryModel();
        $records = $history->getHistory(10);

        echo json_encode(array(
            "status" => "success",
            "data" => $records
        ));
    }

    private function deleteHistory() {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = isset($input['id']) ? (int)$input['id'] : 0;

        if ($id <= 0) {
            throw new Exception("Invalid history record.");
        }

        $history = new HistoryModel();
        $deleted = $history->deleteHistory($id);

        if (!$deleted) {
            throw new Exception("History record not found or could not be deleted.");
        }

        echo json_encode(array(
            "status" => "success",
            "message" => "History record deleted successfully."
        ));
    }
}
