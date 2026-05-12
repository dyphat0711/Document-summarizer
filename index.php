<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IntelliSum | AI Document Summarizer</title>
    <!-- Modern Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Phosphor Icons for modern sleek icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="frontend/css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="brand">
                <i class="ph-fill ph-file-text"></i>
                <h1>IntelliSum</h1>
            </div>
            
            <nav class="nav-menu">
                <a href="#" class="active" id="navSummarizer"><i class="ph ph-magic-wand"></i> Summarizer</a>
                <a href="#" id="navHistory"><i class="ph ph-clock-counter-clockwise"></i> History</a>
                <a href="#" id="settingsBtn"><i class="ph ph-gear"></i> Settings</a>
            </nav>

            <div class="model-info">
                <span>Powered by</span>
                <strong> Gemini API </strong>
            </div>
        </aside>

        <!-- Main Summarizer Area -->
        <main class="main-area" id="viewSummarizer">
            <header class="top-header">
                <h2>Document Summarization</h2>
                <p>Upload a file, paste a link, or type text below to generate an intelligent summary.</p>
            </header>

            <div class="workspace">
                <!-- Input Section -->
                <section class="panel input-panel">
                    <div class="panel-header">
                        <h3><i class="ph ph-text-align-left"></i> Source Material</h3>
                        <div class="actions">
                            <label for="fileUpload" class="icon-btn" title="Upload .txt File" style="cursor:pointer">
                                <i class="ph ph-upload-simple"></i>
                            </label>
                            <input type="file" id="fileUpload" accept=".txt,.md" style="display:none">
                            <button class="icon-btn" id="clearTextBtn" title="Clear Text"><i class="ph ph-trash"></i></button>
                        </div>
                    </div>
                    <div class="url-fetcher">
                        <input type="url" id="urlInput" placeholder="Or paste a news article URL here..." autocomplete="off">
                        <button class="btn btn-secondary" id="fetchUrlBtn" title="Fetch content from URL"><i class="ph ph-download-simple"></i> Fetch</button>
                    </div>
                    <div class="panel-body">
                        <textarea id="documentInput" placeholder="Paste your lengthy document, article, or text here to summarize..."></textarea>
                    </div>
                    <div class="panel-footer">
                        <div class="word-count">Words: <span id="wordCount">0</span></div>
                        <button class="btn btn-primary" id="summarizeBtn">
                            <i class="ph-fill ph-sparkle"></i> Summarize Document
                        </button>
                    </div>
                </section>

                <!-- Output Section -->
                <section class="panel output-panel">
                    <div class="panel-header">
                        <h3><i class="ph ph-list-dashes"></i> AI Summary</h3>
                        <div class="actions">
                            <button class="icon-btn" id="copyBtn" title="Copy to Clipboard"><i class="ph ph-copy"></i></button>
                        </div>
                    </div>
                    <div class="panel-body summary-body">
                        <div id="loadingIndicator" class="loading-state hidden">
                            <div class="spinner"></div>
                            <p>Analyzing document & generating summary...</p>
                        </div>
                        <div id="summaryOutput" class="summary-content">
                            <div class="empty-state">
                                <i class="ph ph-magic-wand"></i>
                                <p>Your summary will appear here.</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- History Area (Hidden by default) -->
        <main class="main-area hidden" id="viewHistory">
            <header class="top-header">
                <h2>Summary History</h2>
                <p>Recent documents you have summarized.</p>
            </header>
            <div class="workspace">
                <section class="panel" style="flex:1;">
                    <div class="panel-body history-body" id="historyContainer">
                        <div class="loading-state">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Settings Modal -->
    <div class="modal-overlay" id="settingsModal">
        <div class="modal">
            <header class="modal-header">
                <h2>Configuration</h2>
                <button class="close-btn" id="closeSettings"><i class="ph ph-x"></i></button>
            </header>
            <div class="modal-body">
                <p class="help-text" style="color: #10b981"><i class="ph-fill ph-shield-check"></i> API Key is securely managed by the PHP backend.</p>
                <div class="form-group">
                    <label for="summaryLength">Summary Length</label>
                    <select id="summaryLength">
                        <option value="short">Short (Bullet points)</option>
                        <option value="medium" selected>Medium (A few paragraphs)</option>
                        <option value="detailed">Detailed (Comprehensive breakdown)</option>
                    </select>
                </div>
            </div>
            <footer class="modal-footer">
                <button class="btn btn-primary" id="saveSettings">Save Configuration</button>
            </footer>
        </div>
    </div>

    <script src="frontend/js/app.js"></script>
</body>
</html>
