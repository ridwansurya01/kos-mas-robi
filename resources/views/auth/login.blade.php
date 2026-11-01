@extends('layouts.app')

@section('title', 'Login Akun - Kos Mas Robi')

@section('content')
<div style="min-height: 100vh; min-height: 100dvh; width: 100%; display: flex; align-items: center; justify-content: center; padding: 24px; background: radial-gradient(circle at 15% 15%, rgba(14, 116, 130, 0.10) 0%, transparent 45%), radial-gradient(circle at 85% 85%, rgba(244, 63, 94, 0.06) 0%, transparent 45%), #f8fafc;">
  <div style="width: 100%; max-width: 450px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 28px; padding: 36px 32px; box-shadow: 0 16px 40px -8px rgba(3, 35, 40, 0.10);">

    <!-- Header Logo & Title -->
    <div style="text-align: center; margin-bottom: 24px;">
      <a href="{{ route('home') }}" style="display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; text-decoration: none;">
        <div style="width: 54px; height: 54px; border-radius: 18px; background: linear-gradient(135deg, #0a5661, #0e7482); display: flex; align-items: center; justify-content: center; color: #ffffff; box-shadow: 0 8px 20px rgba(14, 116, 130, 0.35);">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M3 9.5L12 3l9 6.5V20a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 20V9.5z"/>
            <path d="M9 21V12h6v9"/>
          </svg>
        </div>
      </a>
      <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Masuk ke Akun Anda</h2>
      <p style="font-size: 0.84rem; color: #64748b; margin: 0;">Portal Layanan Hunian & Manajemen Kos Mas Robi</p>
    </div>

    <!-- Quick Credentials Helper Box -->
    <div style="background: #f0fdfa; border: 1px solid #ccfbf1; border-radius: 18px; padding: 14px 16px; margin-bottom: 20px;">
      <div style="font-size: 0.75rem; font-weight: 700; color: #0e7482; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; display: flex; align-items: center; gap: 5px;">
        <span>⚡ Akun Percobaan (1-Klik Isi Form):</span>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
        <button type="button" onclick="fillCreds('admin@gmail.com', '123')" style="background: #063d45; color: #ffffff; border: none; padding: 8px 10px; border-radius: 10px; font-size: 0.76rem; font-weight: 700; cursor: pointer; text-align: left; transition: opacity 0.2s;">
          <div style="color: #99f6e4;">🔑 Admin</div>
          <div style="font-size: 0.7rem; opacity: 0.85;">admin@gmail.com (123)</div>
        </button>
        <button type="button" onclick="fillCreds('dimas@gmail.com', 'password123')" style="background: #ffffff; color: #0e7482; border: 1px solid #ccfbf1; padding: 8px 10px; border-radius: 10px; font-size: 0.76rem; font-weight: 700; cursor: pointer; text-align: left; transition: opacity 0.2s;">
          <div style="color: #0a5661;">📱 Pelanggan</div>
          <div style="font-size: 0.7rem; color: #64748b;">dimas@gmail.com</div>
        </button>
      </div>
    </div>

    @if ($errors->any())
      <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 14px; border-radius: 14px; font-size: 0.82rem; margin-bottom: 16px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST" id="formLogin" style="display: flex; flex-direction: column; gap: 16px;">
      @csrf

      <div class="form-group" style="margin-bottom: 0;">
        <label for="loginEmail" style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">Alamat Email</label>
        <input type="email" id="loginEmail" name="email" class="form-control" placeholder="admin@gmail.com atau dimas@gmail.com" value="{{ old('email') }}" required autofocus style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px 16px; font-size: 0.9rem; color: #0f172a; width: 100%; box-sizing: border-box;">
      </div>

      <div class="form-group" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
          <label for="loginPassword" style="font-size: 0.82rem; font-weight: 700; color: #334155; margin: 0;">Kata Sandi</label>
        </div>
        
        <!-- Password input with toggle button on the far right -->
        <div style="position: relative; width: 100%;">
          <input type="password" id="loginPassword" name="password" class="form-control" placeholder="Masukkan kata sandi..." required style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px 46px 12px 16px; font-size: 0.9rem; color: #0f172a; width: 100%; box-sizing: border-box;">
          
          <button type="button" id="btnTogglePassword" onclick="togglePasswordVisibility()" title="Tampilkan / Sembunyikan Password" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: transparent; border: none; padding: 4px; color: #64748b; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: color 0.2s;">
            <!-- Eye Icon (Password Hidden) -->
            <svg id="iconEye" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <!-- Eye Off Icon (Password Visible) -->
            <svg id="iconEyeOff" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
              <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
          </button>
        </div>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem; margin: 2px 0;">
        <label style="display: flex; align-items: center; gap: 7px; cursor: pointer; color: #475569;">
          <input type="checkbox" name="remember" style="accent-color: #0e7482;"> Ingat saya di perangkat ini
        </label>
      </div>

      <button type="submit" style="padding: 13px; font-size: 0.95rem; font-weight: 700; background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%); color: #ffffff; border: none; border-radius: 14px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(14, 116, 130, 0.35); margin-top: 4px;">
        Masuk Sekarang →
      </button>
    </form>

    <div style="text-align: center; margin-top: 20px; font-size: 0.83rem; color: #64748b;">
      Kembali ke halaman utama? 
      <a href="{{ route('home') }}" style="color: #0e7482; font-weight: 700; text-decoration: none;">Landing Page</a>
    </div>

  </div>
</div>

<script>
function togglePasswordVisibility() {
  const pwdInput = document.getElementById('loginPassword');
  const iconEye = document.getElementById('iconEye');
  const iconEyeOff = document.getElementById('iconEyeOff');
  
  if (pwdInput.type === 'password') {
    pwdInput.type = 'text';
    iconEye.style.display = 'none';
    iconEyeOff.style.display = 'block';
  } else {
    pwdInput.type = 'password';
    iconEye.style.display = 'block';
    iconEyeOff.style.display = 'none';
  }
}

function fillCreds(email, password) {
  document.getElementById('loginEmail').value = email;
  document.getElementById('loginPassword').value = password;
  // Briefly focus the submit button
  document.querySelector('#formLogin button[type="submit"]').focus();
}
</script>
@endsection
