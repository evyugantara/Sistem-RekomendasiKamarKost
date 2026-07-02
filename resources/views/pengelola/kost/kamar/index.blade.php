@extends('layouts.dashboard')

@section('title', 'Kelola Kamar - ' . $kost->name)
@section('header-title', 'Kelola Kamar Kost')
@section('breadcrumb-active', 'Kelola Kamar')

@section('content')
<div style="margin-bottom: 15px;">
    <a href="{{ route('pengelola.kost') }}" class="btn-custom" style="background-color: #ddd; color: #333; font-weight: 600;">
        <i class="fa fa-arrow-left"></i> Kembali ke Daftar Kost
    </a>
</div>

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><i class="fa-solid fa-bed"></i> Daftar Kamar untuk: <strong>{{ $kost->name }}</strong></h3>
        <div class="box-tools">
            <a href="{{ route('pengelola.kost.kamar.tambah', $kost->id) }}" class="btn-custom btn-primary-custom btn-xs">
                <i class="fa fa-plus"></i> Tambah Kamar Baru
            </a>
        </div>
    </div>
    
    <div class="box-body table-responsive">
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 15px; border-radius: 3px; padding: 10px 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">Foto Kamar</th>
                    <th>Nama/Nomor Kamar</th>
                    <th>Harga Sewa Bulanan</th>
                    <th>Status Ketersediaan</th>
                    <th>Spesifikasi Fasilitas Kamar (Pribadi)</th>
                    <th style="width: 180px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kamars as $index => $kamar)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <img src="{{ asset($kamar->fotoKamar()) }}" alt="{{ $kamar->name }}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 3px; border: 1px solid #ddd;">
                        </td>
                        <td>
                            <strong>{{ $kamar->name }}</strong>
                            @if($kamar->description)
                                <p style="font-size: 11px; color: #777; margin-top: 3px; line-height: 1.3;">{{ Str::limit($kamar->description, 80) }}</p>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: 700; color: var(--card-green);">
                                Rp {{ number_format($kamar->price, 0, ',', '.') }}
                            </span>
                            <span style="font-size: 10px; color: #777;">/ bln</span>
                        </td>
                        <td>
                            @if($kamar->status === 'tersedia')
                                <span class="badge badge-success" style="padding: 5px 10px; font-weight: 600;"><i class="fa-solid fa-circle-check"></i> Tersedia</span>
                            @else
                                <span class="badge badge-danger" style="padding: 5px 10px; font-weight: 600;"><i class="fa-solid fa-circle-xmark"></i> Terisi</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                @forelse($kamar->atributKamar as $attr)
                                    <span class="badge badge-info" style="font-size: 10px; padding: 2px 6px;">{{ $attr->opsiKriteria->value }}</span>
                                @empty
                                    <span style="color: #aaa; font-style: italic; font-size: 11.5px;">Belum diatur</span>
                                @endforelse
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 5px; justify-content: center;">
                                <!-- Edit Kamar -->
                                <a href="{{ route('pengelola.kost.kamar.edit', [$kost->id, $kamar->id]) }}" class="btn-custom btn-primary-custom btn-xs" style="padding: 5px 10px;">
                                    <i class="fa-solid fa-edit"></i> Edit
                                </a>
                                
                                <!-- Hapus Kamar -->
                                <form action="{{ route('pengelola.kost.kamar.hapus', [$kost->id, $kamar->id]) }}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kamar ini secara permanen?')" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-custom btn-danger-custom btn-xs" style="padding: 5px 10px;">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #888; padding: 40px 15px;">
                            <i class="fa-solid fa-bed" style="font-size: 48px; color: #ccc; margin-bottom: 12px; display: block;"></i>
                            Belum ada kamar terdaftar untuk kost ini.<br>
                            Silakan klik tombol <strong>Tambah Kamar Baru</strong> di kanan atas untuk mendaftarkan unit kamar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
