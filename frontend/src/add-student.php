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
    <title>Add Student - Student Hub</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body style="padding: 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">
    
    <!-- Header -->
    <div class="page-header" style="background: white; border-radius: 15px; padding: 24px; margin-bottom: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
        <div>
            <h1 class="page-title" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-size: 2.5rem; font-weight: 800;">ADD NEW STUDENT</h1>
            <p class="page-subtitle" style="color: #6c757d;">Create a new student profile</p>
        </div>
        <div style="display: flex; gap: 16px;">
            <span style="font-family: var(--font-mono); font-size: 14px; display: flex; align-items: center;">Welcome, <strong style="margin-left: 5px;"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></strong></span>
            <a href="view.php" class="btn btn-primary shadow-sm">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="message-container" style="display: none; margin-bottom: 24px;">
        <div id="message" class="alert"></div>
    </div>

    <!-- Add Student Form -->
    <div class="card shadow-lg border-0" style="max-width: 900px; margin: 0 auto; border-radius: 15px; overflow: hidden;">
        <div class="card-body p-5">
            <form id="student-form" enctype="multipart/form-data" class="needs-validation" novalidate>
                
                <!-- Profile Photo Upload -->
                <div style="margin-bottom: 32px; text-align: center;">
                    <label class="form-label fw-bold">Profile Photo</label>
                    <div class="drag-drop-area" id="drop-area" style="border: 3px dashed #667eea; border-radius: 15px; padding: 48px 24px; cursor: pointer; transition: all 0.3s;">
                        <div id="drop-content">
                            <div style="font-size: 4rem; margin-bottom: 16px;">📸</div>
                            <p style="margin: 0 0 8px 0; font-weight: 600; font-size: 1.2rem;">Drag & Drop Your Photo</p>
                            <p style="margin: 0 0 16px 0; color: #6c757d;">or click to browse files</p>
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('photo-input').click()">Choose File</button>
                        </div>
                        <div id="preview-container" style="display: none;">
                            <img id="photo-preview" style="max-width: 200px; max-height: 200px; border: 3px solid #667eea; border-radius: 15px; object-fit: cover;">
                            <p id="file-name" style="margin: 12px 0 0 0; font-weight: 500;"></p>
                            <button type="button" class="btn btn-danger mt-3" onclick="removePhoto()">Remove</button>
                        </div>
                    </div>
                    <input type="file" id="photo-input" name="profile_photo" accept="image/*" style="display: none;">
                </div>

                <!-- Personal Information -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Full Name" required minlength="3">
                            <label for="name">Full Name *</label>
                            <div class="invalid-feedback">Name is required (minimum 3 characters).</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                            <label for="email">Email Address *</label>
                            <div class="invalid-feedback">A valid email is required.</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" name="age" id="age" class="form-control" placeholder="Age" min="10" max="100" required>
                            <label for="age">Age *</label>
                            <div class="invalid-feedback">Age must be between 10 and 100.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="tel" name="phone" id="phone" class="form-control" placeholder="Phone">
                            <label for="phone">Phone Number</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select name="status" id="status" class="form-select" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="graduated">Graduated</option>
                            </select>
                            <label for="status">Status *</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating mb-4">
                    <textarea name="address" id="address" class="form-control" placeholder="Address" style="height: 100px; resize: vertical;"></textarea>
                    <label for="address">Address</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 15px;">
                    <span id="submit-text" style="font-size: 1.1rem;">🚀 Create Student Profile</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div style="margin-top: 48px; text-align: center;">
        <div class="card shadow-lg border-0" style="display: inline-block; min-width: 250px; border-radius: 15px; padding: 24px; background: white;">
            <div style="font-size: 3rem; font-weight: 800; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 8px;" id="student-count">0</div>
            <div style="font-weight: 600; text-transform: uppercase; color: #6c757d;">Students Registered</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let API_URL = '/api/students.php';
        let CONFIG_LOADED = false;
        
        fetch('/api/config.php')
            .then(response => response.json())
            .then(config => {
                API_URL = config.studentsUrl;
                CONFIG_LOADED = true;
                fetchStudentCount();
            })
            .catch(error => {
                console.error('Failed to load config:', error);
                CONFIG_LOADED = true;
                fetchStudentCount();
            });
        
        // Drag and drop
        const dropArea = document.getElementById('drop-area');
        const photoInput = document.getElementById('photo-input');
        const dropContent = document.getElementById('drop-content');
        const previewContainer = document.getElementById('preview-container');
        const photoPreview = document.getElementById('photo-preview');
        const fileName = document.getElementById('file-name');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => {
                dropArea.style.borderColor = '#764ba2';
                dropArea.style.background = 'rgba(102, 126, 234, 0.05)';
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => {
                dropArea.style.borderColor = '#667eea';
                dropArea.style.background = 'transparent';
            });
        });

        dropArea.addEventListener('drop', handleDrop);
        dropArea.addEventListener('click', () => photoInput.click());

        function handleDrop(e) {
            handleFiles(e.dataTransfer.files);
        }

        photoInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith('image/')) {
                    if (file.size > 5242880) {
                        showMessage('File size must be less than 5MB', 'danger');
                        return;
                    }
                    displayPreview(file);
                } else {
                    showMessage('Please select a valid image file', 'danger');
                }
            }
        }

        function displayPreview(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                photoPreview.src = e.target.result;
                fileName.textContent = file.name;
                dropContent.style.display = 'none';
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }

        function removePhoto() {
            photoInput.value = '';
            dropContent.style.display = 'block';
            previewContainer.style.display = 'none';
        }

        document.getElementById('student-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!e.target.checkValidity()) {
                e.target.classList.add('was-validated');
                return;
            }
            
            if (!CONFIG_LOADED) {
                showMessage('Please wait for configuration to load...', 'warning');
                return;
            }
            
            const submitText = document.getElementById('submit-text');
            const originalText = submitText.textContent;
            submitText.textContent = '⚡ Creating Profile...';
            
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    credentials: 'include',
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok && data.status === 'inserted') {
                    showMessage('Student profile created successfully! 🎉', 'success');
                    e.target.reset();
                    e.target.classList.remove('was-validated');
                    removePhoto();
                    fetchStudentCount();
                    
                    // Redirect to view page after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'view.php';
                    }, 2000);
                } else {
                    showMessage(data.message || 'Failed to create profile', 'danger');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Network error. Please try again.', 'danger');
            } finally {
                submitText.textContent = originalText;
            }
        });

        function showMessage(text, type) {
            const container = document.getElementById('message-container');
            const message = document.getElementById('message');
            
            message.textContent = text;
            message.className = `alert alert-${type}`;
            container.style.display = 'block';
            
            setTimeout(() => {
                container.style.display = 'none';
            }, 5000);
        }

        async function fetchStudentCount() {
            if (!CONFIG_LOADED) return;
            
            try {
                const response = await fetch(API_URL, {
                    credentials: 'include'
                });
                const data = await response.json();
                if (response.ok && data.status === 'success') {
                    document.getElementById('student-count').textContent = data.pagination.total;
                }
            } catch (error) {
                console.error('Failed to fetch student count:', error);
            }
        }

        // Form validation
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

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('name').focus();
        });
    </script>
</body>
</html>