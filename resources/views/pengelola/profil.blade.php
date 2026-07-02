@extends('layouts.dashboard')

@section('title', 'Profil Pengelola')
@section('header-title', 'Kelola Profil Pengelola Kost')
@section('breadcrumb-active', 'Profil Saya')

@section('content')
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><i class="fa-solid fa-user-edit"></i> Perbarui Informasi Diri</h3>
    </div>
    
    <div class="box-body">
        <form action="{{ route('pengelola.profil.update') }}" method="post">
            @csrf
            
            <div class="grid-2">
                
                <div>
                    <h4 style="border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 15px; font-weight: 600; color: #555;">
                        <i class="fa-solid fa-key"></i> Data Akun & Akses
                    </h4>
                    
                    <div class="form-group">
                        <label>Nama Lengkap Pengelola</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Username (Tidak dapat diubah)</label>
                        <input type="text" class="form-control" value="{{ $user->username }}" style="background-color: #eee; cursor: not-allowed;" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Alamat Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Password Baru (Kosongkan jika tidak ingin diubah)</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                    </div>
                    
                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi password">
                    </div>
                </div>
                
                
                <div>
                    <h4 style="border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 15px; font-weight: 600; color: #555;">
                        <i class="fa-solid fa-id-card"></i> Data Identitas & Kontak
                    </h4>
                    
                    <div class="form-group">
                        <label>Nomor KTP (Kartu Tanda Penduduk)</label>
                        <input type="text" name="ktp_number" class="form-control" value="{{ old('ktp_number', $profil->ktp_number) }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nomor Telepon / WhatsApp (Aktif)</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $profil->phone) }}" required>
                        <small style="color: #888;">Gunakan nomor aktif WhatsApp untuk tombol hubungi pengelola.</small>
                    </div>
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 15px;">
                <label>Alamat Rumah Pengelola</label>
                <textarea name="address" class="form-control" rows="3" required>{{ old('address', $profil->address) }}</textarea>
            </div>
            
            <div class="box-footer" style="padding-left: 0; padding-right: 0; background: none;">
                <button type="submit" class="btn-custom btn-success-custom" style="padding: 8px 20px; font-weight: bold;">
                    <i class="fa-solid fa-save"></i> Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
