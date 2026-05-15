document.addEventListener('DOMContentLoaded', () => {
    // --- DOM Elements ---
    
    // Navigation
    const navSummarizer = document.getElementById('navSummarizer');
    const navHistory = document.getElementById('navHistory');
    const viewSummarizer = document.getElementById('viewSummarizer');
    const viewHistory = document.getElementById('viewHistory');

    // Summarizer Inputs
    const documentInput = document.getElementById('documentInput');
    const wordCountDisplay = document.getElementById('wordCount');
    const summarizeBtn = document.getElementById('summarizeBtn');
    const clearTextBtn = document.getElementById('clearTextBtn');
    const fileUpload = document.getElementById('fileUpload');
    
    // URL Fetcher
    const urlInput = document.getElementById('urlInput');
    const fetchUrlBtn = document.getElementById('fetchUrlBtn');
    
    // Outputs
    const summaryOutput = document.getElementById('summaryOutput');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const copyBtn = document.getElementById('copyBtn');
    
    // History
    const historyContainer = document.getElementById('historyContainer');

    // Settings
    const settingsBtn = document.getElementById('settingsBtn');
    const settingsModal = document.getElementById('settingsModal');
    const closeSettings = document.getElementById('closeSettings');
    const summaryLengthSelect = document.getElementById('summaryLength');
    const saveSettingsBtn = document.getElementById('saveSettings');

    // --- Helper Functions ---

    const updateWordCount = () => {
        const text = documentInput.value.trim();
        const count = text ? text.split(/\s+/).length : 0;
        wordCountDisplay.textContent = count;
        summarizeBtn.disabled = count === 0;
    };

    // HÀM PARSE MARKDOWN DUY NHẤT CHUẨN
    const parseMarkdown = (text) => {
        if (!text) return '';
        let html = text;
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/(?<!\*)\*(?!\*)(.*?)\*/g, '<em>$1</em>');
        html = html.replace(/^[\*\-] (.*$)/gim, '<li>$1</li>');
        html = html.replace(/(<li>.*<\/li>(?:\n<li>.*<\/li>)*)/g, '<ul>$1</ul>');
        html = html.replace(/\n/g, '<br>');
        html = html.replace(/<br><ul>/g, '<ul>').replace(/<\/ul><br>/g, '</ul>');
        return html;
    };

    // --- Navigation Logic ---

    navSummarizer.addEventListener('click', (e) => {
        e.preventDefault();
        navSummarizer.classList.add('active');
        navHistory.classList.remove('active');
        viewSummarizer.classList.remove('hidden');
        viewHistory.classList.add('hidden');
    });

    navHistory.addEventListener('click', (e) => {
        e.preventDefault();
        navHistory.classList.add('active');
        navSummarizer.classList.remove('active');
        viewHistory.classList.remove('hidden');
        viewSummarizer.classList.add('hidden');
        loadHistory();
    });

    // --- Input Logic ---

    documentInput.addEventListener('input', updateWordCount);
    
    clearTextBtn.addEventListener('click', () => {
        if(documentInput.value && confirm('Are you sure you want to clear the input text?')) {
            documentInput.value = '';
            urlInput.value = '';
            updateWordCount();
        }
    });

    // --- XỬ LÝ UPLOAD FILE DUY NHẤT VÀ CHUẨN ---
    fileUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        if (!file.name.match(/\.(txt|md)$/i)) {
            alert('Vui lòng chỉ tải lên file văn bản định dạng .txt hoặc .md');
            e.target.value = ''; 
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
            documentInput.value = event.target.result;
            updateWordCount();
        };
        reader.onerror = function() {
            alert("Error reading file");
        };
        reader.readAsText(file);
    });

    // --- Backend API Calls ---

    // Fetch URL Content
    fetchUrlBtn.addEventListener('click', async () => {
        const url = urlInput.value.trim();
        if (!url) {
            alert('Please enter a valid URL first.');
            return;
        }

        try {
            new URL(url);
        } catch (_) {
            alert('Invalid URL format. Please include http:// or https://');
            return;
        }

        fetchUrlBtn.disabled = true;
        fetchUrlBtn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> Fetching...';
        
        try {
            const response = await fetch('api.php?action=fetch_url', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ url: url })
            });
            
            const data = await response.json();
            
            if (data.status === 'success') {
                documentInput.value = data.data;
                updateWordCount();
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Fetch error:', error);
            alert(`Could not extract text from the URL. Error: ${error.message}`);
        } finally {
            fetchUrlBtn.disabled = false;
            fetchUrlBtn.innerHTML = '<i class="ph ph-download-simple"></i> Fetch';
        }
    });

    // Summarize Document
    summarizeBtn.addEventListener('click', async () => {
        const text = documentInput.value.trim();
        if (!text) return;

        summaryOutput.classList.add('hidden');
        loadingIndicator.classList.remove('hidden');
        summarizeBtn.disabled = true;

        const lengthStyle = summaryLengthSelect.value;

        try {
            const response = await fetch('api.php?action=summarize', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ text: text, lengthStyle: lengthStyle })
            });
            
            const data = await response.json();
            
            loadingIndicator.classList.add('hidden');
            summaryOutput.classList.remove('hidden');
            
            if (data.status === 'success') {
                // ĐÃ ĐẶT ĐÚNG VỊ TRÍ
                summaryOutput.innerHTML = parseMarkdown(data.data);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            loadingIndicator.classList.add('hidden');
            summaryOutput.classList.remove('hidden');
            console.error(error);
            summaryOutput.innerHTML = `<div class="empty-state" style="color: #ef4444;"><i class="ph ph-warning-circle"></i><p>Error: ${error.message}</p></div>`;
        } finally {
            summarizeBtn.disabled = false;
        }
    });

    // Load History
    async function loadHistory() {
        historyContainer.innerHTML = '<div class="loading-state"><div class="spinner"></div><p>Loading history...</p></div>';
        
        try {
            const response = await fetch('api.php?action=get_history');
            const data = await response.json();
            
            if (data.status === 'success') {
                const records = data.data;
                if (records.length === 0) {
                    historyContainer.innerHTML = '<div class="empty-state"><i class="ph ph-clock"></i><p>No summary history found.</p></div>';
                    return;
                }
                
                let html = '';
                records.forEach(record => {
                    const date = new Date(record.created_at).toLocaleString();
                    html += `
                        <div class="history-item">
                            <div class="history-meta">
                                <span><i class="ph-fill ph-tag"></i> Type: ${record.summary_type}</span>
                                <span><i class="ph ph-calendar"></i> ${date}</span>
                                <button class="icon-btn danger-btn delete-history-btn" data-id="${record.id}" title="Delete history record">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </div>
                            <div class="history-preview">"${record.preview}..."</div>
                            <div class="history-summary">${parseMarkdown(record.summary_text)}</div>
                        </div>
                    `;
                });
                historyContainer.innerHTML = html;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            historyContainer.innerHTML = `<div class="empty-state" style="color: #ef4444;"><p>Failed to load history: ${error.message}</p></div>`;
        }
    }

    historyContainer.addEventListener('click', async (e) => {
        const deleteBtn = e.target.closest('.delete-history-btn');
        if (!deleteBtn) return;

        const id = Number(deleteBtn.dataset.id);
        if (!id || !confirm('Delete this history record?')) return;

        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<i class="ph ph-spinner ph-spin"></i>';

        try {
            const response = await fetch('api.php?action=delete_history', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            const data = await response.json();

            if (data.status !== 'success') {
                throw new Error(data.message);
            }

            deleteBtn.closest('.history-item').remove();

            if (!historyContainer.querySelector('.history-item')) {
                historyContainer.innerHTML = '<div class="empty-state"><i class="ph ph-clock"></i><p>No summary history found.</p></div>';
            }
        } catch (error) {
            console.error('Delete history error:', error);
            alert(`Could not delete history record. Error: ${error.message}`);
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = '<i class="ph ph-trash"></i>';
        }
    });

    // --- NÚT COPY ĐÃ ĐƯỢC SỬA LỖI ---
    copyBtn.addEventListener('click', () => {
        const summaryText = summaryOutput.innerText; // Lấy text thô từ màn hình
        if(summaryText && !summaryOutput.querySelector('.empty-state')) {
            navigator.clipboard.writeText(summaryText).then(() => {
                const icon = copyBtn.querySelector('i');
                icon.className = 'ph-fill ph-check-circle';
                icon.style.color = '#10b981';
                setTimeout(() => {
                    icon.className = 'ph ph-copy';
                    icon.style.color = '';
                }, 2000);
            });
        }
    });

    // --- Settings Modal ---
    settingsBtn.addEventListener('click', (e) => {
        e.preventDefault();
        settingsModal.classList.add('active');
    });

    closeSettings.addEventListener('click', () => {
        settingsModal.classList.remove('active');
    });

    settingsModal.addEventListener('click', (e) => {
        if (e.target === settingsModal) {
            settingsModal.classList.remove('active');
        }
    });

    saveSettingsBtn.addEventListener('click', () => {
        settingsModal.classList.remove('active');
    });

    // Initial check
    updateWordCount();
});
