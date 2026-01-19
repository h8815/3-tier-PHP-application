<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <title>Student Dashboard - Student Hub</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Toast CSS -->
    <link href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css" rel="stylesheet">
    <style>
        /* Dark/Light Mode & Mobile Responsiveness */
        [data-bs-theme="dark"] {
            --bs-body-bg: #1a1a1a;
            --bs-body-color: #ffffff;
        }
        
        [data-bs-theme="dark"] .stats-card,
        [data-bs-theme="dark"] .student-card,
        [data-bs-theme="dark"] .search-box {
            background: #2d2d2d !important;
            color: #ffffff;
        }
        
        [data-bs-theme="dark"] .top-nav {
            background: #2d2d2d !important;
        }
        
        /* Loading Animations */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Bulk Actions */
        .bulk-actions {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            display: none;
        }
        
        [data-bs-theme="dark"] .bulk-actions {
            background: #3d3d00;
            border-color: #666600;
        }
        
        .student-checkbox {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 10;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .stats-card {
                margin-bottom: 15px;
            }
            
            .search-box .row {
                gap: 10px;
            }
            
            .search-box .col-md-4,
            .search-box .col-md-2 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .student-card {
                margin-bottom: 15px;
            }
            
            .nav-brand {
                font-size: 1.2rem;
            }
            
            .top-nav .d-flex {
                flex-direction: column;
                gap: 10px;
            }
        }
        
        @media (max-width: 576px) {
            .container-fluid {
                padding: 10px;
            }
            
            .stats-number {
                font-size: 2rem;
            }
            
            .student-card {
                padding: 15px;
            }
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
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border: none;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .stats-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 10px;
        }
        
        .stats-label {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .student-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border: none;
            height: 100%;
        }
        
        .student-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .student-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #f0f0f0;
        }
        
        .student-avatar-default {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: white;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .badge-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-active {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .badge-graduated {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .search-box {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        .pagination {
            margin-top: 30px;
        }
        
        .page-link {
            border-radius: 8px;
            margin: 0 5px;
            color: #667eea;
            border: 2px solid #e9ecef;
        }
        
        .page-link:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }
    </style>
</head>
<body>
    
    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center">
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
                    <a href="add-student.php" class="btn btn-primary shadow-sm btn-sm">
                        <i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Add Student</span>
                    </a>
                    <a href="import-export.php" class="btn btn-outline-success shadow-sm btn-sm">
                        <i class="bi bi-file-earmark-arrow-up"></i> <span class="d-none d-md-inline">Import/Export</span>
                    </a>
                    <button id="logout-btn" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i> <span class="d-none d-md-inline">Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4">
        
        <!-- Bulk Actions Bar -->
        <div id="bulk-actions" class="bulk-actions">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span><strong id="selected-count">0</strong> students selected</span>
                <div class="btn-group" role="group">
                    <button id="select-all-btn" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-check-all"></i> Select All
                    </button>
                    <button id="deselect-all-btn" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Deselect All
                    </button>
                    <button id="bulk-delete-btn" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash"></i> Delete Selected
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-number text-primary" id="total-count">0</div>
                        <div class="stats-label"><i class="bi bi-people-fill"></i> Total Students</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-number text-success" id="active-count">0</div>
                        <div class="stats-label"><i class="bi bi-check-circle-fill"></i> Active</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-number text-warning" id="inactive-count">0</div>
                        <div class="stats-label"><i class="bi bi-pause-circle-fill"></i> Inactive</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-number text-info" id="graduated-count">0</div>
                        <div class="stats-label"><i class="bi bi-mortarboard-fill"></i> Graduated</div>
                    </div>
                </div>
        </div>

        <!-- Search & Filters -->
        <div class="search-box">
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="search-input" class="form-control" placeholder="Search students...">
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <select id="status-filter" class="form-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="graduated">Graduated</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <select id="sort-field" class="form-select">
                        <option value="Name">Sort by Name</option>
                        <option value="Age">Sort by Age</option>
                        <option value="enrollment_date">Sort by Date</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <button id="reset-btn" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i> <span class="d-none d-md-inline">Reset</span>
                    </button>
                </div>
                <div class="col-lg-2 col-md-6">
                    <button id="refresh-btn" class="btn btn-outline-primary w-100">
                        <i class="bi bi-arrow-repeat"></i> <span class="d-none d-md-inline">Refresh</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Student Cards Grid -->
        <div id="students-grid" class="row g-4">
            <!-- Loading shimmer cards -->
            <div class="col-md-4"><div class="student-card shimmer" style="height: 300px;"></div></div>
            <div class="col-md-4"><div class="student-card shimmer" style="height: 300px;"></div></div>
            <div class="col-md-4"><div class="student-card shimmer" style="height: 300px;"></div></div>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center" id="pagination"></ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toast Library -->
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script>
        // Initialize toast notifications
        const notyf = new Notyf({
            duration: 4000,
            position: { x: 'right', y: 'top' },
            dismissible: true,
            types: [
                {
                    type: 'success',
                    background: '#28a745',
                    icon: { className: 'bi bi-check-circle-fill', tagName: 'i' }
                },
                {
                    type: 'error',
                    background: '#dc3545',
                    icon: { className: 'bi bi-x-circle-fill', tagName: 'i' }
                }
            ]
        });
        
        // Loading overlay functions
        function showLoading() {
            const overlay = document.createElement('div');
            overlay.id = 'loading-overlay';
            overlay.className = 'loading-overlay';
            overlay.innerHTML = '<div class="spinner"></div>';
            document.body.appendChild(overlay);
        }
        
        function hideLoading() {
            const overlay = document.getElementById('loading-overlay');
            if (overlay) overlay.remove();
        }
        
        // Dark/Light mode toggle
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
        
        // Bulk actions functionality
        let selectedStudents = new Set();
        
        function updateBulkActions() {
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');
            
            selectedCount.textContent = selectedStudents.size;
            bulkActions.style.display = selectedStudents.size > 0 ? 'block' : 'none';
        }
        
        function toggleStudentSelection(id, checkbox) {
            if (checkbox.checked) {
                selectedStudents.add(id);
            } else {
                selectedStudents.delete(id);
            }
            updateBulkActions();
        }
        
        document.getElementById('select-all-btn').addEventListener('click', () => {
            document.querySelectorAll('.student-checkbox').forEach(cb => {
                cb.checked = true;
                selectedStudents.add(parseInt(cb.dataset.id));
            });
            updateBulkActions();
        });
        
        document.getElementById('deselect-all-btn').addEventListener('click', () => {
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
            selectedStudents.clear();
            updateBulkActions();
        });
        
        document.getElementById('bulk-delete-btn').addEventListener('click', async () => {
            if (selectedStudents.size === 0) return;
            
            if (!confirm(`Delete ${selectedStudents.size} selected students? This cannot be undone.`)) return;
            
            showLoading();
            
            try {
                const promises = Array.from(selectedStudents).map(id => 
                    fetch(API_URL, {
                        method: 'DELETE',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'include',
                        body: JSON.stringify({ id })
                    })
                );
                
                await Promise.all(promises);
                selectedStudents.clear();
                updateBulkActions();
                fetchStudents();
                notyf.success('Selected students deleted successfully');
            } catch (error) {
                notyf.error('Error deleting students');
            } finally {
                hideLoading();
            }
        });
        let API_URL = '/api/students.php';
        let AUTH_URL = '/api/auth.php';
        let CONFIG_LOADED = false;
        
        fetch('/api/config.php')
            .then(response => response.json())
            .then(config => {
                API_URL = config.studentsUrl;
                AUTH_URL = config.authUrl;
                CONFIG_LOADED = true;
                fetchStudents();
            })
            .catch(error => {
                console.error('Failed to load config:', error);
                CONFIG_LOADED = true;
                fetchStudents();
            });
        
        let currentPage = 1;

        document.getElementById('logout-btn').addEventListener('click', async () => {
            try {
                // Clear frontend session
                await fetch('logout.php', { method: 'POST' });
                window.location.href = 'login.php';
            } catch (error) {
                // Fallback - just redirect
                window.location.href = 'login.php';
            }
        });

        async function fetchStudents() {
            if (!CONFIG_LOADED) {
                setTimeout(fetchStudents, 100);
                return;
            }

            showLoading();
            const params = new URLSearchParams({
                page: currentPage,
                limit: 12,
                search: document.getElementById('search-input').value,
                status: document.getElementById('status-filter').value,
                sort_by: document.getElementById('sort-field').value
            });

            try {
                const response = await fetch(`${API_URL}?${params}`, {
                    credentials: 'include',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    renderStudents(data.data);
                    renderPagination(data.pagination);
                    updateStats(data.statusCounts, data.pagination.total);
                    notyf.success('Students loaded successfully');
                } else {
                    notyf.error('Failed to load students');
                }
            } catch (error) {
                console.error('Fetch error:', error);
                notyf.error('Network error occurred');
            } finally {
                hideLoading();
            }
        }

        function renderStudents(students) {
            const grid = document.getElementById('students-grid');
            
            if (students.length === 0) {
                grid.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 5rem; color: #ccc;"></i>
                        <h3 class="mt-3">No Students Found</h3>
                        <p class="text-muted">Try adjusting your search criteria or add a new student.</p>
                        <a href="add-student.php" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle"></i> Add First Student
                        </a>
                    </div>
                `;
                return;
            }

            grid.innerHTML = students.map(student => {
                const statusClass = student.status === 'active' ? 'badge-active' : 
                                  student.status === 'inactive' ? 'badge-inactive' : 'badge-graduated';
                
                const avatarHtml = student.profile_photo 
                    ? `<img src="/${student.profile_photo}" alt="${student.Name}" class="student-avatar">`
                    : `<div class="student-avatar-default">${student.Name.charAt(0).toUpperCase()}</div>`;
                
                return `
                    <div class="col-lg-4 col-md-6">
                        <div class="student-card position-relative">
                            <input type="checkbox" class="form-check-input student-checkbox" 
                                   data-id="${student.ID}" 
                                   onchange="toggleStudentSelection(${student.ID}, this)">
                            
                            <div class="d-flex align-items-center mb-3">
                                ${avatarHtml}
                                <div class="ms-3 flex-grow-1">
                                    <h5 class="mb-1 fw-bold">${student.Name}</h5>
                                    <p class="text-muted mb-2" style="font-size: 0.9rem;">
                                        <i class="bi bi-envelope"></i> ${student.Email}
                                    </p>
                                    <span class="badge-status ${statusClass}">${student.status}</span>
                                </div>
                            </div>
                            
                            <div class="row g-2 mb-3" style="font-size: 0.9rem;">
                                <div class="col-6">
                                    <i class="bi bi-calendar-event text-primary"></i> Age: <strong>${student.Age}</strong>
                                </div>
                                <div class="col-6">
                                    <i class="bi bi-telephone text-success"></i> ${student.phone || 'N/A'}
                                </div>
                            </div>
                            
                            ${student.address ? `<p class="text-muted small mb-3"><i class="bi bi-geo-alt"></i> ${student.address.length > 60 ? student.address.substring(0, 60) + '...' : student.address}</p>` : ''}
                            
                            <div class="d-grid gap-2">
                                <button onclick="editStudent(${student.ID})" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button onclick="deleteStudent(${student.ID}, '${student.Name.replace(/'/g, "\\'")}', this)" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderPagination(pagination) {
            const paginationEl = document.getElementById('pagination');
            const { page, pages } = pagination;
            
            if (pages <= 1) {
                paginationEl.innerHTML = '';
                return;
            }
            
            let html = '';
            
            if (page > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${page - 1}); return false;"><i class="bi bi-chevron-left"></i></a></li>`;
            }
            
            for (let i = Math.max(1, page - 2); i <= Math.min(pages, page + 2); i++) {
                html += `<li class="page-item ${i === page ? 'active' : ''}"><a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a></li>`;
            }
            
            if (page < pages) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${page + 1}); return false;"><i class="bi bi-chevron-right"></i></a></li>`;
            }
            
            paginationEl.innerHTML = html;
        }

        function updateStats(statusCounts, total) {
            document.getElementById('total-count').textContent = total;
            document.getElementById('active-count').textContent = statusCounts.active || 0;
            document.getElementById('inactive-count').textContent = statusCounts.inactive || 0;
            document.getElementById('graduated-count').textContent = statusCounts.graduated || 0;
        }

        function changePage(page) {
            currentPage = page;
            fetchStudents();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        async function deleteStudent(id, name, button) {
            if (!confirm(`Are you sure you want to delete ${name}? This action cannot be undone.`)) return;
            
            const originalText = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            button.disabled = true;
            
            try {
                const response = await fetch(API_URL, {
                    method: 'DELETE',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'include',
                    body: JSON.stringify({ id })
                });
                
                if (response.ok) {
                    fetchStudents();
                } else {
                    alert('Delete failed');
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            } catch (error) {
                alert('Network error');
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }

        function editStudent(id) {
            window.location.href = `edit.php?id=${id}`;
        }

        document.getElementById('refresh-btn').addEventListener('click', () => {
            fetchStudents();
        });

        function showError(message) {
            notyf.error(message);
        }

        document.getElementById('search-input').addEventListener('input', debounce(() => {
            currentPage = 1;
            fetchStudents();
        }, 500));

        document.getElementById('status-filter').addEventListener('change', () => {
            currentPage = 1;
            fetchStudents();
        });

        document.getElementById('sort-field').addEventListener('change', fetchStudents);

        document.getElementById('reset-btn').addEventListener('click', () => {
            document.getElementById('search-input').value = '';
            document.getElementById('status-filter').value = '';
            document.getElementById('sort-field').value = 'Name';
            currentPage = 1;
            fetchStudents();
        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    </script>
</body>
</html>