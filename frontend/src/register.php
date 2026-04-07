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
    <title>Create Account - Student Hub</title>
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

        .register-container {
            background: white;
            border: 4px solid #000;
            box-shadow: 12px 12px 0px #000;
            padding: 40px;
            max-width: 500px;
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
            font-size: 13px;
        }

        .error-message.show {
            display: block;
        }

        .success-message {
            background: #90EE90;
            border: 3px solid #000;
            padding: 15px;
            margin-bottom: 20px;
            display: none;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #000;
        }

        .success-message.show {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 14px;
            color: #000;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 15px;
            border: 3px solid #000;
            font-size: 16px;
            font-family: 'Courier New', monospace;
            background: #fff;
            transition: all 0.2s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
            outline: none;
            box-shadow: 6px 6px 0px #FFC900;
            transform: translate(-3px, -3px);
        }

        .password-requirements {
            background: #F0F0F0;
            border: 2px dashed #000;
            padding: 12px;
            margin-top: 8px;
            font-size: 12px;
            font-family: 'Courier New', monospace;
        }

        .requirement {
            margin: 5px 0;
            display: flex;
            align-items: center;
        }

        .requirement.met {
            color: #00aa00;
            font-weight: bold;
        }

        .requirement.unmet {
            color: #666;
        }

        .requirement-icon {
            margin-right: 8px;
            min-width: 15px;
            text-align: center;
        }

        .btn-register {
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
            margin-top: 20px;
        }

        .btn-register:hover {
            box-shadow: 8px 8px 0px #000;
            transform: translate(-4px, -4px);
        }

        .btn-register:active {
            box-shadow: 4px 4px 0px #000;
            transform: translate(-2px, -2px);
        }

        .btn-register:disabled {
            background: #ccc;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 3px dashed #000;
        }

        .login-link a {
            color: #0066cc;
            text-decoration: none;
            font-weight: bold;
            border-bottom: 2px solid #0066cc;
            transition: all 0.2s;
        }

        .login-link a:hover {
            background: #0066cc;
            color: white;
            padding: 2px 5px;
        }

        @media (max-width: 1024px) {
            .register-container {
                max-width: 90vw;
            }
        }

        @media (max-width: 768px) {
            .register-container {
                padding: 30px 25px;
                box-shadow: 8px 8px 0px #000;
                max-width: 95vw;
            }

            h1 {
                font-size: 28px;
            }

            .logo {
                font-size: 40px;
            }

            .form-group {
                margin-bottom: 18px;
            }

            input[type="text"],
            input[type="password"],
            input[type="email"] {
                padding: 14px;
                font-size: 16px;
            }

            .btn-register {
                padding: 15px;
                font-size: 16px;
                margin-top: 15px;
            }

            .password-requirements {
                font-size: 12px;
                padding: 10px;
                margin-top: 6px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .register-container {
                padding: 25px 20px;
                box-shadow: 6px 6px 0px #000;
                max-width: 100vw;
            }

            h1 {
                font-size: 22px;
            }

            .logo {
                font-size: 32px;
                margin-bottom: 8px;
            }

            .subtitle {
                font-size: 12px;
            }

            .logo-section {
                margin-bottom: 20px;
            }

            .form-group {
                margin-bottom: 12px;
            }

            label {
                font-size: 13px;
                margin-bottom: 6px;
            }

            input[type="text"],
            input[type="password"],
            input[type="email"] {
                padding: 12px;
                font-size: 16px;
                border-width: 2px;
            }

            .password-requirements {
                font-size: 11px;
                padding: 8px;
                margin-top: 4px;
            }

            .requirement {
                margin: 3px 0;
            }

            .requirement-icon {
                min-width: 12px;
                font-size: 12px;
            }

            .btn-register {
                padding: 12px;
                font-size: 13px;
                border-width: 3px;
                margin-top: 12px;
            }

            .login-link {
                margin-top: 12px;
                padding-top: 12px;
                font-size: 12px;
            }

            .login-link a {
                display: inline-block;
                word-break: break-word;
            }

            .error-message,
            .success-message {
                font-size: 12px;
                padding: 12px;
                margin-bottom: 15px;
            }
        }

        @media (max-width: 320px) {
            .register-container {
                padding: 20px 15px;
                box-shadow: 4px 4px 0px #000;
            }

            h1 {
                font-size: 20px;
            }

            input[type="text"],
            input[type="password"],
            input[type="email"] {
                padding: 10px;
                font-size: 14px;
            }

            .btn-register {
                padding: 10px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo-section">
            <div class="logo">🎓</div>
            <h1>STUDENT HUB</h1>
            <p class="subtitle">Create Your Account</p>
        </div>

        <div id="errorMessage" class="error-message">
            <span id="errorText"></span>
        </div>

        <div id="successMessage" class="success-message">
            <span id="successText"></span>
        </div>

        <form id="registerForm" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Enter your username"
                    required
                    minlength="3"
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
                    autocomplete="new-password"
                >
                <div class="password-requirements">
                    <div class="requirement unmet" id="req-length">
                        <span class="requirement-icon" id="icon-length">✗</span>
                        <span>At least 8 characters</span>
                    </div>
                    <div class="requirement unmet" id="req-upper">
                        <span class="requirement-icon" id="icon-upper">✗</span>
                        <span>At least 1 uppercase letter (A-Z)</span>
                    </div>
                    <div class="requirement unmet" id="req-lower">
                        <span class="requirement-icon" id="icon-lower">✗</span>
                        <span>At least 1 lowercase letter (a-z)</span>
                    </div>
                    <div class="requirement unmet" id="req-number">
                        <span class="requirement-icon" id="icon-number">✗</span>
                        <span>At least 1 number (0-9)</span>
                    </div>
                    <div class="requirement unmet" id="req-special">
                        <span class="requirement-icon" id="icon-special">✗</span>
                        <span>At least 1 special character (!@#$%^&*)</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input 
                    type="password" 
                    id="confirmPassword" 
                    name="confirmPassword" 
                    placeholder="••••••••"
                    required
                    autocomplete="new-password"
                >
            </div>

            <button type="submit" class="btn-register" id="registerBtn" disabled>
                CREATE ACCOUNT
            </button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">LOGIN HERE</a>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const registerForm = document.getElementById('registerForm');
        const registerBtn = document.getElementById('registerBtn');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        const successMessage = document.getElementById('successMessage');
        const successText = document.getElementById('successText');

        // Password validation patterns
        const requirements = {
            length: { test: (p) => p.length >= 8, id: 'req-length', icon: 'icon-length' },
            upper: { test: (p) => /[A-Z]/.test(p), id: 'req-upper', icon: 'icon-upper' },
            lower: { test: (p) => /[a-z]/.test(p), id: 'req-lower', icon: 'icon-lower' },
            number: { test: (p) => /[0-9]/.test(p), id: 'req-number', icon: 'icon-number' },
            special: { test: (p) => /[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(p), id: 'req-special', icon: 'icon-special' }
        };

        function validatePassword(password) {
            let allMet = true;
            for (const [key, req] of Object.entries(requirements)) {
                const isMet = req.test(password);
                const element = document.getElementById(req.id);
                const icon = document.getElementById(req.icon);
                
                if (isMet) {
                    element.classList.remove('unmet');
                    element.classList.add('met');
                    icon.textContent = '✓';
                } else {
                    element.classList.add('unmet');
                    element.classList.remove('met');
                    icon.textContent = '✗';
                    allMet = false;
                }
            }
            return allMet;
        }

        function checkFormValidity() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const username = document.getElementById('username').value;
            
            const passwordValid = validatePassword(password);
            const passwordsMatch = password === confirmPassword && password.length > 0;
            const usernameValid = username.trim().length >= 3;
            
            registerBtn.disabled = !(passwordValid && passwordsMatch && usernameValid);
        }

        passwordInput.addEventListener('input', checkFormValidity);
        confirmPasswordInput.addEventListener('input', checkFormValidity);
        document.getElementById('username').addEventListener('input', checkFormValidity);

        registerForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            // Disable button and show loading
            registerBtn.disabled = true;
            registerBtn.textContent = '⚡ CREATING ACCOUNT...';
            errorMessage.classList.remove('show');
            successMessage.classList.remove('show');
            
            const username = document.getElementById('username').value.trim();
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            // Client-side validation
            if (password !== confirmPassword) {
                errorText.textContent = 'Passwords do not match!';
                errorMessage.classList.add('show');
                registerBtn.disabled = false;
                registerBtn.textContent = 'CREATE ACCOUNT';
                return;
            }

            if (!validatePassword(password)) {
                errorText.textContent = 'Password does not meet all requirements!';
                errorMessage.classList.add('show');
                registerBtn.disabled = false;
                registerBtn.textContent = 'CREATE ACCOUNT';
                return;
            }
            
            try {
                const response = await fetch('/api/auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify({ 
                        action: 'register',
                        username: username, 
                        password: password 
                    })
                });
                
                const data = await response.json();
                console.log('Registration response:', response.status, data);
                
                if (response.ok && data.status === 'success') {
                    // Success - show success message and redirect
                    successText.textContent = data.message || 'Account created successfully! Redirecting to login...';
                    successMessage.classList.add('show');
                    
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    // Show error message
                    console.log('Registration failed:', data);
                    errorText.textContent = data.message || 'Registration failed. Please try again.';
                    errorMessage.classList.add('show');
                    registerBtn.disabled = false;
                    registerBtn.textContent = 'CREATE ACCOUNT';
                }
            } catch (error) {
                console.error('Error:', error);
                errorText.textContent = 'Network error. Please try again.';
                errorMessage.classList.add('show');
                registerBtn.disabled = false;
                registerBtn.textContent = 'CREATE ACCOUNT';
            }
        });

        // initial check
        checkFormValidity();
    </script>
</body>
</html>
