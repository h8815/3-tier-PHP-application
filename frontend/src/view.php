<?php
// TIER 1: PRESENTATION LAYER - VIEW
// Database logic removed, status messages remain.

$status_message = '';
$alert_class = '';

if (isset($_GET['status'])) {
    $status = htmlspecialchars($_GET['status']);
    if ($status === 'inserted') {
        $status_message = 'Student record successfully added! 🎉';
        $alert_class = 'alert-success';
    } elseif ($status === 'updated') {
        $status_message = 'Student record successfully updated! ✏️';
        $alert_class = 'alert-info';
    } elseif ($status === 'deleted') {
        $status_message = 'Student record successfully deleted. 🗑️';
        $alert_class = 'alert-warning';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Records - Management System</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="icon.png">
    <link href="style.css" rel="stylesheet">
</head>
<body class="container py-5">
    
    <?php if ($status_message): ?>
    <div class="alert <?= $alert_class; ?> alert-dismissible fade show mb-4" role="alert" id="api-status-alert">
        <strong>Success!</strong> <?= $status_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div>
            <h1 class="mb-1 text-primary">Student Records</h1>
            <p class="text-muted mb-0">Manage and view all student information</p>
        </div>
        <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
             <button id="theme-toggle" title="Toggle Light/Dark Mode">☀️</button>
             <a href="index.php" class="btn btn-primary shadow-sm">
                <span>➕</span> Add New Student
             </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body text-center py-4">
                    <h3 class="text-primary mb-1" style="font-size: 2.5rem; font-weight: 700;" id="total-students-count">0</h3>
                    <p class="text-muted mb-0" style="font-size: 0.95rem;">Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-3 mt-md-0">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body text-center py-4">
                    <h3 class="text-success mb-1" style="font-size: 2.5rem; font-weight: 700;">✓</h3>
                    <p class="text-muted mb-0" style="font-size: 0.95rem;">All Records Active</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-3 mt-md-0">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body text-center py-4">
                    <h3 class="text-info mb-1" style="font-size: 2.5rem; font-weight: 700;">📊</h3>
                    <p class="text-muted mb-0" style="font-size: 0.95rem;">API Status: <span id="api-status">Connecting...</span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-lg border-0">
        <div class="card-header">
             <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">📋 All Students</h4>
                <span class="badge bg-light text-dark" id="record-count-badge">0 Records</span>
             </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm m-0"> 
                    <thead class="table-primary">
                        <tr>
                            <th scope="col" class="ps-4">ID</th>
                            <th scope="col">👤 Name</th>
                            <th scope="col">📧 Email</th>
                            <th scope="col">🎂 Age</th>
                            <th scope="col" class="text-center pe-4">⚙️ Actions</th>
                        </tr>
                    </thead>
                    <tbody id="student-table-body">
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <span class="spinner-border text-primary"></span>
                                    <p class="mb-2 mt-2">Loading records from API...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light text-muted text-center py-3" style="border-radius: 0 0 16px 16px; border-top: 1px solid var(--border-color);">
            <small id="footer-count">Loading...</small>
        </div>
    </div>

    <div class="text-center mt-4">
        <p class="text-muted" style="font-size: 0.9rem;">
            Student Management System © 2025 | Built with ❤️
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // TIER 1 JAVASCRIPT LOGIC - Communicates with Tier 2 API
        const API_URL = '<?php echo getenv("API_URL") ?: "http://localhost:8081/api/students.php"; ?>';
        function buildRow(student) {
            return `
                <tr>
                    <td class="ps-4"><strong>${student.ID}</strong></td>
                    <td>${student.Name}</td>
                    <td><a href="mailto:${student.Email}" class="text-decoration-none">${student.Email}</a></td>
                    <td>${student.Age} years</td>
                    <td class="text-center pe-4">
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="edit.php?id=${student.ID}" class="btn btn-outline-primary" title="Edit Record">
                                ✏️ Edit
                            </a>
                            <button class="btn btn-outline-danger" title="Delete Record"
                               onclick="handleDelete(${student.ID}, '${student.Name.replace(/'/g, "\\'")}')">
                                🗑️ Delete
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        async function fetchStudents() {
            const tableBody = document.getElementById('student-table-body');
            try {
                const response = await fetch(API_URL);
                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    document.getElementById('api-status').textContent = 'OK';
                    document.getElementById('api-status').classList.remove('text-danger', 'text-warning');
                    document.getElementById('api-status').classList.add('text-success');

                    const students = data.data;
                    const totalStudents = data.total;

                    // Update all count and status elements
                    document.getElementById('total-students-count').textContent = totalStudents;
                    document.getElementById('record-count-badge').textContent = `${totalStudents} Records`;
                    document.getElementById('footer-count').textContent = `Showing all ${totalStudents} student record${totalStudents != 1 ? 's' : ''}`;

                    if (students.length > 0) {
                        // Render data from the API
                        tableBody.innerHTML = students.map(buildRow).join('');
                    } else {
                        // Empty state
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <h3 style="font-size: 3rem;">📭</h3>
                                        <p class="mb-2">No student records found.</p>
                                        <a href="index.php" class="btn btn-primary btn-sm mt-2">Add Your First Student</a>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }
                } else {
                    document.getElementById('api-status').textContent = 'ERROR';
                    document.getElementById('api-status').classList.remove('text-success', 'text-warning');
                    document.getElementById('api-status').classList.add('text-danger');
                    tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load data from API. Error: ${data.message || 'Check console.'}</td></tr>`;
                }

            } catch (error) {
                console.error('Fetch error:', error);
                document.getElementById('api-status').textContent = 'FAILURE';
                document.getElementById('api-status').classList.remove('text-success', 'text-warning');
                document.getElementById('api-status').classList.add('text-danger');
                tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Network Error: Could not reach the API. Check server status.</td></tr>`;
            }
        }

        async function handleDelete(id, name) {
            if (confirm(`⚠️ Are you sure you want to delete this record?\n\nStudent: ${name}\nThis action cannot be undone.`)) {
                try {
                    const response = await fetch(API_URL, {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: id })
                    });
                    const data = await response.json();
                    
                    if (response.ok && data.status === 'deleted') {
                        // Redirect with status to trigger alert on refresh
                        window.location.href = `view.php?status=deleted`;
                    } else {
                        alert(`Deletion failed. Error: ${data.message || 'Unknown error.'}`);
                    }
                } catch (error) {
                    alert('Network error during deletion.');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchStudents();
            
            // Standard UI/Theme Toggle logic...
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

            setTimeout(() => {
                const alertElement = document.getElementById('api-status-alert');
                if(alertElement) {
                    const bsAlert = new bootstrap.Alert(alertElement);
                    bsAlert.close();
                }
            }, 5000);

            document.body.style.opacity = '0';
            setTimeout(() => {
                document.body.style.transition = 'opacity 0.3s';
                document.body.style.opacity = '1';
            }, 50);
        });
    </script>
</body>
</html>