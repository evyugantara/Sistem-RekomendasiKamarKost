@extends('layouts.dashboard')

@section('title', 'Kelola Kost Saya')
@section('header-title', 'Daftar Kost Saya')
@section('breadcrumb-active', 'Kelola Kost')

@section('styles')
<style>
    
    .action-dropdown {
        position: relative;
        display: inline-block;
    }
    .action-dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        background-color: 
        min-width: 170px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.15);
        z-index: 100;
        border: 1px solid 
        border-radius: 4px;
        text-align: left;
        margin-top: 2px;
    }
    .action-dropdown-content a, .action-dropdown-content button {
        color: 
        padding: 10px 16px;
        text-decoration: none;
        display: block;
        font-size: 13px;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
        box-sizing: border-box;
        transition: background-color 0.2s;
    }
    .action-dropdown-content a:hover, .action-dropdown-content button:hover {
        background-color: 
    }
    .action-dropdown-content hr {
        margin: 4px 0;
        border: 0;
        border-top: 1px solid 
    }
    .action-dropdown:hover .action-dropdown-content {
        display: block;
    }

    
    @media (min-width: 768px) {
        .box-body.table-responsive {
            overflow: visible !important;
        }
    }
</style>
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><i class="fa-solid fa-house-chimney-window"></i> Daftar Kost Milik Anda</h3>
        <div class="box-tools">
            <a href="{{ route('pengelola.kost.tambah') }}" class="btn-custom btn-primary-custom btn-xs">
                <i class="fa fa-plus"></i> Tambah Kost Baru
            </a>
        </div>
    </div>
    
    <div class="box-body table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 100px;">Foto</th>
                    <th>Nama Kost</th>
                    <th>Kampus Terdekat</th>
                    <th>Harga Sewa</th>
                    <th>Koordinat Lokasi</th>
                    <th style="width: 320px; text-align: center;">Aksi Pengelolaan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kosts as $index => $kost)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <img src="{{ asset($kost->fotoUtama()) }}" alt="{{ $kost->name }}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 3px; border: 1px solid #ddd;">
                        </td>
                        <td>
                            <strong>{{ $kost->name }}</strong><br>
                            <span style="font-size: 11.5px; color: #777;"><i class="fa fa-map-marker-alt"></i> {{ Str::limit($kost->address, 50) }}</span>
                        </td>
                        <td>{{ $kost->kampus->name }}</td>
                        <td>
                            <span style="font-weight: 700; color: var(--card-green);">
                                @if($kost->kamars->isNotEmpty())
                                    @php
                                        $minPrice = $kost->kamars->min('price');
                                        $maxPrice = $kost->kamars->max('price');
                                    @endphp
                                    @if($minPrice == $maxPrice)
                                        Rp {{ number_format($minPrice, 0, ',', '.') }}
                                    @else
                                        Rp {{ number_format($minPrice, 0, ',', '.') }} - {{ number_format($maxPrice, 0, ',', '.') }}
                                    @endif
                                @else
                                    Rp {{ number_format($kost->price, 0, ',', '.') }}
                                @endif
                            </span>
                            <span style="font-size: 10px; color: #777;">/ bln</span>
                        </td>
                        <td style="font-family: monospace; font-size: 12px;">
                            Lat: {{ $kost->latitude }}<br>
                            Lng: {{ $kost->longitude }}
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 5px; justify-content: center; align-items: center;">
                                
                                <a href="{{ route('pengelola.kost.kamar', $kost->id) }}" class="btn-custom btn-primary-custom btn-sm" style="background-color: #605ca8; border-color: #605ca8; font-weight: 600; padding: 6px 12px;">
                                    <i class="fa-solid fa-bed"></i> Kamar ({{ $kost->kamars->count() }})
                                </a>

                                
                                <div class="action-dropdown">
                                    <button class="btn-custom btn-sm" style="background-color: #f4f4f4; border: 1px solid #ddd; color: #333; font-weight: 600; padding: 6px 12px;">
                                        Lainnya <i class="fa fa-caret-down"></i>
                                    </button>
                                    <div class="action-dropdown-content">
                                        <a href="{{ route('pengelola.kost.edit', $kost->id) }}">
                                            <i class="fa-solid fa-edit" style="color: #3c8dbc; width: 18px;"></i> Edit Detail
                                        </a>
                                        <a href="{{ route('pengelola.kost.fasilitas', $kost->id) }}">
                                            <i class="fa-solid fa-list-check" style="color: #f39c12; width: 18px;"></i> Fasilitas
                                        </a>
                                        <a href="{{ route('pengelola.kost.fotos', $kost->id) }}">
                                            <i class="fa-solid fa-images" style="color: #00a65a; width: 18px;"></i> Galeri Foto
                                        </a>
                                        <hr>
                                        <form id="deleteForm_{{ $kost->id }}" action="{{ route('pengelola.kost.hapus', $kost->id) }}" method="post" style="margin: 0; padding: 0;">
                                            @csrf
                                            <button type="button" onclick="confirmDeleteKost({{ $kost->id }})" style="color: #dd4b39; font-weight: 600;">
                                                <i class="fa-solid fa-trash" style="width: 18px;"></i> Hapus Kost
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #888; padding: 30px;">
                            <i class="fa-solid fa-house-circle-xmark" style="font-size: 40px; color: #ccc; margin-bottom: 10px; display: block;"></i>
                            Anda belum mendaftarkan kost apa pun. Silakan klik tombol <strong>Tambah Kost Baru</strong> di atas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@section('scripts')
<script>
    function confirmDeleteKost(id) {
        Swal.fire({
            title: 'Hapus Kost?',
            text: "Seluruh data kamar, foto, dan relasi kost ini akan dihapus secara permanen dari sistem!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dd4b39',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm_' + id).submit();
            }
        });
    }
</script>
@endsection

@endsection
