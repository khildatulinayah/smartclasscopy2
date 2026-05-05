<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartClass - Login</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo2.png')); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 50%, #eff6ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        /* Background decorative elements */
        .bg-decoration {
            position: absolute;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.08);
            animation: float 6s ease-in-out infinite;
        }
        
        .bg-decoration:nth-child(1) {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .bg-decoration:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 70%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .bg-decoration:nth-child(3) {
            width: 100px;
            height: 100px;
            bottom: 10%;
            left: 20%;
            animation-delay: 4s;
        }
        
        .bg-dots {
            position: absolute;
            width: 100px;
            height: 100px;
            background-image: radial-gradient(circle, rgba(59, 130, 246, 0.2) 2px, transparent 2px);
            background-size: 20px 20px;
            border-radius: 8px;
            opacity: 0.6;
        }
        
        .bg-dots:nth-child(4) {
            top: 20%;
            right: 20%;
        }
        
        .bg-dots:nth-child(5) {
            bottom: 30%;
            right: 30%;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 10;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .logo-icon {
            width: 140px;
            height: 140px;
            object-fit: contain;
            display: block;
            margin: 0 auto 16px;
        }
        
        .brand-name {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }
        
        .brand-subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 24px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .login-title {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .login-subtitle {
            font-size: 14px;
            color: #6b7280;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            color: #1f2937;
            background: #ffffff;
            transition: all 0.2s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-input::placeholder {
            color: #9ca3af;
        }
        
        .password-strength {
            margin-top: 12px;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        
        .strength-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 13px;
            color: #374151;
        }
        
        .strength-text {
            font-weight: 600;
            font-size: 12px;
        }
        
        .strength-text.very-weak { color: #dc2626; }
        .strength-text.weak { color: #ea580c; }
        .strength-text.fair { color: #d97706; }
        .strength-text.good { color: #059669; }
        .strength-text.strong { color: #047857; }
        
        .strength-bar {
            width: 100%;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 8px;
        }
        
        .strength-fill {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background-color 0.3s ease;
            border-radius: 3px;
        }
        
        .strength-fill.very-weak { width: 20%; background: #dc2626; }
        .strength-fill.weak { width: 40%; background: #ea580c; }
        .strength-fill.fair { width: 60%; background: #f59e0b; }
        .strength-fill.good { width: 80%; background: #10b981; }
        .strength-fill.strong { width: 100%; background: #059669; }
        
        .strength-tips {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.4;
        }
        
        .strength-tips ul {
            margin: 0;
            padding-left: 16px;
        }
        
        .strength-tips li {
            margin-bottom: 2px;
        }
        
        .strength-tips li.valid {
            color: #059669;
            text-decoration: line-through;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 16px;
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 16px;
            cursor: pointer;
            transition: color 0.2s;
        }
        
        .password-toggle:hover {
            color: #6b7280;
        }
        
        .login-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 24px;
        }
        
        .login-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }
        
        .login-button:active {
            transform: translateY(0);
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 16px;
        }
        
        .forgot-password a {
            color: #3b82f6;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .forgot-password a:hover {
            color: #1d4ed8;
        }
        
        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
        }
        
        .error-box p {
            color: #dc2626;
            font-size: 14px;
            margin-bottom: 4px;
        }
        
        .error-box p:last-child {
            margin-bottom: 0;
        }
        
        .error-box strong {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .footer {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            color: white;
            font-size: 12px;
            z-index: 10;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 32px 24px;
            }
            
            .login-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Background decorations -->
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    <div class="bg-decoration"></div>
    <div class="bg-dots"></div>
    <div class="bg-dots"></div>
    
    <div class="login-container">
        <div class="logo-section">
            <img src="<?php echo e(asset('images/logo2.png')); ?>" alt="Logo" class="logo-icon">
            <div class="brand-name">smartclass</div>
            <div class="brand-subtitle">Aplikasi Manajemen Kelas</div>
        </div>
        
        <div class="login-header">
            <h2 class="login-title">Masuk ke Akun Anda</h2>
            <p class="login-subtitle">Silakan masuk untuk melanjutkan</p>
        </div>
        
        <?php if($errors->any()): ?>
        <div class="error-box">
            <strong>Login gagal!</strong>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p><?php echo e($error); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo e(route('login.post')); ?>">
            <?php echo csrf_field(); ?>
            
            <div class="form-group">
                <label class="form-label">Email atau Username</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="email" value="<?php echo e(old('email')); ?>" 
                           class="form-input" placeholder="Masukkan email atau username" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" id="password" 
                           class="form-input" placeholder="Masukkan password" required>
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>
            </div>
            
            <button type="submit" class="login-button">Masuk</button>
        </form>
        
      
    </div>
    
    <div class="footer">
        © 2024 SmartClass. Semua hak dilindungi.
    </div>
    
    <script>
        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        // Add input focus effects
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('.input-icon').style.color = '#3b82f6';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.querySelector('.input-icon').style.color = '#9ca3af';
            });
        });
    </script>
</body>
</html>


<?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/login.blade.php ENDPATH**/ ?>