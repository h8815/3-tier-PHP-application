<?php
session_start();

// Check authentication
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: view.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <title id="pageTitle">Edit Student - Loading...</title>
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
        
        [data-bs-theme="dark"] .card, [data-bs-theme="dark"] .info-card {
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
        
        .edit-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .edit-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        
        .student-id-badge {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-radius: 25px;
            color: #667eea;
            font-weight: 600;
            font-size: 1rem;
            margin-top: 0.5rem;
            border: 2px solid #667eea;
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border: none;
        }
        
        .info-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn {
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .edit-title {
                font-size: 2rem;
            }
            
            .form-card {
                padding: 1.5rem;
            }
            
            .nav-brand {
                font-size: 1.2rem;
            }
        }
        
        #loading-state {
            text-align: center;
            padding: 5rem 0;
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
        <div class="edit-header">
            <h1 class="edit-title">✏️ Edit Student Record</h1>
            <p class="text-muted" style="font-size: 1.1rem;">Update student information</p>
            <div class="student-id-badge">
                🆔 Student ID: <span id="student-id-display"><?= htmlspecialchars($id); ?></span>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div id="loading-state">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
                    <p class="text-muted mt-3">Fetching student data...</p>
                </div>

                <div id="content-container" style="display: none;">
                    <div class="info-card">
                        <div class="d-flex align-items-center">
                            <div style="font-size: 2.5rem; margin-right: 1rem;">ℹ️</div>
                            <div>
                                <strong style="color: #667eea; font-size: 1.1rem;">Current Student:</strong>
                                <p class="mb-0 text-muted" style="font-size: 1rem;">
                                    <span id="current-name-display"></span> • <span id="current-email-display"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Photo Upload Section -->
                    <div class="card border-0 mb-4" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%); border-radius: 15px;">
                        <div class="card-body p-4">
                            <h5 class="mb-3"><i class="bi bi-camera"></i> Profile Photo</h5>
                            <div class="row align-items-center">
                                <div class="col-md-4 text-center">
                                    <div id="current-photo-container">
                                        <div id="photo-preview" style="width: 120px; height: 120px; margin: 0 auto; border-radius: 50%; border: 3px solid #667eea; display: flex; align-items: center; justify-content: center; background: #f8f9fa; overflow: hidden;">
                                            <span id="photo-placeholder" style="font-size: 3rem; color: #667eea;">📷</span>
                                            <img id="photo-img" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <input type="file" class="form-control" id="photo-input" accept="image/*" style="border-radius: 10px;">
                                        <div class="form-text">Choose a new photo (JPG, PNG, GIF - Max 5MB)</div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" id="upload-photo-btn" class="btn btn-outline-primary btn-sm" disabled>
                                            <i class="bi bi-upload"></i> Upload Photo
                                        </button>
                                        <button type="button" id="remove-photo-btn" class="btn btn-outline-danger btn-sm" style="display: none;">
                                            <i class="bi bi-trash"></i> Remove Photo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card form-card border-0">
                        <div class="card-body">
                            <div id="update-error-alert" class="alert alert-danger alert-dismissible fade show" role="alert" style="display:none;">
                                <strong>⚠️ Update Failed!</strong> <span id="update-error-message"></span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>

                            <form autocomplete="off" class="needs-validation" novalidate id="editForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" name="name" class="form-control" id="nameInput" 
                                                placeholder="Full Name" required minlength="3">
                                            <label for="nameInput">👤 Full Name</label>
                                            <div class="invalid-feedback">Name is required (minimum 3 characters).</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" name="email" class="form-control" id="emailInput" 
                                                placeholder="Email Address" required>
                                            <label for="emailInput">📧 Email Address</label>
                                            <div class="invalid-feedback">A valid email address is required.</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="number" name="age" class="form-control" id="ageInput" 
                                                placeholder="Age" required min="10" max="100">
                                            <label for="ageInput">🎂 Age</label>
                                            <div class="invalid-feedback">Age must be between 10 and 100.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="tel" name="phone" class="form-control" id="phoneInput" 
                                                placeholder="Phone">
                                            <label for="phoneInput">📱 Phone Number</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <select name="status" class="form-select" id="statusInput" required>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="graduated">Graduated</option>
                                            </select>
                                            <label for="statusInput">📊 Status</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-floating">
                                    <textarea name="address" class="form-control" id="addressInput" 
                                        placeholder="Address" style="height: 120px;"></textarea>
                                    <label for="addressInput">🏠 Address</label>
                                </div>
                                
                                <div class="d-grid gap-3 mt-4">
                                    <button type="submit" id="save-btn" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check-circle"></i> Save Changes
                                    </button>
                                    
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-outline-secondary w-100" onclick="resetToOriginal()">
                                                <i class="bi bi-arrow-clockwise"></i> Reset to Original
                                            </button>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-outline-danger w-100"
                                               onclick="handleDeleteOnEditPage(<?= htmlspecialchars($id); ?>, document.getElementById('nameInput').value)">
                                                <i class="bi bi-trash"></i> Delete Student
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
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

        let API_URL = '/api/students.php';
        let CONFIG_LOADED = false;
        const STUDENT_ID = <?= json_encode(htmlspecialchars($id)); ?>;

        fetch('/api/config.php')
            .then(response => response.json())
            .then(config => {
                API_URL = config.studentsUrl;
                CONFIG_LOADED = true;
                loadStudent();
            })
            .catch(error => {
                CONFIG_LOADED = true;
                loadStudent();
            });

        let originalValues = {};
        let currentPhotoPath = null;

        const nameInput = document.getElementById('nameInput');
        const emailInput = document.getElementById('emailInput');
        const ageInput = document.getElementById('ageInput');
        const phoneInput = document.getElementById('phoneInput');
        const addressInput = document.getElementById('addressInput');
        const statusInput = document.getElementById('statusInput');
        const editForm = document.getElementById('editForm');
        const saveBtn = document.getElementById('save-btn');
        const updateErrorAlert = document.getElementById('update-error-alert');
        const updateErrorMessage = document.getElementById('update-error-message');
        const loadingState = document.getElementById('loading-state');
        const contentContainer = document.getElementById('content-container');
        const pageTitle = document.getElementById('pageTitle');
        const currentNameDisplay = document.getElementById('current-name-display');
        const currentEmailDisplay = document.getElementById('current-email-display');
        
        // Photo elements
        const photoInput = document.getElementById('photo-input');
        const photoPreview = document.getElementById('photo-preview');
        const photoImg = document.getElementById('photo-img');
        const photoPlaceholder = document.getElementById('photo-placeholder');
        const uploadPhotoBtn = document.getElementById('upload-photo-btn');
        const removePhotoBtn = document.getElementById('remove-photo-btn');
        
        // Photo event listeners
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5242880) {
                    notyf.error('File size must be less than 5MB');
                    photoInput.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoImg.src = e.target.result;
                    photoImg.style.display = 'block';
                    photoPlaceholder.style.display = 'none';
                    uploadPhotoBtn.disabled = false;
                };
                reader.readAsDataURL(file);
            }
        });
        
        uploadPhotoBtn.addEventListener('click', async function() {
            const file = photoInput.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('profile_photo', file);
            formData.append('student_id', STUDENT_ID);
            
            uploadPhotoBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Uploading...';
            uploadPhotoBtn.disabled = true;
            
            try {
                const response = await fetch('/api/upload-photo.php', {
                    method: 'POST',
                    credentials: 'include',
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok && data.status === 'success') {
                    currentPhotoPath = data.photo_path;
                    removePhotoBtn.style.display = 'inline-block';
                    notyf.success('Photo uploaded successfully!');
                } else {
                    notyf.error(data.message || 'Photo upload failed');
                }
            } catch (error) {
                notyf.error('Network error during photo upload');
            } finally {
                uploadPhotoBtn.innerHTML = '<i class="bi bi-upload"></i> Upload Photo';
                uploadPhotoBtn.disabled = false;
            }
        });
        
        removePhotoBtn.addEventListener('click', async function() {
            if (!confirm('Are you sure you want to remove the profile photo?')) return;
            
            removePhotoBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Removing...';
            removePhotoBtn.disabled = true;
            
            try {
                const response = await fetch('/api/remove-photo.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({ student_id: STUDENT_ID })
                });
                
                const data = await response.json();
                
                if (response.ok && data.status === 'success') {
                    photoImg.style.display = 'none';
                    photoPlaceholder.style.display = 'block';
                    removePhotoBtn.style.display = 'none';
                    currentPhotoPath = null;
                    photoInput.value = '';
                    notyf.success('Photo removed successfully!');
                } else {
                    notyf.error(data.message || 'Photo removal failed');
                }
            } catch (error) {
                notyf.error('Network error during photo removal');
            } finally {
                removePhotoBtn.innerHTML = '<i class="bi bi-trash"></i> Remove Photo';
                removePhotoBtn.disabled = false;
            }
        });

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
            phoneInput.value = originalValues.phone || '';
            addressInput.value = originalValues.address || '';
            statusInput.value = originalValues.status || 'active';
            editForm.classList.remove('was-validated');
            hideError(); 
        }

        async function loadStudent() {
            if (!CONFIG_LOADED) {
                setTimeout(loadStudent, 100);
                return;
            }

            try {
                const response = await fetch(`${API_URL}?id=${STUDENT_ID}`, {
                    credentials: 'include'
                });
                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    const student = data.data;
                    
                    originalValues = {
                        Name: student.Name,
                        Email: student.Email,
                        Age: student.Age,
                        phone: student.phone,
                        address: student.address,
                        status: student.status
                    };

                    nameInput.value = student.Name;
                    emailInput.value = student.Email;
                    ageInput.value = student.Age;
                    phoneInput.value = student.phone || '';
                    addressInput.value = student.address || '';
                    statusInput.value = student.status || 'active';

                    pageTitle.textContent = `Edit Student - ${student.Name}`;
                    currentNameDisplay.textContent = student.Name;
                    currentEmailDisplay.textContent = student.Email;
                    
                    // Handle photo display
                    if (student.profile_photo) {
                        currentPhotoPath = student.profile_photo;
                        photoImg.src = `/${student.profile_photo}`;
                        photoImg.style.display = 'block';
                        photoPlaceholder.style.display = 'none';
                        removePhotoBtn.style.display = 'inline-block';
                    } else {
                        photoImg.style.display = 'none';
                        photoPlaceholder.style.display = 'block';
                        removePhotoBtn.style.display = 'none';
                    }

                    loadingState.style.display = 'none';
                    contentContainer.style.display = 'block';

                } else if (response.status === 404) {
                    notyf.error('Student not found');
                    window.location.href = 'view.php';
                } else {
                    throw new Error(data.message || 'API error during fetch.');
                }
            } catch (error) {
                notyf.error('Network error occurred');
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

            const formData = new FormData(editForm);
            const data = Object.fromEntries(formData);
            data.id = parseInt(STUDENT_ID);
            data.age = parseInt(data.age);

            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            saveBtn.disabled = true;

            try {
                const response = await fetch(API_URL, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify(data)
                });
                const responseData = await response.json();

                if (response.ok && responseData.status === 'updated') {
                    notyf.success('Student updated successfully!');
                    setTimeout(() => {
                        window.location.href = 'view.php';
                    }, 1000);
                } else {
                    const message = responseData.message || 'An unknown error occurred on the server.';
                    showError(message);
                }
            } catch (error) {
                showError('A network error prevented the update. Check server status.');
            } finally {
                saveBtn.innerHTML = '<i class="bi bi-check-circle"></i> Save Changes';
                saveBtn.disabled = false;
            }
        });

        function handleDeleteOnEditPage(id, name) {
            if (confirm(`⚠️ Are you sure you want to delete this record?\n\nStudent: ${name}\nThis action cannot be undone.`)) {
                fetch(API_URL, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'deleted') {
                        notyf.success('Student deleted successfully!');
                        setTimeout(() => {
                            window.location.href = 'view.php';
                        }, 1000);
                    } else {
                        notyf.error(`Deletion failed: ${data.message || 'Unknown error.'}`);
                    }
                })
                .catch(error => notyf.error('Network error during deletion.'));
            }
        }

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
            notyf.success(`Switched to ${theme} mode`);
        });
        
        function updateThemeIcon(theme) {
            const icon = themeToggle.querySelector('i');
            icon.className = theme === 'light' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
        }
        
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
    </script>
</body>
</html>