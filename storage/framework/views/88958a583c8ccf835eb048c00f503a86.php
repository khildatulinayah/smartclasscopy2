<!-- Sidebar for Student Dashboard -->
<aside class="sidebar" id="appSidebar" aria-label="Sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="<?php echo e(asset('images/logo2.png')); ?>" alt="Logo" class="logo-img" style="width: 60px; height: 60px;">
            <span class="logo-text">SMARTCLASS</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="<?php echo e(route('siswa.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('siswa.dashboard') ? 'active' : ''); ?>">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="<?php echo e(route('siswa.profile')); ?>" class="nav-item <?php echo e(request()->routeIs('siswa.profile') ? 'active' : ''); ?>">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span>Profile</span>
        </a>

        <a href="<?php echo e(route('siswa.absensi')); ?>" class="nav-item <?php echo e(request()->routeIs('siswa.absensi') ? 'active' : ''); ?>">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Absensi</span>
        </a>

        <a href="<?php echo e(route('siswa.pembayaran')); ?>" class="nav-item <?php echo e(request()->routeIs('siswa.pembayaran') ? 'active' : ''); ?>">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Pembayaran</span>
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
                <svg class="logout-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<!-- Mobile off-canvas helpers (rendered here for student layout) -->
<button id="sidebarClose" type="button" aria-label="Tutup Sidebar" style="display:none;"></button>
<div id="sidebarOverlay" class="hidden fixed inset-0 bg-black/30 z-40"></div>

<script>
    (function(){
        const sidebar = document.getElementById('appSidebar');
        if(!sidebar) return;

        const toggleBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('sidebarClose');
        const overlay = document.getElementById('sidebarOverlay');

        const openSidebar = () => {
            sidebar.classList.add('open');
            if(overlay) overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        };

        const closeSidebar = () => {
            sidebar.classList.remove('open');
            if(overlay) overlay.classList.add('hidden');
            document.body.style.overflow = '';
        };

        toggleBtn && toggleBtn.addEventListener('click', (e)=>{ e.preventDefault(); openSidebar(); });
        closeBtn && closeBtn.addEventListener('click', (e)=>{ e.preventDefault(); closeSidebar(); });
        overlay && overlay.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', (e)=>{
            if(e.key === 'Escape') closeSidebar();
        });
    })();
</script>

<?php /**PATH C:\laragon\www\projectsc - Copy\resources\views/components/siswa-sidebar.blade.php ENDPATH**/ ?>