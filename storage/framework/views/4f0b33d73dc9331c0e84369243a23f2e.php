<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>SMARTCLASS - Dashboard</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo2.png')); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .hidden { display: none; }
        .fixed { position: fixed; }
        .inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
        .z-50 { z-index: 50; }
        
        /* Toast Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            pointer-events: none;
        }
        .toast {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-left: 6px solid;
            min-width: 400px;
            max-width: 500px;
            pointer-events: auto;
            display: flex;
            align-items: center;
            gap: 16px;
            animation: slideIn 0.15s ease-out;
            transition: all 0.15s ease;
        }
        .toast.hiding {
            animation: slideOut 0.4s ease-out;
            opacity: 0;
            transform: translateX(100%);
        }
        .toast.success {
            border-left-color: #10b981;
            background: #f0fdf4;
        }
        .toast.error {
            border-left-color: #ef4444;
            background: #fef2f2;
        }
        .toast.warning {
            border-left-color: #f59e0b;
            background: #fffbeb;
        }
        .toast.info {
            border-left-color: #3b82f6;
            background: #eff6ff;
        }
        .toast-icon {
            flex-shrink: 0;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 16px;
        }
        .toast.success .toast-icon {
            background: #10b981;
        }
        .toast.error .toast-icon {
            background: #ef4444;
        }
        .toast.warning .toast-icon {
            background: #f59e0b;
        }
        .toast.info .toast-icon {
            background: #3b82f6;
        }
        .toast-content {
            flex: 1;
        }
        .toast-title {
            font-weight: 700;
            margin-bottom: 4px;
            color: #1f2937;
            font-size: 18px;
        }
        .toast-message {
            font-size: 16px;
            color: #6b7280;
            line-height: 1.5;
        }
        .toast-close {
            flex-shrink: 0;
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .toast-close:hover {
            background: rgba(0, 0, 0, 0.1);
            color: #4b5563;
        }
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        @media (max-width: 640px) {
            .toast-container {
                top: 10px;
                right: 10px;
                left: 10px;
            }
            .toast {
                min-width: auto;
                max-width: none;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>
    
    <?php echo $__env->yieldContent('content'); ?>
    
    <!-- Toast System JavaScript -->
    <script>
        // Toast System
        let toastCounter = 0;
        const toastContainer = document.getElementById('toastContainer');
        
        function showToast(message, type = 'info', title = null, duration = 5000) {
            const toastId = 'toast-' + (++toastCounter);
            
            const icons = {
                success: '✓',
                error: '✗',
                warning: '⚠',
                info: 'ℹ'
            };
            
            const titles = {
                success: 'Berhasil',
                error: 'Error',
                warning: 'Peringatan',
                info: 'Informasi'
            };
            
            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <div class="toast-icon">${icons[type]}</div>
                <div class="toast-content">
                    <div class="toast-title">${title || titles[type]}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="hideToast('${toastId}')">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </button>
            `;
            
            toastContainer.appendChild(toast);
            
            // Force immediate display to avoid delay
            setTimeout(() => {
                toast.style.display = 'flex';
            }, 10);
            
            // Auto hide after duration
            if (duration > 0) {
                setTimeout(() => hideToast(toastId), duration);
            }
            
            return toastId;
        }
        
        function hideToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.add('hiding');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 400);
            }
        }
        
        // Helper functions for different types
        function showSuccessToast(message, title = null, duration = 6000) {
            return showToast(message, 'success', title, duration);
        }
        
        function showErrorToast(message, title = null, duration = 8000) {
            return showToast(message, 'error', title, duration);
        }
        
        function showWarningToast(message, title = null, duration = 7000) {
            return showToast(message, 'warning', title, duration);
        }
        
        function showInfoToast(message, title = null, duration = 6000) {
            return showToast(message, 'info', title, duration);
        }
    </script>
</body>
</html><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/layouts/app.blade.php ENDPATH**/ ?>