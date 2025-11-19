<?php
// TIER 1: PRESENTATION LAYER - INDEX
// ALL PHP database logic has been removed.
// Success redirects (which were previously handled by PHP) are now replaced by JavaScript messages.

// Status messages are now pulled from URL parameters only for network/API errors that caused a redirect.
$api_status_message = '';
$alert_class = '';

if (isset($_GET['status'])) {
    $status = htmlspecialchars($_GET['status']);
    if ($status === 'connection_error') {
        $api_status_message = '⚠️ Database connection failed. Please check credentials in db.php and ensure server is running.';
        $alert_class = 'alert-danger';
    } elseif ($status === 'api_error') {
        $api_status_message = '❌ A network error or API failure occurred. See browser console for details.';
        $alert_class = 'alert-danger';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Management System - Add Student</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="icon.png">
    <link href="style.css" rel="stylesheet">
    <style>
        /* Additional styles for entry page */
        .hero-section {
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeInDown 0.6s ease-out;
        }
        
        .hero-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            letter-spacing: -1px;
        }
        
        .hero-subtitle {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .stats-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(11, 94, 215, 0.1) 100%);
            border-radius: 20px;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        
        .form-card {
            animation: fadeInUp 0.6s ease-out;
            animation-delay: 0.2s;
            opacity: 0;
            animation-fill-mode: forwards;
        }
        
        .quick-actions {
            animation: fadeInUp 0.6s ease-out;
            animation-delay: 0.4s;
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
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
        
        .feature-card {
            text-align: center;
            padding: 1.5rem;
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
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
    </style>
</head>
<body class="container py-4">
    
    <div class="d-flex justify-content-end mb-3">
        <button id="theme-toggle" title="Toggle Light/Dark Mode">☀️</button>
    </div>

    <?php if ($api_status_message): ?>
        <div class="alert <?= $alert_class; ?> alert-dismissible fade show mb-4" role="alert">
            <strong>Error!</strong> <?= htmlspecialchars($api_status_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div id="api-response-alert" class="alert alert-dismissible fade show mb-4" role="alert" style="display:none;">
        <span id="response-message"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="hero-section">
        <h1 class="hero-title">Student Management System</h1>
        <p class="hero-subtitle">Efficiently manage and organize student records</p>
        <div class="stats-badge" id="student-count-badge">
            📊 Loading...
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-6 mb-4">
            <div class="card shadow-lg border-0 form-card">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="feature-icon mx-auto" style="width: 70px; height: 70px; font-size: 2rem;">
                            ➕
                        </div>
                        <h2 class="card-title text-primary mb-2" style="font-weight: 700;">Add New Student</h2>
                        <p class="text-muted" style="font-size: 0.95rem;">Fill in the details to register a new student</p>
                    </div>
                    
                    <form autocomplete="off" class="needs-validation" novalidate id="studentForm">
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
                        
                        <button type="submit" id="submit-btn" class="btn btn-primary w-100 shadow-sm mb-2" style="padding: 0.85rem;">
                            <span style="font-size: 1.1rem;">✓ Add Student Record</span>
                        </button>
                        
                        <button type="reset" class="btn btn-outline-secondary w-100">
                            🔄 Clear Form
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="quick-actions">
                <div class="card shadow-lg border-0 mb-3">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3" style="font-weight: 600;">⚡ Quick Actions</h5>
                        <a href="view.php" class="btn btn-outline-primary w-100 mb-2" style="padding: 0.75rem;">
                            📋 View All Students
                        </a>
                        <button onclick="document.getElementById('nameInput').focus()" class="btn btn-outline-secondary w-100">
                            ➕ Add Another Student
                        </button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="feature-card">
                            <div class="feature-icon" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                ✏️
                            </div>
                            <h6 style="font-weight: 600; font-size: 0.9rem;">Easy Edit</h6>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">Update records anytime</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="feature-card">
                            <div class="feature-icon" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                🔒
                            </div>
                            <h6 style="font-weight: 600; font-size: 0.9rem;">Secure</h6>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">Data protection</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="feature-card">
                            <div class="feature-icon" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                ⚡
                            </div>
                            <h6 style="font-weight: 600; font-size: 0.9rem;">Fast</h6>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">Quick operations</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="feature-card">
                            <div class="feature-icon" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                📱
                            </div>
                            <h6 style="font-weight: 600; font-size: 0.9rem;">Responsive</h6>
                            <p class="text-muted mb-0" style="font-size: 0.8rem;">Works anywhere</p>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-3" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.05) 0%, rgba(11, 94, 215, 0.05) 100%);">
                    <div class="card-body p-3 text-center">
                        <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                            💡 <strong>Tip:</strong> All fields are required to add a student
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <p class="text-muted" style="font-size: 0.9rem;">
            Student Management System © 2025 | Built with ❤️ and PHP
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // TIER 1 JAVASCRIPT LOGIC - Communicates with Tier 2 API
        const API_URL = '<?php echo getenv("API_URL") ?: "http://localhost:8081/api/students.php"; ?>';
        const form = document.getElementById('studentForm');
        const submitBtn = document.getElementById('submit-btn');
        const apiResponseAlert = document.getElementById('api-response-alert');
        const responseMessageSpan = document.getElementById('response-message');
        const countBadge = document.getElementById('student-count-badge');

        function showAlert(message, isSuccess = false) {
            responseMessageSpan.innerHTML = message;
            apiResponseAlert.classList.remove('alert-success', 'alert-danger');
            apiResponseAlert.classList.add(isSuccess ? 'alert-success' : 'alert-danger');
            apiResponseAlert.style.display = 'block';

            // Auto-hide the alert after 5 seconds
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(apiResponseAlert);
                bsAlert.close();
            }, 5000);
        }

        function hideAlert() {
            apiResponseAlert.style.display = 'none';
        }

        async function fetchStudentCount() {
            try {
                const response = await fetch(API_URL);
                const data = await response.json();
                if (response.ok && data.status === 'success') {
                    countBadge.innerHTML = `📊 ${data.total} Student${data.total != 1 ? 's' : ''} Registered`;
                } else {
                    countBadge.innerHTML = '📊 API Error';
                }
            } catch (error) {
                console.error('Count Fetch Error:', error);
                countBadge.innerHTML = '📊 Connection Failed';
            }
        }

        // Handle Form Submission via AJAX/Fetch
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            hideAlert();

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            // Get form data
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            data.age = parseInt(data.age); // Convert age to integer

            // Show loading state
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding Student...';
            submitBtn.disabled = true;

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const responseData = await response.json();

                if (response.ok && responseData.status === 'inserted') {
                    // Success: Display success message on the current page
                    showAlert(`<strong>Success!</strong> Student record successfully added! 🎉`, true);
                    form.reset(); // Clear the form
                    form.classList.remove('was-validated'); // Remove validation styles
                    fetchStudentCount(); // Update the count badge
                } else {
                    // API returned a 400 or 500 error
                    const message = responseData.message || 'An unknown error occurred on the server.';
                    showAlert(`<strong>Error:</strong> ${message}`, false);
                }
            } catch (error) {
                console.error('Network Error:', error);
                // On critical network error, fallback to redirect to display PHP error
                window.location.href = `index.php?status=api_error`; 
            } finally {
                // Reset button state
                submitBtn.innerHTML = '<span style="font-size: 1.1rem;">✓ Add Student Record</span>';
                submitBtn.disabled = false;
            }
        });

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

        // Form Validation (Bootstrap)
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
        
        // Auto-focus and fetch count on load
        window.addEventListener('load', () => {
            document.getElementById('nameInput').focus();
            fetchStudentCount();
        });

        // Add input animations
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
    
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        .shake {
            animation: shake 0.5s;
        }
        
        .form-floating {
            transition: transform 0.2s ease;
        }
    </style>
</body>
</html>