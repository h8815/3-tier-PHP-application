<?php
// Landing page - redirects to login if not authenticated
session_start();

// Check if user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

// If authenticated, redirect to dashboard
header('Location: view.php');
exit;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Student - Neo Hub</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="favicon.ico">
    <link href="style.css" rel="stylesheet">
</head>
<body style="padding: 24px;">
    
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">ADD STUDENT</h1>
            <p class="page-subtitle">Create a new student profile</p>
        </div>
        <div style="display: flex; gap: 16px;">
            <?php if ($isAuthenticated): ?>
                <a href="view.php" class="btn-brutal blue">← BACK TO DASHBOARD</a>
            <?php else: ?>
                <a href="login.php" class="btn-brutal">LOGIN</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="message-container" style="display: none; margin-bottom: 24px;">
        <div id="message" class="message success"></div>
    </div>

    <!-- Add Student Form -->
    <div class="brutal-card" style="padding: 40px; max-width: 800px; margin: 0 auto;">
        <form id="student-form" enctype="multipart/form-data">
            
            <!-- Profile Photo Upload -->
            <div style="margin-bottom: 32px;">
                <label class="form-label">Profile Photo</label>
                <div class="drag-drop-area" id="drop-area">
                    <div id="drop-content">
                        <div style="font-size: 3rem; margin-bottom: 16px;">📸</div>
                        <p style="margin: 0 0 8px 0; font-family: var(--font-display); font-size: 1.2rem;">DRAG & DROP YOUR PHOTO</p>
                        <p style="margin: 0 0 16px 0; font-family: var(--font-mono); font-size: 0.9rem;">or click to browse files</p>
                        <button type="button" class="btn-brutal" onclick="document.getElementById('photo-input').click()">CHOOSE FILE</button>
                    </div>
                    <div id="preview-container" style="display: none;">
                        <img id="photo-preview" style="max-width: 200px; max-height: 200px; border: 3px solid var(--ink-black); object-fit: cover;">
                        <p id="file-name" style="margin: 12px 0 0 0; font-family: var(--font-mono); font-weight: 500;"></p>
                        <button type="button" class="btn-brutal danger" style="margin-top: 12px;" onclick="removePhoto()">REMOVE</button>
                    </div>
                </div>
                <input type="file" id="photo-input" name="profile_photo" accept="image/*" style="display: none;">
            </div>

            <!-- Personal Information -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" id="name" class="brutal-input" placeholder="Enter full name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" id="email" class="brutal-input" placeholder="Enter email address" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; margin-bottom: 24px;">
                <div class="form-group">
                    <label class="form-label">Age *</label>
                    <input type="number" name="age" id="age" class="brutal-input" placeholder="Enter age" min="10" max="100" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" id="phone" class="brutal-input" placeholder="Enter phone number">
                </div>
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" id="status" class="brutal-input" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="graduated">Graduated</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 32px;">
                <label class="form-label">Address</label>
                <textarea name="address" id="address" class="brutal-input" rows="3" placeholder="Enter address" style="resize: vertical; min-height: 80px;"></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-brutal" style="width: 100%; font-size: 1.2rem; padding: 18px;">
                <span id="submit-text">🚀 CREATE STUDENT PROFILE</span>
            </button>
        </form>
    </div>

    <!-- Statistics -->
    <div style="margin-top: 48px; text-align: center;">
        <div class="brutal-card" style="padding: 24px; display: inline-block; min-width: 200px;">
            <div style="font-family: var(--font-display); font-size: 2.5rem; color: var(--ink-black); margin-bottom: 8px; text-shadow: 2px 2px 0px var(--pop-pink);" id="student-count">0</div>
            <div style="font-family: var(--font-mono); font-weight: 700; text-transform: uppercase;">Students Registered</div>
        </div>
    </div>

    <script>
        // Get API URL from config endpoint
        let API_URL = '/api/students.php';
        let CONFIG_LOADED = false;
        
        // Load configuration
        fetch('/api/config.php')
            .then(response => response.json())
            .then(config => {
                API_URL = config.studentsUrl;
                CONFIG_LOADED = true;
                console.log('API URL configured:', API_URL);
                fetchStudentCount();
            })
            .catch(error => {
                console.error('Failed to load config, using default:', error);
                CONFIG_LOADED = true;
                fetchStudentCount();
            });
        
        // Drag and drop functionality
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
            dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
        });

        dropArea.addEventListener('drop', handleDrop, false);
        dropArea.addEventListener('click', () => photoInput.click());

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        photoInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith('image/')) {
                    if (file.size > 5242880) { // 5MB limit
                        showMessage('File size must be less than 5MB', 'error');
                        return;
                    }
                    displayPreview(file);
                } else {
                    showMessage('Please select a valid image file', 'error');
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

        // Form submission
        document.getElementById('student-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!CONFIG_LOADED) {
                showMessage('Please wait for configuration to load...', 'error');
                return;
            }
            
            const submitText = document.getElementById('submit-text');
            const originalText = submitText.textContent;
            submitText.textContent = '⚡ CREATING PROFILE...';
            
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok && data.status === 'inserted') {
                    showMessage('Student profile created successfully! 🎉', 'success');
                    e.target.reset();
                    removePhoto();
                    fetchStudentCount();
                } else {
                    showMessage(data.message || 'Failed to create profile', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Network error. Please try again.', 'error');
            } finally {
                submitText.textContent = originalText;
            }
        });

        function showMessage(text, type) {
            const container = document.getElementById('message-container');
            const message = document.getElementById('message');
            
            message.textContent = text;
            message.className = `message ${type}`;
            container.style.display = 'block';
            
            setTimeout(() => {
                container.style.display = 'none';
            }, 5000);
        }

        async function fetchStudentCount() {
            if (!CONFIG_LOADED) return;
            
            try {
                const response = await fetch(API_URL);
                const data = await response.json();
                if (response.ok && data.status === 'success') {
                    document.getElementById('student-count').textContent = data.pagination.total;
                }
            } catch (error) {
                console.error('Failed to fetch student count:', error);
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('name').focus();
        });
    </script>
</body>
</html>