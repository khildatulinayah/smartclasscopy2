@extends('layouts.app')

@section('content')
<div class="dashboard-layout">
    <!-- Sidebar -->
    @include('components.admin-sidebar')

    <div class="main-area">
        <main class="main-content">
            <section class="greeting-section">
                <div class="greeting-card">
                    <h1 class="greeting-title">Tambah Siswa Baru</h1>
                    <p class="greeting-subtitle">Isi data siswa dengan lengkap dan akurat</p>
                </div>
            </section>

            <div class="form-section">
                <div class="form-card">
                    <form method="POST" action="{{ route('admin.students.store') }}" class="student-form">
                        @csrf
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Foto Profil (Otomatis)</label>
                                <div id="avatarPreview" class="avatar-preview">
                                    Preview akan muncul di sini
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                                <input type="text" name="name" id="nameInput" class="form-input" required value="{{ old('name') }}">
                                @error('name') <span class="error-text">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Email <span class="required">*</span></label>
                                <input type="email" name="email" id="emailInput" class="form-input" required value="{{ old('email') }}">
                                @error('email') <span class="error-text">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Password <span class="required">*</span></label>
                                <input type="password" name="password" class="form-input" required>
                                @error('password') <span class="error-text">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Role <span class="required">*</span></label>
                                <select name="role" class="form-input" required>
                                    <option value="">Pilih Role</option>
                                    <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="bendahara" {{ old('role') == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                                    <option value="sekretaris" {{ old('role') == 'sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                                </select>
                                @error('role') <span class="error-text">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('admin.students') }}" class="btn-secondary">🔙 Kembali</a>
                            <button type="submit" class="btn-primary">💾 Simpan Siswa</button>
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
    background: #dbeafe; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    font-size: 14px; 
    color: #3b82f6; 
    margin-top: 8px; 
    border: 2px dashed #93c5fd; 
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
    const avatarPreview = document.getElementById('avatarPreview');
    
    function updatePreview() {
        const name = nameInput.value || 'Nama Siswa';
        avatarPreview.innerHTML = `<img src="https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3b82f6&color=fff&size=120" style="width: 100%; height: 100%; border-radius: 16px; object-fit: cover;">`;
    }
    
    nameInput.addEventListener('input', updatePreview);
    emailInput.addEventListener('input', updatePreview);
    updatePreview(); // Initial
});
</script>
@endsection
