<?php
// backend/models/HistoryModel.php
require_once __DIR__ . '/../config/Database.php';

class HistoryModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function saveSummary($originalText, $summaryText, $summaryType) {
        if (!$this->db) return false; // Fail gracefully if no DB connection

        $query = "INSERT INTO summaries (original_text, summary_text, summary_type) VALUES (:orig, :summ, :type)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':orig', $originalText);
        $stmt->bindParam(':summ', $summaryText);
        $stmt->bindParam(':type', $summaryType);

        return $stmt->execute();
    }

    public function getHistory($limit = 10) {
        if (!$this->db) return [];

        $query = "SELECT id, LEFT(original_text, 100) as preview, summary_text, summary_type, created_at FROM summaries ORDER BY id DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
