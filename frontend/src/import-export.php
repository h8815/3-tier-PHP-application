<?php
session_start();

// Check authentication
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <title>Import/Export - Student Hub</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css" rel="stylesheet">
    <style>
        [data-bs-theme="dark"] {
            --bs-body-bg: #1a1a1a;
            --bs-body-color: #ffffff;
        }
        
        [data-bs-theme="dark"] .card {
            background: #2d2d2d !important;
            color: #ffffff;
            border-color: #404040;
        }
        
        [data-bs-theme="dark"] .top-nav {
            background: #2d2d2d !important;
        }
        
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .top-nav {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .nav-brand {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .import-export-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border: none;
            height: 100%;
        }
        
        .drop-zone {
            border: 3px dashed #667eea;
            border-radius: 15px;
            padding: 3rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: rgba(102, 126, 234, 0.05);
        }
        
        .drop-zone:hover {
            border-color: #764ba2;
            background: rgba(102, 126, 234, 0.1);
        }
        
        .drop-zone.dragover {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .feature-card {
                padding: 1.5rem;
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body>
    <nav class="top-nav">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="nav-brand">
                    <i class="bi bi-mortarboard-fill"></i> STUDENT HUB
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <button id="theme-toggle" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-sun-fill"></i>
                    </button>
                    <span class="text-muted d-none d-md-inline">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>
                    </span>
                    <a href="view.php" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-arrow-left"></i> <span class="d-none d-md-inline">Back to Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="import-export-header">
            <h1 class="page-title">Import/Export Students</h1>
            <p class="text-muted" style="font-size: 1.1rem;">Bulk manage your student data with CSV files</p>
        </div>

        <div class="row g-4">
            <!-- Export Section -->
            <div class="col-lg-6">
                <div class="card feature-card">
                    <div class="text-center mb-4">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">📤</div>
                        <h3 class="fw-bold text-primary">Export Students</h3>
                        <p class="text-muted">Download all your students as a CSV file</p>
                    </div>
                    
                    <div class="d-grid gap-3">
                        <button id="export-btn" class="btn btn-primary btn-lg">
                            <i class="bi bi-download"></i> Export to CSV
                        </button>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Export includes:</strong> Name, Email, Age, Phone, Address, Status, Enrollment Date
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Section -->
            <div class="col-lg-6">
                <div class="card feature-card">
                    <div class="text-center mb-4">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">📥</div>
                        <h3 class="fw-bold text-success">Import Students</h3>
                        <p class="text-muted">Upload a CSV file to add multiple students</p>
                    </div>
                    
                    <div class="drop-zone" id="drop-zone">
                        <div id="drop-content">
                            <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #667eea;"></i>
                            <h5 class="mt-3 mb-2">Drag & Drop CSV File</h5>
                            <p class="text-muted mb-3">or click to browse files</p>
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('csv-input').click()">
                                Choose CSV File
                            </button>
                        </div>
                        <div id="file-info" style="display: none;">
                            <i class="bi bi-file-earmark-text" style="font-size: 3rem; color: #28a745;"></i>
                            <h5 class="mt-3 mb-2" id="file-name"></h5>
                            <button type="button" class="btn btn-success" id="import-btn">
                                <i class="bi bi-upload"></i> Import Students
                            </button>
                            <button type="button" class="btn btn-outline-secondary ms-2" onclick="clearFile()">
                                Cancel
                            </button>
                        </div>
                    </div>
                    
                    <input type="file" id="csv-input" accept=".csv" style="display: none;">
                    
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>CSV Format:</strong> Name, Email, Age, Phone, Address, Status<br>
                        <small>Status: active, inactive, or graduated</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sample CSV Format -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card feature-card">
                    <h4 class="fw-bold mb-3"><i class="bi bi-file-text"></i> Sample CSV Format</h4>
                    <div class="bg-light p-3 rounded">
                        <code>
Name,Email,Age,Phone,Address,Status<br>
John Doe,john@example.com,20,+1234567890,123 Main St,active<br>
Jane Smith,jane@example.com,22,+0987654321,456 Oak Ave,graduated
                        </code>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-outline-primary" onclick="downloadSample()">
                            <i class="bi bi-download"></i> Download Sample CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script>
        const notyf = new Notyf({
            duration: 4000,
            position: { x: 'right', y: 'top' },
            dismissible: true
        });

        let API_URL = '/api/csv.php';
        
        // Theme toggle
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;
        const currentTheme = localStorage.getItem('theme') || 'light';
        
        html.setAttribute('data-bs-theme', currentTheme);
        updateThemeIcon(currentTheme);
        
        themeToggle.addEventListener('click', () => {
            const theme = html.getAttribute('data-bs-theme') === 'light' ? 'dark' : 'light';
            html.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);
            updateThemeIcon(theme);
        });
        
        function updateThemeIcon(theme) {
            const icon = themeToggle.querySelector('i');
            icon.className = theme === 'light' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
        }

        // Export functionality
        document.getElementById('export-btn').addEventListener('click', async () => {
            const btn = document.getElementById('export-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exporting...';
            btn.disabled = true;
            
            try {
                const response = await fetch(API_URL, {
                    method: 'GET',
                    credentials: 'include'
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `students_export_${new Date().toISOString().slice(0,10)}.csv`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                    notyf.success('Students exported successfully!');
                } else {
                    notyf.error('Export failed');
                }
            } catch (error) {
                notyf.error('Network error during export');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });

        // File handling
        const dropZone = document.getElementById('drop-zone');
        const csvInput = document.getElementById('csv-input');
        const dropContent = document.getElementById('drop-content');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        let selectedFile = null;

        // Drag and drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'));
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'));
        });

        dropZone.addEventListener('drop', handleDrop);
        dropZone.addEventListener('click', () => csvInput.click());
        csvInput.addEventListener('change', (e) => handleFiles(e.target.files));

        function handleDrop(e) {
            handleFiles(e.dataTransfer.files);
        }

        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];
                if (file.type === 'text/csv' || file.name.endsWith('.csv')) {
                    selectedFile = file;
                    fileName.textContent = file.name;
                    dropContent.style.display = 'none';
                    fileInfo.style.display = 'block';
                } else {
                    notyf.error('Please select a CSV file');
                }
            }
        }

        function clearFile() {
            selectedFile = null;
            csvInput.value = '';
            dropContent.style.display = 'block';
            fileInfo.style.display = 'none';
        }

        // Import functionality
        document.getElementById('import-btn').addEventListener('click', async () => {
            if (!selectedFile) {
                notyf.error('Please select a CSV file first');
                return;
            }
            
            const btn = document.getElementById('import-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Importing...';
            btn.disabled = true;
            
            const formData = new FormData();
            formData.append('csv_file', selectedFile);
            
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    credentials: 'include',
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok && data.status === 'success') {
                    notyf.success(`Import completed: ${data.imported} students imported`);
                    if (data.errors && data.errors.length > 0) {
                        console.log('Import errors:', data.errors);
                        notyf.error(`${data.errors.length} rows had errors - check console`);
                    }
                    clearFile();
                } else {
                    notyf.error(data.message || 'Import failed');
                }
            } catch (error) {
                console.error('Import error:', error);
                notyf.error('Network error during import');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });

        // Download sample CSV
        function downloadSample() {
            const csvContent = "Name,Email,Age,Phone,Address,Status\nJohn Doe,john@example.com,20,+1234567890,123 Main St,active\nJane Smith,jane@example.com,22,+0987654321,456 Oak Ave,graduated";
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sample_students.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>