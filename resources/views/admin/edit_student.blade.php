@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img">
                <span class="logo-text">SMARTCLASS</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.students') }}" class="nav-item active">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a4 4 0 11-8 0 4 4 0 018 0zm1 3a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>CRUD Siswa</span>
            </a>
            <a href="{{ route('sekretaris.absensi') }}" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Monitor Absensi</span>
            </a>
            <a href="{{ route('bendahara.kas') }}" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Monitor Kas</span>
            </a>
            <a href="{{ route('admin.reports') }}" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Laporan</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-profile-mini">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=3b82f6&color=fff" alt="User" class="user-avatar-mini">
                <div class="user-info-mini">
                    <div class="user-name-mini">{{ auth()->user()->name }}</div>
                    <div class="user-role-mini">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg class="logout-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="main-area">
        <main class="main-content">
            <section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Edit Data Siswa</h1>
                    <p class="greeting-subtitle">{{ $student->name }} - Update informasi siswa</p>
                </div>
            </section>

            <div class="form-section">
                <div class="form-card">
                    <form method="POST" action="{{ route('admin.students.update', $student->id) }}" class="student-form">
                        @csrf @method('PUT')
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Foto Profil</label>
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=3b82f6&color=fff&size=120" class="avatar-preview" alt="{{ $student->name }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                                <input type="text" name="name" id="nameInput" class="form-input" required value="{{ old('name', $student->name) }}">
                                @error('name') <span class="error-text">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Email <span class="required">*</span></label>
                                <input type="email" name="email" id="emailInput" class="form-input" required value="{{ old('email', $student->email) }}">
                                @error('email') <span class="error-text">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
                                <input type="password" name="password" class="form-input">
                                @error('password') <span class="error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Role <span class="required">*</span></label>
                                <select name="role" class="form-input" required>
                                    <option value="">Pilih Role</option>
                                    <option value="siswa" {{ old('role', $student->role) == 'siswa' ? 'selected' : '' }}>Siswa</option>
                                    <option value="admin" {{ old('role', $student->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="bendahara" {{ old('role', $student->role) == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                                    <option value="sekretaris" {{ old('role', $student->role) == 'sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                                </select>
                                @error('role') <span class="error-text">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('admin.students') }}" class="btn-secondary">🔙 Kembali</a>
                            <button type="submit" class="btn-primary">💾 Update Siswa</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
/* Modern Dashboard Styles */
* { 
    margin: 0; 
    padding: 0; 
    box-sizing: border-box; 
}

.dashboard-layout { 
    display: flex; 
    height: 100vh; 
    background: #f8fafc; 
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; 
}

/* Sidebar Styles */
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

/* Main Content */
.main-area { 
    flex: 1; 
    display: flex; 
    flex-direction: column; 
    overflow: hidden; 
}

.main-content { 
    flex: 1; 
    padding: 32px; 
    overflow-y: auto; 
}

/* Greeting Section */
.greeting-section { 
    margin-bottom: 32px; 
}

.greeting-card { 
    background: white; 
    padding: 32px; 
    border-radius: 16px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    border: 1px solid #e2e8f0; 
}

.greeting-title { 
    font-size: 32px; 
    font-weight: 700; 
    color: #1e293b; 
    margin-bottom: 8px; 
}

.greeting-subtitle { 
    font-size: 16px; 
    color: #64748b; 
}

/* Form Section */
.form-section { 
    margin-bottom: 32px; 
}

.form-card { 
    background: white; 
    border-radius: 16px; 
    padding: 32px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    border: 1px solid #e2e8f0; 
}

.student-form { 
    max-width: 800px; 
    margin: 0 auto; 
}

.form-grid { 
    display: grid; 
    grid-template-columns: 1fr 1fr; 
    gap: 24px; 
    margin-bottom: 32px; 
}

.form-row { 
    display: grid; 
    grid-template-columns: 1fr 1fr; 
    gap: 24px; 
    margin-bottom: 24px; 
}

.form-group { 
    display: flex; 
    flex-direction: column; 
}

.form-label { 
    font-size: 14px; 
    font-weight: 600; 
    color: #1e293b; 
    margin-bottom: 8px; 
}

.required { 
    color: #ef4444; 
}

.form-input { 
    width: 100%; 
    padding: 12px 16px; 
    border: 2px solid #e2e8f0; 
    border-radius: 8px; 
    font-size: 14px; 
    transition: all 0.2s; 
    background: white; 
}

.form-input:focus { 
    outline: none; 
    border-color: #3b82f6; 
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1); 
}

.avatar-preview { 
    width: 120px; 
    height: 120px; 
    border-radius: 16px; 
    object-fit: cover; 
    border: 3px solid #e2e8f0; 
    margin-top: 8px; 
}

.error-text { 
    display: block; 
    font-size: 12px; 
    color: #ef4444; 
    margin-top: 4px; 
}

.form-actions { 
    display: flex; 
    gap: 16px; 
    justify-content: flex-end; 
    margin-top: 32px; 
}

.btn-primary { 
    background: #3b82f6; 
    color: white; 
    border: none; 
    padding: 12px 24px; 
    border-radius: 8px; 
    font-size: 14px; 
    font-weight: 600; 
    cursor: pointer; 
    transition: all 0.2s ease; 
}

.btn-primary:hover { 
    background: #2563eb; 
    transform: translateY(-1px); 
}

.btn-secondary { 
    background: #64748b; 
    color: white; 
    border: none; 
    padding: 12px 24px; 
    border-radius: 8px; 
    font-size: 14px; 
    font-weight: 600; 
    cursor: pointer; 
    text-decoration: none; 
    transition: all 0.2s ease; 
    display: inline-flex; 
    align-items: center; 
    justify-content: center; 
}

.btn-secondary:hover { 
    background: #475569; 
    transform: translateY(-1px); 
}

/* Responsive */
@media (max-width: 768px) { 
    .sidebar { 
        width: 260px; 
    } 
    .main-content { 
        padding: 20px; 
    } 
    .form-grid { 
        grid-template-columns: 1fr; 
    } 
    .form-row { 
        grid-template-columns: 1fr; 
    } 
    .form-actions { 
        flex-direction: column; 
    } 
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.getElementById('nameInput');
    const emailInput = document.getElementById('emailInput');
    const avatarPreview = document.querySelector('.avatar-preview');
    
    function updatePreview() {
        const name = nameInput.value || '{{ $student->name }}';
        avatarPreview.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3b82f6&color=fff&size=120`;
    }
    
    nameInput.addEventListener('input', updatePreview);
    emailInput.addEventListener('input', updatePreview);
});
</script>
@endsection
