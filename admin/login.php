<?php
session_start();
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Vérification simple (on améliorera la sécurité après)
    if($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPower Admin - Login</title>
    <link rel="stylesheet" href="css/admin-login.css">
</head>
<body>
    <div class="login-wrapper">
        <!-- Background Animation -->
        <div class="bg-animation">
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
            <div class="floating-shape shape-3"></div>
            <div class="floating-shape shape-4"></div>
        </div>
        
        <!-- Login Container -->
        <div class="login-container">
            <div class="login-card">
                <!-- Header -->
                <div class="login-header">
                    <div class="logo-section">
                        <img src="../images/logo-gpower.png" alt="GPower Logo" class="login-logo">
                        <h1>GPower</h1>
                    </div>
                    <h2>Admin Portal</h2>
                    <p>Professional Equipment Management System</p>
                </div>
                
                <!-- Form -->
                <div class="login-form">
                    <?php if($error): ?>
                        <div class="error-message">
                            <span class="error-icon">⚠️</span>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="form">
                        <div class="input-group">
                            <div class="input-wrapper">
                                <span class="input-icon">👤</span>
                                <input type="text" name="username" placeholder="Username" required 
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                                       class="form-input">
                            </div>
                        </div>
                        
                        <div class="input-group">
                            <div class="input-wrapper">
                                <span class="input-icon">🔒</span>
                                <input type="password" name="password" placeholder="Password" required 
                                       class="form-input">
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <span id="toggle-icon">👁️</span>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="login-btn">
                            <span class="btn-text">Sign In</span>
                            <span class="btn-icon">→</span>
                        </button>
                    </form>
                    
                </div>
                
                <!-- Footer -->
                <div class="login-footer">
                    <p>&copy; 2024 GPower. Professional Equipment Solutions.</p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Password toggle functionality
        function togglePassword() {
            const passwordInput = document.querySelector('input[name="password"]');
            const toggleIcon = document.getElementById('toggle-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }
        
        // Form animation on load
        document.addEventListener('DOMContentLoaded', function() {
            const loginCard = document.querySelector('.login-card');
            loginCard.style.opacity = '0';
            loginCard.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                loginCard.style.transition = 'all 0.6s ease';
                loginCard.style.opacity = '1';
                loginCard.style.transform = 'translateY(0)';
            }, 100);
        });
        
        // Input focus effects
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    </script>
</body>
</html>