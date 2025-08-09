<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Network Analytics - Kayıt Ol' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" style="stop-color:rgba(255,255,255,0.1)"/><stop offset="100%" style="stop-color:rgba(255,255,255,0)"/></radialGradient></defs><circle cx="300" cy="300" r="120" fill="url(%23a)"/><circle cx="700" cy="200" r="100" fill="url(%23a)"/><circle cx="500" cy="600" r="140" fill="url(%23a)"/><circle cx="150" cy="750" r="90" fill="url(%23a)"/></svg>');
            animation: float 25s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(-5deg); }
        }

        .register-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 480px;
            padding: 20px;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: all 0.3s ease;
        }

        .register-card:hover {
            transform: translateY(-8px);
            box-shadow: 
                0 35px 70px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.3);
        }

        .brand-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .brand-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .brand-icon i {
            color: white;
            font-size: 32px;
        }

        .brand-title {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {
            font-size: 16px;
            color: #718096;
            font-weight: 400;
        }

        .alert {
            border: none;
            border-radius: 12px;
            font-weight: 500;
            margin-bottom: 24px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .alert-danger {
            background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
            color: #c53030;
        }

        .alert-danger ul {
            margin: 0;
            padding-left: 20px;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 8px;
            color: #667eea;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            font-size: 16px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            box-shadow: none;
            position: relative;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: white;
            outline: none;
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: #a0aec0;
            font-weight: 400;
        }

        .password-strength {
            margin-top: 8px;
            font-size: 12px;
        }

        .strength-meter {
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            margin-top: 4px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { background: #e53e3e; width: 25%; }
        .strength-fair { background: #dd6b20; width: 50%; }
        .strength-good { background: #38a169; width: 75%; }
        .strength-strong { background: #065f46; width: 100%; }

        .form-text {
            color: #718096;
            font-size: 12px;
            margin-top: 4px;
        }

        .btn-register {
            width: 100%;
            padding: 16px;
            font-size: 16px;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: white;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-register:hover::before {
            left: 100%;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 45px rgba(102, 126, 234, 0.4);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .btn-register.loading {
            pointer-events: none;
        }

        .btn-register.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }

        .login-link {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            position: relative;
        }

        .login-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }

        .login-link a:hover::after {
            width: 100%;
        }

        .login-link a:hover {
            color: #764ba2;
        }

        /* Floating particles */
        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            pointer-events: none;
            animation: float-particle 8s linear infinite;
        }

        @keyframes float-particle {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .register-card {
                padding: 32px 24px;
                margin: 20px;
            }
            
            .brand-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="brand-section">
                <div class="brand-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1 class="brand-title">Kayıt Ol</h1>
                <p class="brand-subtitle">Yeni hesabınızı oluşturun</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($errors)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/register" id="registerForm">
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i>
                        Kullanıcı Adı
                    </label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="<?= htmlspecialchars($username ?? '') ?>" 
                           placeholder="Benzersiz kullanıcı adınız" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i>
                        Email Adresi
                    </label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= htmlspecialchars($email ?? '') ?>" 
                           placeholder="ornek@email.com" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i>
                        Şifre
                    </label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Güçlü bir şifre oluşturun" required>
                    <div class="password-strength">
                        <div class="strength-meter">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <small id="strengthText" class="form-text">En az 6 karakter olmalıdır</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">
                        <i class="fas fa-shield-alt"></i>
                        Şifre Tekrarı
                    </label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                           placeholder="Şifrenizi tekrar girin" required>
                    <small class="form-text" id="passwordMatch">Şifreler eşleşmelidir</small>
                </div>
                
                <button type="submit" class="btn btn-register" id="registerBtn">
                    <i class="fas fa-user-plus me-2"></i>
                    Hesap Oluştur
                </button>
            </form>
            
            <div class="login-link">
                <a href="/login">
                    <i class="fas fa-arrow-left me-1"></i>
                    Zaten hesabınız var mı? Giriş yapın
                </a>
            </div>
        </div>
    </div>

    <!-- Floating particles -->
    <script>
        // Create floating particles
        function createParticle() {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + 'vw';
            particle.style.width = particle.style.height = Math.random() * 12 + 4 + 'px';
            particle.style.animationDuration = Math.random() * 4 + 4 + 's';
            document.body.appendChild(particle);

            setTimeout(() => {
                particle.remove();
            }, 8000);
        }

        // Create particles periodically
        setInterval(createParticle, 400);

        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordMatch = document.getElementById('passwordMatch');

        function checkPasswordStrength(password) {
            let strength = 0;
            let text = '';
            let className = '';

            if (password.length >= 6) strength += 1;
            if (password.length >= 8) strength += 1;
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;

            switch (strength) {
                case 0:
                case 1:
                    text = 'Çok zayıf';
                    className = 'strength-weak';
                    break;
                case 2:
                    text = 'Zayıf';
                    className = 'strength-fair';
                    break;
                case 3:
                case 4:
                    text = 'İyi';
                    className = 'strength-good';
                    break;
                case 5:
                    text = 'Çok güçlü';
                    className = 'strength-strong';
                    break;
            }

            strengthFill.className = 'strength-fill ' + className;
            strengthText.textContent = text;
        }

        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (confirmPassword === '') {
                passwordMatch.textContent = 'Şifreler eşleşmelidir';
                passwordMatch.style.color = '#718096';
                confirmPasswordInput.style.borderColor = '#e2e8f0';
            } else if (password === confirmPassword) {
                passwordMatch.innerHTML = '<i class="fas fa-check text-success me-1"></i>Şifreler eşleşiyor';
                passwordMatch.style.color = '#38a169';
                confirmPasswordInput.style.borderColor = '#38a169';
            } else {
                passwordMatch.innerHTML = '<i class="fas fa-times text-danger me-1"></i>Şifreler eşleşmiyor';
                passwordMatch.style.color = '#e53e3e';
                confirmPasswordInput.style.borderColor = '#e53e3e';
            }
        }

        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });

        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        passwordInput.addEventListener('input', checkPasswordMatch);

        // Form submission with loading state
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                confirmPasswordInput.focus();
                return false;
            }

            const btn = document.getElementById('registerBtn');
            btn.classList.add('loading');
            btn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Hesap oluşturuluyor...';
        });

        // Input focus effects
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
                this.parentElement.style.transition = 'all 0.3s ease';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });

            // Real-time validation effects
            input.addEventListener('input', function() {
                if (this.checkValidity()) {
                    this.style.borderColor = '#38a169';
                } else if (this.value.length > 0) {
                    this.style.borderColor = '#e53e3e';
                } else {
                    this.style.borderColor = '#e2e8f0';
                }
            });
        });

        // Username availability check (simulate)
        const usernameInput = document.getElementById('username');
        let usernameTimeout;

        usernameInput.addEventListener('input', function() {
            clearTimeout(usernameTimeout);
            const username = this.value;
            
            if (username.length >= 3) {
                usernameTimeout = setTimeout(() => {
                    // Simulate API call
                    console.log('Checking username availability:', username);
                    // Here you could make an AJAX call to check username availability
                }, 500);
            }
        });

        // Email validation
        const emailInput = document.getElementById('email');
        emailInput.addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.style.borderColor = '#e53e3e';
            } else if (email) {
                this.style.borderColor = '#38a169';
            }
        });

        // Add some micro-interactions
        document.querySelector('.brand-icon').addEventListener('click', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'bounce 2s ease-in-out infinite';
            }, 10);
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>