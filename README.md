# IntelliSum - AI Document Summarizer

IntelliSum is a PHP web application that summarizes manually entered text or webpage content using the Google Gemini API. It stores summary history in MySQL and provides a responsive web interface.

## Requirements

- PHP 8.0 or newer
- MySQL or MariaDB
- PHP extensions:
  - `pdo_mysql`
  - `curl`
- Google Gemini API key
- A local web server such as XAMPP, Laragon, WAMP, or PHP built-in server

## Setup Instructions

1. Clone or copy the project into your web server directory.

   Example for XAMPP:

   ```bash
   C:\xampp\htdocs\document_summarizer
   ```

2. Create the MySQL database.

   Open phpMyAdmin or MySQL CLI, then import:

   ```bash
   database.sql
   ```

   This creates the `intellisum` database and the `summaries` table.

3. Create the environment file.

   Copy `.env.example` to `.env` in the project root:

   ```bash
   cp .env.example .env
   ```

   On Windows PowerShell:

   ```powershell
   Copy-Item .env.example .env
   ```

4. Update `.env` with your local database settings and Gemini API key.

   ```env
   # Database Configuration
   DB_HOST=localhost
   DB_USER=root
   DB_PASS=
   DB_NAME=intellisum

   # Google Gemini API Key
   GEMINI_API_KEY=YOUR_GEMINI_API_KEY_HERE
   ```

5. Start your local server.

   If using XAMPP:

   - Start Apache.
   - Start MySQL.
   - Open the project in your browser:

   ```text
   http://localhost/document_summarizer/
   ```

   If using PHP built-in server from the project root:

   ```bash
   php -S localhost:8000
   ```

   Then open:

   ```text
   http://localhost:8000
   ```

## Testing Credentials

This project does not include user authentication, so no application username or password is required for testing.

Use these default local database credentials if you are running XAMPP with the standard MySQL setup:

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=intellisum
```

For Gemini API testing, use your own Google Gemini API key:

```env
GEMINI_API_KEY=YOUR_GEMINI_API_KEY_HERE
```

## Feature Checklist

- Manual text input
- URL content extraction
- AI summarization with Google Gemini API
- Summary result display
- Summary history storage
- Summary history viewing
- Summary history deletion
- Error handling
- Responsive interface
- Asynchronous frontend requests using `fetch()`

## Project Structure

```text
document_summarizer/
+-- api.php
+-- index.php
+-- database.sql
+-- .env.example
+-- backend/
|   +-- config/
|   |   +-- config.php
|   |   +-- Database.php
|   +-- controllers/
|   |   +-- ApiController.php
|   +-- models/
|       +-- GeminiService.php
|       +-- HistoryModel.php
|       +-- ScraperService.php
+-- frontend/
    +-- css/
    |   +-- style.css
    +-- js/
        +-- app.js
```

## API Endpoints

The frontend communicates with `api.php` using asynchronous JSON requests.

| Action | Method | Description |
| ------ | ------ | ----------- |
| `api.php?action=summarize` | `POST` | Summarizes text and stores the result in history. |
| `api.php?action=fetch_url` | `POST` | Extracts text content from a webpage URL. |
| `api.php?action=get_history` | `GET` | Returns recent summary history records. |
| `api.php?action=delete_history` | `POST` | Deletes a history record by ID. |

## Notes

- Keep `.env` private and do not commit real API keys.
- If history does not save, verify that MySQL is running and the `.env` database values are correct.
- If summarization fails, verify that `GEMINI_API_KEY` is valid and the PHP `curl` extension is enabled.
