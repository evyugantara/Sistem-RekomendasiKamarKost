@extends('layouts.dashboard')

@section('title', 'Dashboard Mahasiswa')
@section('header-title', 'Dashboard Mahasiswa')
@section('breadcrumb-active', 'Dashboard')

@section('content')
<div class="box box-primary" style="margin-bottom: 20px;">
    <div class="box-body" style="padding: 20px;">
        <h2 style="font-weight: 300; margin-bottom: 10px;">Halo, <strong>{{ auth()->user()->name }}</strong>!</h2>
        <p style="color: #666; font-size: 15px;">Selamat datang di Sistem Rekomendasi Kost Mahasiswa Berbasis Web. Anda dapat mengisi preferensi kriteria kost Anda untuk mendapatkan hasil rekomendasi kost dengan nilai kemiripan tertinggi menggunakan algoritma Content-Based Filtering.</p>
    </div>
</div>



<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><i class="fa-solid fa-user"></i> Informasi Profil & Akun Anda</h3>
    </div>
    <div class="box-body" style="padding: 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <h4 style="margin: 0 0 15px 0; color: var(--primary-color); border-bottom: 1px dashed #ddd; padding-bottom: 5px; font-weight: bold;">
                    <i class="fa-solid fa-id-card"></i> Profil Diri
                </h4>
                <table class="kost-specs-table" style="width: 100%;">
                    <tr>
                        <td style="width: 140px; color: #666; padding: 8px 0;">Nama Lengkap</td>
                        <td style="font-weight: 600; padding: 8px 0;">{{ auth()->user()->name }}</td>
                    </tr>
                    <tr>
                        <td style="color: #666; padding: 8px 0;">Jenis Kelamin</td>
                        <td style="font-weight: 600; padding: 8px 0;">{{ auth()->user()->profilMahasiswa->gender ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #666; padding: 8px 0;">Alamat Asal</td>
                        <td style="font-weight: 600; padding: 8px 0;">{{ auth()->user()->profilMahasiswa->address ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            
            <div>
                <h4 style="margin: 0 0 15px 0; color: var(--primary-color); border-bottom: 1px dashed #ddd; padding-bottom: 5px; font-weight: bold;">
                    <i class="fa-solid fa-address-book"></i> Kontak & Akun
                </h4>
                <table class="kost-specs-table" style="width: 100%;">
                    <tr>
                        <td style="width: 140px; color: #666; padding: 8px 0;">Username</td>
                        <td style="font-weight: 600; padding: 8px 0;">{{ auth()->user()->username }}</td>
                    </tr>
                    <tr>
                        <td style="color: #666; padding: 8px 0;">Email</td>
                        <td style="font-weight: 600; padding: 8px 0;">{{ auth()->user()->email }}</td>
                    </tr>
                    <tr>
                        <td style="color: #666; padding: 8px 0;">No. Telepon</td>
                        <td style="font-weight: 600; padding: 8px 0;">{{ auth()->user()->profilMahasiswa->phone ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div style="margin-top: 30px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
            <a href="{{ route('mahasiswa.rekomendasi') }}" class="btn-custom btn-primary-custom" style="padding: 12px 30px; font-size: 15px; font-weight: bold; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Mulai Cari Rekomendasi Kost
            </a>
            <a href="{{ route('mahasiswa.profil') }}" class="btn-custom" style="padding: 12px 30px; font-size: 15px; font-weight: bold; background-color: #f4f4f4; border: 1px solid #ddd; color: #333; margin-left: 10px; border-radius: 4px;">
                <i class="fa-solid fa-user-edit"></i> Edit Profil Saya
            </a>
        </div>
    </div>
</div>
@endsection
