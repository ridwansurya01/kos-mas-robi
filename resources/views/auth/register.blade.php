@extends('layouts.app')

@section('title', 'Daftar Akun Anak Kos - Kos Mas Robi')

@section('content')
<div style="min-height: 100vh; min-height: 100dvh; width: 100%; display: flex; align-items: center; justify-content: center; padding: 24px; background: radial-gradient(circle at 15% 15%, rgba(14, 116, 130, 0.10) 0%, transparent 45%), radial-gradient(circle at 85% 85%, rgba(244, 63, 94, 0.06) 0%, transparent 45%), #f8fafc;">
  <div style="width: 100%; max-width: 480px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 28px; padding: 36px 30px; box-shadow: 0 16px 40px -8px rgba(3, 35, 40, 0.10);">

    <div style="text-align: center; margin-bottom: 26px;">
      <div style="margin: 0 auto 14px; width: 56px; height: 56px; border-radius: 18px; background: linear-gradient(135deg, #0a5661, #0e7482); display: flex; align-items: center; justify-content: center; color: #ffffff; box-shadow: 0 8px 20px rgba(14, 116, 130, 0.35);">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
        </svg>
      </div>
      <h2 style="font-size: 1.55rem; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Daftar Akun Anak Kos</h2>
      <p style="font-size: 0.85rem; color: #64748b;">Akses tagihan, info kos, dan layanan perbaikan</p>
    </div>

    @if ($errors->any())
      <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 14px; border-radius: 14px; font-size: 0.82rem; margin-bottom: 16px; font-weight: 500;">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('register.post') }}" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
      @csrf
      <div class="form-group" style="margin-bottom: 0;">
        <label for="regName" style="display: block; font-size: 0.82rem; font-weight: 600; color: #334155; margin-bottom: 6px;">Nama Lengkap *</label>
        <input type="text" id="regName" name="name" class="form-control" placeholder="Nama Anda" value="{{ old('name') }}" required style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px 16px; font-size: 0.9rem; color: #0f172a; width: 100%;">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div class="form-group" style="margin-bottom: 0;">
          <label for="regEmail" style="display: block; font-size: 0.82rem; font-weight: 600; color: #334155; margin-bottom: 6px;">Email *</label>
          <input type="email" id="regEmail" name="email" class="form-control" placeholder="email@gmail.com" value="{{ old('email') }}" required style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px 16px; font-size: 0.9rem; color: #0f172a; width: 100%;">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
          <label for="regPhone" style="display: block; font-size: 0.82rem; font-weight: 600; color: #334155; margin-bottom: 6px;">No. WhatsApp *</label>
          <input type="tel" id="regPhone" name="phone" class="form-control" placeholder="0812xxxxxxxx" value="{{ old('phone') }}" required style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px 16px; font-size: 0.9rem; color: #0f172a; width: 100%;">
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 0;">
        <label for="regRoom" style="display: block; font-size: 0.82rem; font-weight: 600; color: #334155; margin-bottom: 6px;">Pilih Kamar yang Dihuni (Opsional)</label>
        <select id="regRoom" name="room_id" class="form-control" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px 16px; font-size: 0.9rem; color: #0f172a; width: 100%;">
          <option value="">-- Belum Memilih Kamar --</option>
          @foreach($availableRooms as $ar)
            <option value="{{ $ar->id }}">Kamar #{{ $ar->number }} - {{ $ar->type }}</option>
          @endforeach
        </select>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div class="form-group" style="margin-bottom: 0;">
          <label for="regPassword" style="display: block; font-size: 0.82rem; font-weight: 600; color: #334155; margin-bottom: 6px;">Kata Sandi *</label>
          <input type="password" id="regPassword" name="password" class="form-control" placeholder="Min 6 karakter" required style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px 16px; font-size: 0.9rem; color: #0f172a; width: 100%;">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
          <label for="regPasswordConf" style="display: block; font-size: 0.82rem; font-weight: 600; color: #334155; margin-bottom: 6px;">Ulangi Sandi *</label>
          <input type="password" id="regPasswordConf" name="password_confirmation" class="form-control" placeholder="Ulangi sandi" required style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px 16px; font-size: 0.9rem; color: #0f172a; width: 100%;">
        </div>
      </div>

      <button type="submit" style="padding: 13px; font-size: 0.95rem; font-weight: 700; background: linear-gradient(135deg, #0a5661 0%, #0e7482 100%); color: #ffffff; border: none; border-radius: 14px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(14, 116, 130, 0.35); margin-top: 6px;">
        Buat Akun Sekarang
      </button>
    </form>

    <div style="text-align: center; margin-top: 22px; font-size: 0.83rem; color: #64748b;">
      Sudah punya akun? 
      <a href="{{ route('login') }}" style="color: #0e7482; font-weight: 700;">Masuk di sini</a>
    </div>

  </div>
</div>
@endsection
