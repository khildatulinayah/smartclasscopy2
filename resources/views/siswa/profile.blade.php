@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    @include('components.siswa-sidebar')

    <!-- Main Content Area -->
    <div class="main-area">
        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            <div class="topbar-right">
                <button class="notification-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="notification-badge">3</span>
                </button>
                <div class="user-profile">
                    <img src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=3b82f6&color=fff' }}" alt="User" class="user-avatar">
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">Siswa</div>
                    </div>
                    <button class="user-menu-btn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Profile Header -->
            <section class="profile-header">
                <div class="profile-card">
                    <div class="profile-info">
                        <div class="profile-avatar">
                            <img src="{{ $student->profile_photo ? asset('storage/' . $student->profile_photo) : 'https://picsum.photos/seed/' . $student->id . '/120/120.jpg' }}" alt="{{ $student->name }}" class="avatar-img" id="avatarPreview">
                            <div class="avatar-status online"></div>
                            <div class="avatar-upload">
                                <label for="profilePhoto" class="upload-btn">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 5.22L12.93 7.07A2 2 0 0115.07 8.93l-1.22.812A2 2 0 0113 7.07V9a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 5.22L12.93 7.07A2 2 0 0115.07 8.93l-1.22.812A2 2 0 0113 7.07V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3-3m0 0l-3 3m3-3v12m0-8l3 3"></path>
                                    </svg>
                                    <span>Ubah Foto</span>
                                </label>
                                <input type="file" id="profilePhoto" name="profile_photo" accept="image/*" style="display: none;" onchange="previewImage(event)">
                            </div>
                        </div>
                        <div class="profile-details">
                            <h1 class="profile-name">{{ $student->name }}</h1>
                            <p class="profile-role">Siswa Aktif</p>
                            <div class="profile-meta">
                                <span class="meta-item">
                                    <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $student->email }}
                                </span>
                                <span class="meta-item">
                                    <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Bergabung: {{ $student->created_at->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="profile-actions">
                        <button class="btn-edit" onclick="openEditModal()">
                            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Profile
                        </button>
                    </div>
                </div>
            </section>

            <!-- Edit Profile Modal -->
            <div id="editModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Edit Profile</h3>
                        <button class="modal-close" onclick="closeEditModal()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('siswa.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" id="name" name="name" class="form-input" value="{{ $student->name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="gender" class="form-label">Jenis Kelamin</label>
                                <select id="gender" name="gender" class="form-input">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="L" {{ $student->gender == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ $student->gender == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-input" value="{{ $student->email }}" readonly>
                                <small class="form-help">Email tidak dapat diubah</small>
                            </div>
                            <div class="form-group">
                                <label for="profile_photo" class="form-label">Foto Profil</label>
                                <input type="file" id="profile_photo" name="profile_photo" class="form-input" accept="image/*">
                                <small class="form-help">Format: JPG, PNG, GIF. Maksimal 2MB</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                            <button type="submit" class="btn-save">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Profile Information -->
            <section class="profile-info-section">
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-header">
                            <h3 class="info-title">Informasi Pribadi</h3>
                        </div>
                        <div class="info-content">
                            <div class="info-item">
                                <div class="info-label">Nama Lengkap</div>
                                <div class="info-value">{{ $student->name }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $student->email }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Jenis Kelamin</div>
                                <div class="info-value">
                                    @if($student->gender)
                                        <span class="gender-badge {{ $student->gender == 'L' ? 'male' : 'female' }}">
                                            {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </span>
                                    @else
                                        <span class="gender-badge unknown">Belum diisi</span>
                                    @endif
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Status</div>
                                <div class="info-value">
                                    <span class="status-badge active">Siswa Aktif</span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Tanggal Bergabung</div>
                                <div class="info-value">{{ $student->created_at->format('d F Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-header">
                            <h3 class="info-title">Informasi Akun</h3>
                        </div>
                        <div class="info-content">
                            <div class="info-item">
                                <div class="info-label">ID Pengguna</div>
                                <div class="info-value">#{{ str_pad($student->id, 6, '0', STR_PAD_LEFT) }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Role</div>
                                <div class="info-value">
                                    <span class="role-badge student">Siswa</span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Terakhir Login</div>
                                <div class="info-value">{{ $student->updated_at->diffForHumans() }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Status Akun</div>
                                <div class="info-value">
                                    <span class="account-status verified">Terverifikasi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<style>
/* Profile Page Styles */
* { margin: 0; padding: 0; box-sizing: border-box; }

.dashboard-layout { 
    display: flex; 
    height: 100vh; 
    background: #f8fafc; 
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; 
}

/* SIDEBAR */
.sidebar {
    width: 280px;
    background: white;
    border-right: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 24px 20px;
    border-bottom: 1px solid #e2e8f0;
}

.logo {
    display: flex;
    align-items: center;
    gap: 12px;
}

.logo-img {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    object-fit: cover;
}

.logo-text {
    font-size: 20px;
    font-weight: 700;
    color: #1e293b;
}

.sidebar-nav {
    flex: 1;
    padding: 16px 0;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    color: #64748b;
    text-decoration: none;
    transition: all 0.2s ease;
    border-radius: 0 8px 8px 0;
    margin: 0 12px;
}

.nav-item:hover {
    background: #f8fafc;
    color: #3b82f6;
}

.nav-item.active {
    background: #eff6ff;
    color: #3b82f6;
    font-weight: 600;
}

.nav-icon {
    width: 20px;
    height: 20px;
}

.sidebar-footer { 
    padding: 16px 20px; 
    border-top: 1px solid #e2e8f0; 
}

.user-profile-mini { 
    display: flex; 
    align-items: center; 
    gap: 10px; 
    margin-bottom: 12px; 
}

.user-avatar-mini { 
    width: 32px; 
    height: 32px; 
    border-radius: 6px; 
    object-fit: cover; 
}

.user-name-mini { 
    font-size: 13px; 
    font-weight: 600; 
    color: #1e293b; 
}

.user-role-mini { 
    font-size: 11px; 
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
    gap: 8px; 
    background: #fee2e2; 
    color: #dc2626; 
    border: none; 
    padding: 8px 12px; 
    border-radius: 8px; 
    font-size: 13px; 
    font-weight: 600; 
    cursor: pointer; 
    transition: all 0.2s ease; 
}

.logout-btn:hover { 
    background: #fecaca; 
}

.logout-icon { 
    width: 16px; 
    height: 16px; 
}

/* ===== MAIN AREA ===== */
.main-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ===== TOPBAR ===== */
.topbar {
    background: white;
    border-bottom: 1px solid #e2e8f0;
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.topbar-left {
    display: flex;
    align-items: center;
}

.menu-toggle {
    background: none;
    border: none;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    color: #64748b;
    transition: all 0.2s ease;
}

.menu-toggle:hover {
    background: #f8fafc;
    color: #1e293b;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.notification-btn {
    position: relative;
    background: none;
    border: none;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    color: #64748b;
    transition: all 0.2s ease;
}

.notification-btn:hover {
    background: #f8fafc;
    color: #1e293b;
}

.notification-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    padding: 2px 4px;
    border-radius: 10px;
    min-width: 16px;
    text-align: center;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    object-fit: cover;
}

.user-info {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
}

.user-role {
    font-size: 12px;
    color: #64748b;
}

.user-menu-btn {
    background: none;
    border: none;
    padding: 4px;
    cursor: pointer;
    color: #64748b;
}

.user-menu-btn:hover {
    color: #3b82f6;
}

/* ===== MAIN CONTENT ===== */
.main-content {
    flex: 1;
    padding: 32px;
    overflow-y: auto;
}

.menu-toggle { 
    background: none; 
    border: none; 
    padding: 8px; 
    border-radius: 8px; 
    cursor: pointer; 
    color: #64748b; 
}

.notification-btn { 
    position: relative; 
    background: none; 
    border: none; 
    padding: 8px; 
    border-radius: 8px; 
    cursor: pointer; 
    color: #64748b; 
}

.notification-badge { 
    position: absolute; 
    top: 6px; 
    right: 6px; 
    background: #ef4444; 
    color: white; 
    font-size: 10px; 
    padding: 2px 4px; 
    border-radius: 10px; 
    min-width: 16px; 
    text-align: center; 
}

.user-profile { 
    display: flex; 
    align-items: center; 
    gap: 12px; 
}

.user-avatar { 
    width: 40px; 
    height: 40px; 
    border-radius: 8px; 
    object-fit: cover; 
}

.user-info { 
    display: flex; 
    flex-direction: column; 
}

.user-name { 
    font-size: 14px; 
    font-weight: 600; 
    color: #1e293b; 
}

.user-role { 
    font-size: 12px; 
    color: #64748b; 
}

/* Profile Header */
.profile-header { 
    margin-bottom: 32px; 
}

.profile-card { 
    background: white; 
    padding: 32px; 
    border-radius: 16px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    border: 1px solid #e2e8f0; 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
}

.profile-info { 
    display: flex; 
    align-items: center; 
    gap: 24px; 
}

.profile-avatar { 
    position: relative; 
}

.avatar-img { 
    width: 120px; 
    height: 120px; 
    border-radius: 50%; 
    object-fit: cover; 
    border: 4px solid #e2e8f0; 
}

.avatar-upload { 
    position: absolute; 
    bottom: 0; 
    right: 0; 
    background: rgba(0,0,0,0.7); 
    border-radius: 0 0 12px 12px; 
    padding: 8px 12px; 
    opacity: 0; 
    transition: opacity 0.2s ease; 
}

.profile-avatar:hover .avatar-upload { 
    opacity: 1; 
}

.upload-btn { 
    display: flex; 
    align-items: center; 
    gap: 6px; 
    color: white; 
    font-size: 12px; 
    font-weight: 600; 
    cursor: pointer; 
    transition: all 0.2s ease; 
}

.upload-btn:hover { 
    color: #3b82f6; 
}

.upload-btn svg { 
    width: 16px; 
    height: 16px; 
}

.avatar-status { 
    position: absolute; 
    bottom: 8px; 
    right: 8px; 
    width: 24px; 
    height: 24px; 
    border-radius: 50%; 
    border: 3px solid white; 
}

.avatar-status.online { 
    background: #10b981; 
}

.profile-details { 
    flex: 1; 
}

.profile-name { 
    font-size: 32px; 
    font-weight: 700; 
    color: #1e293b; 
    margin-bottom: 8px; 
}

.profile-role { 
    font-size: 16px; 
    color: #64748b; 
    margin-bottom: 16px; 
}

.profile-meta { 
    display: flex; 
    gap: 24px; 
}

.meta-item { 
    display: flex; 
    align-items: center; 
    gap: 8px; 
    color: #64748b; 
    font-size: 14px; 
}

.meta-icon { 
    width: 16px; 
    height: 16px; 
}

.profile-actions { 
    display: flex; 
    gap: 12px; 
}

.btn-edit { 
    display: flex; 
    align-items: center; 
    gap: 8px; 
    background: #3b82f6; 
    color: white; 
    border: none; 
    padding: 12px 20px; 
    border-radius: 8px; 
    font-size: 14px; 
    font-weight: 600; 
    cursor: pointer; 
    transition: all 0.2s ease; 
}

.btn-edit:hover { 
    background: #2563eb; 
}

.btn-icon { 
    width: 16px; 
    height: 16px; 
}

/* Profile Information Section */
.profile-info-section { 
    margin-bottom: 32px; 
}

.info-grid { 
    display: grid; 
    grid-template-columns: repeat(2, 1fr); 
    gap: 24px; 
}

.info-card { 
    background: white; 
    border-radius: 16px; 
    padding: 24px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    border: 1px solid #e2e8f0; 
}

.info-header { 
    margin-bottom: 24px; 
}

.info-title { 
    font-size: 18px; 
    font-weight: 600; 
    color: #1e293b; 
}

.info-content { 
    display: flex; 
    flex-direction: column; 
    gap: 20px; 
}

.info-item { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    padding-bottom: 16px; 
    border-bottom: 1px solid #f1f5f9; 
}

.info-item:last-child { 
    border-bottom: none; 
    padding-bottom: 0; 
}

.info-label { 
    font-size: 14px; 
    color: #64748b; 
    font-weight: 500; 
}

.info-value { 
    font-size: 14px; 
    color: #1e293b; 
    font-weight: 600; 
}

/* Status Badges */
.status-badge { 
    padding: 4px 12px; 
    border-radius: 20px; 
    font-size: 12px; 
    font-weight: 600; 
}

.status-badge.active { 
    background: #dcfce7; 
    color: #166534; 
}

.role-badge { 
    padding: 4px 12px; 
    border-radius: 20px; 
    font-size: 12px; 
    font-weight: 600; 
}

.role-badge.student { 
    background: #dbeafe; 
    color: #1e40af; 
}

.account-status { 
    padding: 4px 12px; 
    border-radius: 20px; 
    font-size: 12px; 
    font-weight: 600; 
}

.account-status.verified { 
    background: #fef3c7; 
    color: #92400e; 
}

/* Gender Badges */
.gender-badge { 
    padding: 4px 12px; 
    border-radius: 20px; 
    font-size: 12px; 
    font-weight: 600; 
}

.gender-badge.male { 
    background: #dbeafe; 
    color: #1e40af; 
}

.gender-badge.female { 
    background: #fce7f3; 
    color: #be185d; 
}

.gender-badge.unknown { 
    background: #f3f4f6; 
    color: #6b7280; 
}

/* Modal Styles */
.modal { 
    display: none; 
    position: fixed; 
    z-index: 1000; 
    left: 0; 
    top: 0; 
    width: 100%; 
    height: 100%; 
    background-color: rgba(0,0,0,0.5); 
}

.modal-content { 
    background-color: white; 
    margin: 5% auto; 
    padding: 0; 
    border-radius: 16px; 
    width: 90%; 
    max-width: 500px; 
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); 
}

.modal-header { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    padding: 24px; 
    border-bottom: 1px solid #e2e8f0; 
}

.modal-title { 
    font-size: 20px; 
    font-weight: 600; 
    color: #1e293b; 
    margin: 0; 
}

.modal-close { 
    background: none; 
    border: none; 
    padding: 8px; 
    border-radius: 8px; 
    cursor: pointer; 
    color: #64748b; 
    transition: all 0.2s ease; 
}

.modal-close:hover { 
    background: #f8fafc; 
    color: #1e293b; 
}

.modal-close svg { 
    width: 20px; 
    height: 20px; 
}

.modal-body { 
    padding: 24px; 
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

.form-input { 
    width: 100%; 
    padding: 12px 16px; 
    border: 1px solid #d1d5db; 
    border-radius: 8px; 
    font-size: 14px; 
    transition: all 0.2s ease; 
}

.form-input:focus { 
    outline: none; 
    border-color: #3b82f6; 
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); 
}

.form-input[readonly] { 
    background-color: #f9fafb; 
    color: #6b7280; 
}

.form-help { 
    display: block; 
    font-size: 12px; 
    color: #6b7280; 
    margin-top: 4px; 
}

.modal-footer { 
    display: flex; 
    justify-content: flex-end; 
    gap: 12px; 
    padding: 24px; 
    border-top: 1px solid #e2e8f0; 
}

.btn-cancel { 
    background: #f3f4f6; 
    color: #374151; 
    border: none; 
    padding: 12px 20px; 
    border-radius: 8px; 
    font-size: 14px; 
    font-weight: 600; 
    cursor: pointer; 
    transition: all 0.2s ease; 
}

.btn-cancel:hover { 
    background: #e5e7eb; 
}

.btn-save { 
    background: #3b82f6; 
    color: white; 
    border: none; 
    padding: 12px 20px; 
    border-radius: 8px; 
    font-size: 14px; 
    font-weight: 600; 
    cursor: pointer; 
    transition: all 0.2s ease; 
}

.btn-save:hover { 
    background: #2563eb; 
}

/* Responsive */
@media (max-width: 1200px) { 
    .info-grid { 
        grid-template-columns: 1fr; 
    } 
}

@media (max-width: 768px) { 
    .sidebar { 
        width: 260px; 
    } 
    
    .main-content { 
        padding: 20px; 
    } 
    
    .topbar { 
        padding: 12px 16px; 
    } 
    
    .topbar-right { 
        gap: 12px; 
    } 
    
    .profile-card { 
        flex-direction: column; 
        gap: 24px; 
    } 
    
    .profile-info { 
        flex-direction: column; 
        text-align: center; 
    } 
    
    .profile-meta { 
        flex-direction: column; 
        gap: 8px; 
    } 
    
    .info-grid { 
        grid-template-columns: 1fr; 
        gap: 16px; 
    } 
    
    .info-item { 
        flex-direction: column; 
        align-items: flex-start; 
        gap: 8px; 
        padding-bottom: 12px; 
    } 
    
    .modal-content { 
        width: 95%; 
        margin: 10% auto; 
    } 
}
</style>

<script>
function openEditModal() {
    document.getElementById('editModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target == modal) {
        closeEditModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeEditModal();
    }
});

// Image preview function
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('avatarPreview');
    
    if (file) {
        // Check file size (max 2MB)
        if (file.size > 2097152) {
            alert('Ukuran file terlalu besar. Maksimal 2MB.');
            event.target.value = '';
            return;
        }
        
        // Check file type
        if (!file.type.match('image.*')) {
            alert('Hanya file gambar yang diperbolehkan.');
            event.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}

// Show success message if exists
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = '{{ session()->get("success") }}';
    if (successMessage && successMessage !== '') {
        // You can add a toast notification here if needed
        console.log('Success:', successMessage);
    }
});
</script>
@endsection
