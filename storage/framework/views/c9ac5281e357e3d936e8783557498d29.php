<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>SMARTCLASS - <?php echo $__env->yieldContent('title', 'Dashboard Bendahara'); ?></title>
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('images/logo.png')); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .hidden { display: none; }
        .fixed { position: fixed; }
        .inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
        .z-50 { z-index: 50; }

        /* Standard Sidebar Styles */
        .dashboard-layout { 
            display: flex; 
            height: 100vh; 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); 
            font-family: 'Inter', sans-serif; 
        }
        .sidebar { 
            width: 280px; 
            background: #ffffff; 
            border-right: 1px solid #e5e7eb; 
            box-shadow: 4px 0 20px rgba(0,0,0,0.08); 
            display: flex; 
            flex-direction: column; 
        }
        .sidebar-header { 
            padding: 2rem 1.5rem; 
            border-bottom: 1px solid #f3f4f6; 
        }
        .logo { 
            display: flex; 
            align-items: center; 
            gap: 0.75rem; 
        }
        .logo-img { 
            width: 2.5rem; 
            height: 2.5rem; 
            border-radius: 0.5rem; 
            object-fit: cover; 
        }
        .logo-text { 
            font-size: 1.25rem; 
            font-weight: 800; 
            background: linear-gradient(135deg, #3b82f6, #1d4ed8); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
            background-clip: text; 
        }
        .sidebar-nav { 
            flex: 1; 
            padding: 1rem 0; 
        }
        .nav-item { 
            display: flex; 
            align-items: center; 
            gap: 0.75rem; 
            padding: 0.875rem 1.25rem; 
            color: #6b7280; 
            text-decoration: none; 
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); 
            border-radius: 0 1rem 1rem 0; 
            margin: 0 0.75rem; 
            position: relative; 
            overflow: hidden; 
        }
        .nav-item:hover { 
            background: #f8fafc; 
            color: #3b82f6; 
            transform: translateX(2px); 
        }
        .nav-item.active { 
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); 
            color: #1d4ed8; 
            font-weight: 600; 
        }
        .nav-icon { 
            width: 1.25rem; 
            height: 1.25rem; 
            flex-shrink: 0; 
        }
        .sidebar-section-header { 
            font-size: 0.75rem; 
            font-weight: 600; 
            letter-spacing: 0.05em; 
            color: #9ca3af; 
            text-transform: uppercase; 
        }
        .sidebar-footer { 
            padding: 1rem 1.25rem; 
            border-top: 1px solid #f3f4f6; 
        }
        .user-profile-mini { 
            display: flex; 
            align-items: center; 
            gap: 0.625rem; 
            margin-bottom: 0.75rem; 
        }
        .user-avatar-mini { 
            width: 2rem; 
            height: 2rem; 
            border-radius: 0.375rem; 
            object-fit: cover; 
        }
        .user-name-mini { 
            font-size: 0.8125rem; 
            font-weight: 600; 
            color: #1e293b; 
        }
        .user-role-mini { 
            font-size: 0.6875rem; 
            color: #64748b; 
        }
        .logout-form { 
            display: block; 
        }
        .logout-btn { 
            width: 100%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 0.5rem; 
            background: #fee2e2; 
            color: #dc2626; 
            border: none; 
            padding: 0.5rem 0.75rem; 
            border-radius: 0.5rem; 
            font-size: 0.8125rem; 
            font-weight: 600; 
            cursor: pointer; 
            transition: all 0.2s ease; 
        }
        .logout-btn:hover { 
            background: #fecaca; 
        }
        .logout-icon { 
            width: 1rem; 
            height: 1rem; 
        }
        .main-area { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
        }
        .main-content { 
            flex: 1; 
            padding: 2rem; 
            overflow-y: auto; 
            scroll-behavior: smooth; 
        }

        /* Responsive Design */
        @media (max-width: 768px) { 
            .sidebar { 
                width: 100%; 
                position: absolute; 
                z-index: 50; 
                transform: translateX(-100%); 
            } 
            .sidebar.open { 
                transform: translateX(0); 
            }
            .main-content { 
                padding: 1rem; 
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <?php echo $__env->make('components.bendahara-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Main Content Area -->
        <div class="main-area">
            <main class="main-content">
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/layouts/bendahara.blade.php ENDPATH**/ ?>