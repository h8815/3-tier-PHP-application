<?php
// TIER 1: PRESENTATION LAYER - EDIT
// ALL PHP database logic is removed. The page is now mostly static HTML with JavaScript.
$id = $_GET['id'] ?? null;
if (!$id) {
    // If no ID is provided, redirect or show an error immediately
    header("Location: view.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title id="pageTitle">Edit Student - Loading...</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="icon.png">
    <link href="style.css" rel="stylesheet">
    <style>
        .edit-header {
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeInDown 0.6s ease-out;
        }
        
        .edit-title {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .student-id-badge {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(11, 94, 215, 0.1) 100%);
            border-radius: 20px;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 0.95rem;
            margin-top: 0.5rem;
            border: 2px solid var(--primary-color);
        }
        
        .form-card {
            animation: fadeInUp 0.6s ease-out;
            animation-delay: 0.2s;
            opacity: 0;
            animation-fill-mode: forwards;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: var(--text-muted);
            z-index: 5;
        }
        
        .form-control-with-icon {
            padding-left: 3rem !important;
        }
        
        .info-card {
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.05) 0%, rgba(11, 94, 215, 0.05) 100%);
            border: 1px solid rgba(23, 162, 184, 0.2);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        #loading-state {
            text-align: center;
            padding: 5rem 0;
        }
    </style>
