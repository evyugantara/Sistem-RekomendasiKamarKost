@extends('layouts.dashboard')

@section('title', 'Dashboard Pengelola')
@section('header-title', 'Dashboard Pengelola Kost')
@section('breadcrumb-active', 'Dashboard')

@section('content')
<div class="box box-primary">
    <div class="box-body" style="padding: 20px;">
        <h2 style="font-weight: 300; margin-bottom: 10px;">Halo Bpk/Ibu, <strong>{{ auth()->user()->name }}</strong>!</h2>
        <p style="color: #666; font-size: 15px;">Selamat datang di dashboard Pengelola Kost. Di sini Anda dapat menambahkan kost milik Anda, melengkapi spesifikasi kriteria kost secara mendetail, mengunggah galeri foto, serta memantau seberapa banyak mahasiswa yang berminat menghubungi Anda.</p>
    </div>
</div>

<!-- Row Metrik / KPI Cards -->
<div class="metrics-row">
    <!-- Card Total Kost -->
    <div class="metric-card blue">
        <div class="inner">
            <h3>{{ $totalKosts }}</h3>
            <p>Total Kost Saya</p>
        </div>
        <div class="icon">
            <i class="fa fa-house-chimney-window"></i>
        </div>
    </div>
    
    <!-- Card Total Kamar -->
    <div class="metric-card green">
        <div class="inner">
            <h3>{{ $totalKamars }}</h3>
            <p>Total Kamar Kost</p>
        </div>
        <div class="icon">
            <i class="fa fa-bed"></i>
        </div>
    </div>
    
    <!-- Card Kamar Tersedia -->
    <div class="metric-card yellow">
        <div class="inner">
            <h3>{{ $kamarTersedia }}</h3>
            <p>Total Kamar Tersedia</p>
        </div>
        <div class="icon">
            <i class="fa-solid fa-door-open"></i>
        </div>
    </div>

    <!-- Card Kamar Terisi -->
    <div class="metric-card red">
        <div class="inner">
            <h3>{{ $kamarTerisi }}</h3>
            <p>Kamar Terisi / Penuh</p>
        </div>
        <div class="icon">
            <i class="fa-solid fa-door-closed"></i>
        </div>
    </div>
</div>

<div class="grid-2" style="grid-template-columns: 1.5fr 1fr; gap: 20px;">
    <!-- Riwayat Mahasiswa Menghubungi -->
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa-solid fa-bell"></i> Riwayat Mahasiswa Menghubungi Kost Anda</h3>
        </div>
        <div class="box-body table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Mahasiswa</th>
                        <th>Kost yang Dituju</th>
                        <th>Tipe Kontak</th>
                        <th>Tanggal Klik</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentContacts as $index => $con)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $con->user->name }}</strong><br>
                                <span style="font-size: 11px; color: #777;">WA/Telp: {{ $con->user->profilMahasiswa->phone ?? '-' }}</span>
                            </td>
                            <td>
                                @if($con->kamar)
                                    <strong>{{ $con->kamar->name }}</strong><br>
                                    <span style="font-size: 11px; color: #777;">{{ $con->kamar->kost->name }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($con->contact_type === 'whatsapp')
                                    <span class="badge badge-success"><i class="fa-brands fa-whatsapp"></i> WhatsApp</span>
                                @else
                                    <span class="badge badge-primary"><i class="fa fa-phone"></i> Telepon</span>
                                @endif
                            </td>
                            <td>{{ $con->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #888; padding: 20px;">Belum ada mahasiswa yang mengklik tombol hubungi pada kost Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pintasan Aksi Pengelola -->
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa-solid fa-rocket"></i> Menu Pintasan Cepat</h3>
        </div>
        <div class="box-body" style="display: flex; flex-direction: column; gap: 10px;">
            <a href="{{ route('pengelola.kost.tambah') }}" class="btn-custom btn-primary-custom" style="padding: 12px; font-weight: bold;">
                <i class="fa fa-plus"></i> Tambah Kost Baru
            </a>
            <a href="{{ route('pengelola.kost') }}" class="btn-custom" style="background-color: #f4f4f4; border: 1px solid #ddd; color: #333; padding: 12px; font-weight: bold;">
                <i class="fa fa-home"></i> Kelola Kost & Fasilitas
            </a>
            <a href="{{ route('pengelola.profil') }}" class="btn-custom" style="background-color: #f4f4f4; border: 1px solid #ddd; color: #333; padding: 12px; font-weight: bold;">
                <i class="fa fa-user-edit"></i> Perbarui Profil Saya
            </a>
        </div>
    </div>
</div>
@endsection
