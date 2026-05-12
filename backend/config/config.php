<?php
// backend/config/config.php

// Load .env file
$envFile = __DIR__ . '/../../.env';
$env = [];
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
}

// Define constants for the application
define('DB_HOST', isset($env['DB_HOST']) ? $env['DB_HOST'] : 'localhost');
define('DB_USER', isset($env['DB_USER']) ? $env['DB_USER'] : 'root');
define('DB_PASS', isset($env['DB_PASS']) ? $env['DB_PASS'] : '');
define('DB_NAME', isset($env['DB_NAME']) ? $env['DB_NAME'] : 'intellisum');

// Gemini API Key
define('GEMINI_API_KEY', isset($env['GEMINI_API_KEY']) ? $env['GEMINI_API_KEY'] : '');

// API Endpoint
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . GEMINI_API_KEY);