</head>
<body class="container py-5">
    <div class="d-flex justify-content-end mb-3">
        <button id="theme-toggle" title="Toggle Light/Dark Mode">☀️</button>
    </div>

    <div class="edit-header">
        <h1 class="edit-title">✏️ Edit Student Record</h1>
        <p class="text-muted" style="font-size: 1.1rem;">Update student information</p>
        <div class="student-id-badge">
            🆔 Student ID: <span id="student-id-display"><?= htmlspecialchars($id); ?></span>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">

            <div id="loading-state">
                <span class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></span>
                <p class="text-muted mt-3">Fetching student data...</p>
            </div>

            <div id="content-container" style="display: none;">
                <div class="info-card">
                    <div class="d-flex align-items-center">
                        <div style="font-size: 2rem; margin-right: 1rem;">ℹ️</div>
                        <div>
                            <strong style="color: var(--info-color);">Current Student:</strong>
                            <p class="mb-0 text-muted" style="font-size: 0.95rem;">
                                <span id="current-name-display"></span> • <span id="current-email-display"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card shadow-lg border-0 form-card">
                    <div class="card-body p-4">
                        <div id="update-error-alert" class="alert alert-danger alert-dismissible fade show" role="alert" style="display:none;">
                            <strong>⚠️ Update Failed!</strong> <span id="update-error-message"></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        <form autocomplete="off" class="needs-validation" novalidate id="editForm">
                            <div class="form-floating mb-3 position-relative">
                                <span class="input-icon">👤</span>
                                <input type="text" name="name" class="form-control form-control-with-icon" id="nameInput" 
                                    placeholder="Full Name" required minlength="3">
                                <label for="nameInput" style="padding-left: 3rem;">Full Name</label>
                                <div class="invalid-feedback">Name is required (minimum 3 characters).</div>
                            </div>
                            
                            <div class="form-floating mb-3 position-relative">
                                <span class="input-icon">📧</span>
                                <input type="email" name="email" class="form-control form-control-with-icon" id="emailInput" 
                                    placeholder="Email Address" required>
                                <label for="emailInput" style="padding-left: 3rem;">Email Address</label>
                                <div class="invalid-feedback">A valid email address is required.</div>
                            </div>
                            
                            <div class="form-floating mb-4 position-relative">
                                <span class="input-icon">🎂</span>
                                <input type="number" name="age" class="form-control form-control-with-icon" id="ageInput" 
                                    placeholder="Age" required min="10" max="100">
                                <label for="ageInput" style="padding-left: 3rem;">Age</label>
                                <div class="invalid-feedback">Age must be between 10 and 100.</div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" id="save-btn" class="btn btn-primary shadow-sm" style="padding: 0.85rem;">
                                    <span style="font-size: 1.1rem;">💾 Save Changes</span>
                                </button>
                                
                                <button type="button" class="btn btn-outline-secondary" onclick="resetToOriginal()">
                                    🔄 Reset to Original
                                </button>
                                
                                <a href="view.php" class="btn btn-outline-primary">
                                    ← Back to Student List
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-3" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.05) 0%, rgba(255, 152, 0, 0.05) 100%);">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong style="color: var(--warning-color);">⚠️ Permanent Action</strong>
                                <p class="mb-0 text-muted" style="font-size: 0.85rem;">Delete this student record</p>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm"
                               onclick="handleDeleteOnEditPage(<?= htmlspecialchars($id); ?>, document.getElementById('nameInput').value)">
                                🗑️ Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <p class="text-muted" style="font-size: 0.9rem;">
            Student Management System © 2025
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // TIER 1 JAVASCRIPT LOGIC - Communicates with Tier 2 API (GET and PUT)
        const API_URL = '<?php echo getenv("API_URL") ?: "http://localhost:8081/api/students.php"; ?>';
        const STUDENT_ID = <?= json_encode(htmlspecialchars($id)); ?>;
        let originalValues = {};

        const nameInput = document.getElementById('nameInput');
        const emailInput = document.getElementById('emailInput');
        const ageInput = document.getElementById('ageInput');
        const editForm = document.getElementById('editForm');
        const saveBtn = document.getElementById('save-btn');
        const updateErrorAlert = document.getElementById('update-error-alert');
        const updateErrorMessage = document.getElementById('update-error-message');
        const loadingState = document.getElementById('loading-state');
        const contentContainer = document.getElementById('content-container');
        const pageTitle = document.getElementById('pageTitle');
        const currentNameDisplay = document.getElementById('current-name-display');
        const currentEmailDisplay = document.getElementById('current-email-display');

        function showError(message) {
            updateErrorMessage.textContent = message;
            updateErrorAlert.style.display = 'block';
        }

        function hideError() {
            updateErrorAlert.style.display = 'none';
        }
        
        function resetToOriginal() {
            nameInput.value = originalValues.Name;
            emailInput.value = originalValues.Email;
            ageInput.value = originalValues.Age;
            editForm.classList.remove('was-validated');
            // Hide error if shown
            hideError(); 
        }

        async function loadStudent() {
            loadingState.style.display = 'block';
            contentContainer.style.display = 'none';

            try {
                const response = await fetch(`${API_URL}?id=${STUDENT_ID}`);
                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    const student = data.data;
                    
                    // Store original data
                    originalValues = {
                        Name: student.Name,
                        Email: student.Email,
                        Age: student.Age
                    };

                    // Populate form fields
                    nameInput.value = student.Name;
                    emailInput.value = student.Email;
                    ageInput.value = student.Age;

                    // Update display elements
                    pageTitle.textContent = `Edit Student - ${student.Name}`;
                    currentNameDisplay.textContent = student.Name;
                    currentEmailDisplay.textContent = student.Email;

                    // Show content
                    loadingState.style.display = 'none';
                    contentContainer.style.display = 'block';

                } else if (response.status === 404) {
                    alert('Error: Student not found.');
                    window.location.href = 'view.php';
                } else {
                    throw new Error(data.message || 'API error during fetch.');
                }
            } catch (error) {
                console.error('Fetch Error:', error);
                alert('A network or API connection error occurred. Redirecting to list.');
                window.location.href = 'view.php';
            }
        }

        editForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            hideError();

            if (!editForm.checkValidity()) {
                editForm.classList.add('was-validated');
                return;
            }

            // Get form data and add ID
            const formData = new FormData(editForm);
            const data = Object.fromEntries(formData);
            data.id = parseInt(STUDENT_ID);
            data.age = parseInt(data.age); // Convert age to integer

            // Show loading state
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            saveBtn.disabled = true;

            try {
                const response = await fetch(API_URL, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const responseData = await response.json();

                if (response.ok && responseData.status === 'updated') {
                    // Success: Redirect to view.php with success status
                    window.location.href = `view.php?status=updated`;
                } else {
                    // API returned error message
                    const message = responseData.message || 'An unknown error occurred on the server.';
                    showError(message);
                }
            } catch (error) {
                console.error('Network Error:', error);
                showError('A network error prevented the update. Check server status.');
            } finally {
                // Reset button state
                saveBtn.innerHTML = '<span style="font-size: 1.1rem;">💾 Save Changes</span>';
                saveBtn.disabled = false;
            }
        });

        // Delete Handler (for the button on the edit page)
        function handleDeleteOnEditPage(id, name) {
            if (confirm(`⚠️ Are you sure you want to delete this record?\n\nStudent: ${name}\nThis action cannot be undone.`)) {
                // Use the same DELETE logic as view.php, but redirect to view.php on success
                fetch(API_URL, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'deleted') {
                        window.location.href = `view.php?status=deleted`;
                    } else {
                        alert(`Deletion failed. Error: ${data.message || 'Unknown error.'}`);
                    }
                })
                .catch(error => alert('Network error during deletion.'));
            }
        }


        // --- UI & Initialization ---
        
        // Theme Toggle (rest of JS functions remain)
        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;
        const storedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

        function applyTheme(theme) {
            body.setAttribute('data-bs-theme', theme);
            themeToggle.innerHTML = theme === 'dark' ? '🌙' : '☀️';
            localStorage.setItem('theme', theme);
        }

        applyTheme(storedTheme);

        themeToggle.addEventListener('click', () => {
            const currentTheme = body.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            applyTheme(newTheme);
        });
        
        // Form Validation
        (function () {
          'use strict'
          const forms = document.querySelectorAll('.needs-validation')
          Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
              if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
              }
              form.classList.add('was-validated')
            }, false)
          })
        })()


        window.addEventListener('load', () => {
            if (STUDENT_ID) {
                loadStudent();
            }
        });
    </script>
</body>
</html>