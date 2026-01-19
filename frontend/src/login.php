<?php
session_start();

// If already logged in, redirect to view page
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header('Location: view.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Student Hub</title>
    <link rel="icon" type="image/png" href="images/icon.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial Black', 'Arial Bold', Gadget, sans-serif;
            background-image: url('images/background-image.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border: 4px solid #000;
            box-shadow: 12px 12px 0px #000;
            padding: 40px;
            max-width: 450px;
            width: 100%;
            position: relative;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 48px;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 32px;
            color: #000;
            margin-bottom: 10px;
            font-family: 'Cooper Black', 'Arial Black', sans-serif;
        }

        .subtitle {
            color: #666;
            font-size: 14px;
            font-family: 'Courier New', monospace;
            font-weight: normal;
        }

        .error-message {
            background: #FF90E8;
            border: 3px solid #000;
            padding: 15px;
            margin-bottom: 20px;
            display: none;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }

        .error-message.show {
            display: block;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 14px;
            color: #000;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 15px;
            border: 3px solid #000;
            font-size: 16px;
            font-family: 'Courier New', monospace;
            background: #fff;
            transition: all 0.2s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            box-shadow: 6px 6px 0px #FFC900;
            transform: translate(-3px, -3px);
        }

        .btn-login {
            width: 100%;
            padding: 18px;
            background: #FFC900;
            border: 4px solid #000;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Arial Black', sans-serif;
            text-transform: uppercase;
        }

        .btn-login:hover {
            box-shadow: 8px 8px 0px #000;
            transform: translate(-4px, -4px);
        }

        .btn-login:active {
            box-shadow: 4px 4px 0px #000;
            transform: translate(-2px, -2px);
        }

        .btn-login:disabled {
            background: #ccc;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .admin-help {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 3px dashed #000;
            text-align: center;
        }

        .help-title {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }

        .cli-command {
            background: #000;
            color: #00ff00;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            border: 3px solid #000;
            text-align: left;
            overflow-x: auto;
        }

        .security-badge {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: #FF90E8;
            border: 2px solid #000;
            padding: 5px 10px;
            font-size: 10px;
            font-weight: bold;
        }

        @media (max-width: 500px) {
            .login-container {
                padding: 30px 20px;
                box-shadow: 8px 8px 0px #000;
            }

            h1 {
                font-size: 24px;
            }

            .logo {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <div class="logo">🎓</div>
            <h1>STUDENT HUB</h1>
            <p class="subtitle">Admin Access Portal</p>
        </div>

        <div id="errorMessage" class="error-message">
            <span id="errorText"></span>
        </div>

        <form id="loginForm" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="admin@admin.com"
                    required
                    autocomplete="username"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                LOGIN
            </button>
        </form>

        <!-- <div class="admin-help">
            <p class="help-title">ℹ️ Need an admin account?</p>
            <div class="cli-command">
                CLI: docker exec -it 3tier-backend php create_admin.php
            </div>
        </div> -->

        <!-- <div class="security-badge">
            🛡️ Secure Authentication System
        </div> -->
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const loginBtn = document.getElementById('loginBtn');
            const errorMessage = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');
            
            // Disable button and show loading
            loginBtn.disabled = true;
            loginBtn.textContent = '⚡ LOGGING IN...';
            errorMessage.classList.remove('show');
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            try {
                const response = await fetch('/api/auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify({ username, password })
                });
                
                const data = await response.json();
                console.log('Login response:', response.status, data);
                
                if (response.ok && data.status === 'success') {
                    // Success - set session flag and redirect
                    console.log('Login successful, redirecting...');
                    
                    // Make a request to set frontend session
                    fetch('/set_session.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            username: data.user.username,
                            user_id: data.user.id
                        })
                    }).then(() => {
                        window.location.href = 'view.php';
                    }).catch(() => {
                        // Fallback - just redirect
                        window.location.href = 'view.php';
                    });
                } else {
                    // Show error message
                    console.log('Login failed:', data);
                    errorText.textContent = data.message || 'Login failed. Please try again.';
                    errorMessage.classList.add('show');
                }
            } catch (error) {
                console.error('Login error:', error);
                errorText.textContent = 'Network error. Please check your connection and try again.';
                errorMessage.classList.add('show');
            } finally {
                // Re-enable button
                loginBtn.disabled = false;
                loginBtn.textContent = '➜ LOGIN';
            }
        });
        
        // Focus on username field when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
    </script>

</body>
</html>