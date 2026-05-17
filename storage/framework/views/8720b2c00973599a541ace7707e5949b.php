<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="<?php echo e(asset('images/logo2.png')); ?>" alt="Logo" class="logo-img" style="width: 60px; height: 60px;">
            <span class="logo-text">SMARTCLASS</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span>Dashboard</span>
        </a>
        <a href="<?php echo e(route('admin.students')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.students') || request()->routeIs('admin.create.student') || request()->routeIs('admin.edit.student') ? 'active' : ''); ?>">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a4 4 0 11-8 0 4 4 0 018 0zm1 3a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span>CRUD Siswa</span>
        </a>
        <a href="<?php echo e(route('admin.monitor.absensi')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.monitor.absensi') ? 'active' : ''); ?>">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2v2a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5 0.5l.5.5 8.5a.5.5 0 01-.5-.5v-8a.5.5 0 011 0v8a.5.5 0 01-.5.5z"></path>
            </svg>
            <span>Monitor Absensi</span>
        </a>
        <a href="<?php echo e(route('admin.monitor.pembayaran')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.monitor.pembayaran') ? 'active' : ''); ?>">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h2m2 10h2a2 2 0 002-2v-4a2 2 0 00-2-2h-2m-4 2H7a2 2 0 00-2 2v4a2 2 0 002 2h2"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h1m4 0h1"></path>
            </svg>
            <span>Monitor Pembayaran</span>
        </a>
        <a href="<?php echo e(route('admin.monitor.keuangan')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.monitor.keuangan') ? 'active' : ''); ?>">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Monitor Keuangan</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-profile-mini">
            <img src="<?php echo e(auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=3b82f6&color=fff'); ?>" alt="User" class="user-avatar-mini">
            <div class="user-info-mini">
                <div class="user-name-mini"><?php echo e(auth()->user()->name); ?></div>
                <div class="user-role-mini"><?php echo e(ucfirst(auth()->user()->role)); ?></div>
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>" class="logout-form" onsubmit="return confirmLogout()">
            <?php echo csrf_field(); ?>
            <button type="submit" class="logout-btn">
                <svg class="logout-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
<?php /**PATH C:\laragon\www\projectsc - Copy\resources\views\components\admin-sidebar.blade.php ENDPATH**/ ?>