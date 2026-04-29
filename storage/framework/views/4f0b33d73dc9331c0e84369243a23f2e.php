<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>SMARTCLASS - Dashboard</title>
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('images/logo.png')); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('images/logo.png')); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('images/logo.png')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('images/logo.png')); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
* { margin: 0; padding: 0; box-sizing: border-box; }\n.dashboard-layout { display: flex; height: 100vh; background: #f8fafc; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }\n.sidebar { width: 280px; background: white; border-right: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; flex-direction: column; }\n.sidebar-header { padding: 24px 20px; border-bottom: 1px solid #e2e8f0; }\n.logo { display: flex; align-items: center; gap: 12px; }\n.logo-img { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; }\n.logo-text { font-size: 20px; font-weight: 700; color: #1e293b; }\n.sidebar-nav { flex: 1; padding: 16px 0; }\n.nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #64748b; text-decoration: none; transition: all 0.2s ease; border-radius: 0 8px 8px 0; margin: 0 12px; }\n.nav-item:hover { background: #f8fafc; color: #3b82f6; }\n.nav-item.active { background: #eff6ff; color: #3b82f6; font-weight: 600; }\n.nav-icon { width: 20px; height: 20px; }\n.sidebar-footer { padding: 16px 20px; border-top: 1px solid #e2e8f0; }\n.user-profile-mini { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }\n.user-avatar-mini { width: 32px; height: 32px; border-radius: 6px; object-fit: cover; }\n.user-name-mini { font-size: 13px; font-weight: 600; color: #1e293b; }\n.user-role-mini { font-size: 11px; color: #64748b; }\n.logout-form { display: block; }\n.logout-btn { width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: #fee2e2; color: #dc2626; border: none; padding: 8px 12px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }\n.logout-btn:hover { background: #fecaca; }\n.logout-icon { width: 16px; height: 16px; }\n.main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }\n.main-content { flex: 1; padding: 32px; overflow-y: auto; }\n.greeting-section { margin-bottom: 32px; }\n.greeting-title { font-size: 32px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }\n.greeting-card { background: white; padding: 32px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; margin-bottom: 32px; }\n.greeting-subtitle { font-size: 16px; color: #64748b; }\n.stats-section { margin-bottom: 32px; }\n.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }\n.stat-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }\n.stat-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }\n.stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }\n.stat-icon.balance { background: #dbeafe; color: #3b82f6; }\n.stat-icon.income { background: #dcfce7; color: #10b981; }\n.stat-icon.expense { background: #fee2e2; color: #ef4444; }\n.stat-icon.remaining { background: #fef3c7; color: #f59e0b; }\n.stat-icon.payment { background: #e0e7ff; color: #6366f1; }\n.stat-icon.info { background: #f3f4f6; color: #6b7280; }\n.stat-icon svg { width: 20px; height: 20px; }\n.stat-title { font-size: 16px; font-weight: 600; color: #1e293b; }\n.stat-value { font-size: 28px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }\n.stat-description { font-size: 14px; color: #64748b; }\n.progress-container { margin-bottom: 16px; }\n.progress-bar { width: 100%; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; }\n.progress-fill { height: 100%; background: linear-gradient(90deg, #3b82f6, #2563eb); border-radius: 4px; transition: width 0.3s ease; }\n.progress-text { text-align: center; font-size: 24px; font-weight: 700; color: #1e293b; margin-top: 8px; }\n.stat-details { display: flex; justify-content: space-between; gap: 16px; }\n.detail-item { flex: 1; text-align: center; padding: 12px; background: #f8fafc; border-radius: 8px; }\n.detail-label { font-size: 12px; color: #64748b; display: block; margin-bottom: 4px; }\n.detail-value { font-size: 14px; font-weight: 600; color: #1e293b; }\n.tables-section { display: grid; grid-template-columns: 1fr; gap: 24px; }\n.table-card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; overflow: hidden; }\n.table-header { padding: 24px; border-bottom: 1px solid #e2e8f0; }\n.table-title { font-size: 18px; font-weight: 600; color: #1e293b; }\n.table-container { padding: 24px; }\n.data-table { width: 100%; border-collapse: collapse; }\n.data-table th { background: #f8fafc; color: #475569; padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }\n.data-table td { padding: 16px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }\n.data-table tr:hover td { background: #f8fafc; }\n.status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }\n.feature-btn { background: #3b82f6; color: white; padding: 12px 24px; border-radius: 8px; font-weight: 600; text-decoration: none; }\n.feature-btn:hover { background: #2563eb; }\n@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }\n@media (max-width: 768px) { .main-content { padding: 20px; } .stats-grid { grid-template-columns: 1fr; } }\n.pixel-font { font-family: 'Press Start 2P', cursive; }\n.pixel-button { border: 3px solid #1a1a1a; box-shadow: 3px 3px 0px #1a1a1a; transition: all 0.1s; cursor: pointer; font-weight: bold; display: inline-block; text-decoration: none; }\n.pixel-button:hover { transform: translate(1px, 1px); box-shadow: 2px 2px 0px #1a1a1a; }\n.pixel-button:active { transform: translate(3px, 3px); box-shadow: 0px 0px 0px #1a1a1a; }
        
        /* Fix untuk layout grid */
        .grid {
            display: grid;
        }
        
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        
        @media (min-width: 1024px) {
            .lg\:col-span-1 { grid-column: span 1 / span 1; }
            .lg\:col-span-2 { grid-column: span 2 / span 2; }
            .lg\:col-span-3 { grid-column: span 3 / span 3; }
            .lg\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .lg\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }
        
        .gap-4 { gap: 1rem; }
        .gap-6 { gap: 1.5rem; }
        .gap-8 { gap: 2rem; }
        
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-8 { margin-bottom: 2rem; }
        
        .p-3 { padding: 0.75rem; }
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
        .py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
        
        .text-xs { font-size: 0.75rem; line-height: 1rem; }
        .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
        .text-base { font-size: 1rem; line-height: 1.5rem; }
        .text-lg { font-size: 1.125rem; line-height: 1.75rem; }
        .text-xl { font-size: 1.25rem; line-height: 1.75rem; }
        .text-2xl { font-size: 1.5rem; line-height: 2rem; }
        .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
        
        .font-bold { font-weight: 700; }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        
        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .items-center { align-items: center; }
        
        .space-y-4 > * + * { margin-top: 1rem; }
        .space-y-6 > * + * { margin-top: 1.5rem; }
        
        .border-t-4 { border-top-width: 4px; }
        .border-b-4 { border-bottom-width: 4px; }
        .border-4 { border-width: 4px; }
        .border-black { border-color: #000000; }
        
        .bg-blue-100 { background-color: #dbeafe; }
        .bg-green-100 { background-color: #d1fae5; }
        .bg-yellow-100 { background-color: #fef3c7; }
        .bg-blue-400 { background-color: #60a5fa; }
        .bg-red-400 { background-color: #f87171; }
        .bg-green-400 { background-color: #4ade80; }
        .bg-yellow-400 { background-color: #facc15; }
        
        .text-black { color: #000000; }
        .text-white { color: #ffffff; }
        .text-gray-600 { color: #4b5563; }
        .text-green-600 { color: #16a34a; }
        .text-red-600 { color: #dc2626; }
        .text-blue-600 { color: #2563eb; }
        .text-yellow-600 { color: #7c7b78; }
        .text-blue-800 { color: #1e40af; }
        .text-green-800 { color: #166534; }
        .text-yellow-800 { color: #92400e; }
        
        .max-w-7xl { max-width: 80rem; }
        .max-w-6xl { max-width: 72rem; }
        
        .mx-auto { margin-left: auto; margin-right: auto; }
    </style>
</head>
<body>
    <!-- Main Content -->
    <main style="width: 100%; height: 100vh; overflow-y: auto;">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</body>
</html><?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/layouts/app.blade.php ENDPATH**/ ?>