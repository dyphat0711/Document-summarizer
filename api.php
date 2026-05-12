<?php
// api.php
require_once __DIR__ . '/backend/controllers/ApiController.php';

// Enable basic CORS for local dev
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$controller = new ApiController();
$controller->handleRequest();
