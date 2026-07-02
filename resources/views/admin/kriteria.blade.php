@extends('layouts.dashboard')

@section('title', 'Kelola Kriteria Kost')
@section('header-title', 'Kelola Atribut & Kriteria Rekomendasi')
@section('breadcrumb-active', 'Kelola Kriteria')

@section('content')
<div class="grid-2" style="grid-template-columns: 0.9fr 1.3fr; gap: 20px;">
    
    <div>
        
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="fa-solid fa-list"></i> Kriteria Terdaftar</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Nama Kriteria</th>
                            <th>Kategori</th>
                            <th style="width: 80px; text-align: center;">Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kriterias as $index => $kr)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $kr->name }}</strong></td>
                                <td>
                                    @if($kr->category === 'umum')
                                        <span class="badge badge-primary">Umum</span>
                                    @elseif($kr->category === 'pribadi')
                                        <span class="badge badge-info">Pribadi</span>
                                    @else
                                        <span class="badge badge-success">Bersama</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <form action="{{ route('admin.kriteria.hapus', $kr->id) }}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kriteria ini beserta seluruh opsi pilihannya?')">
                                        @csrf
                                        <button type="submit" class="btn-custom btn-danger-custom btn-xs" style="padding: 2px 6px;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: #888; padding: 15px;">Belum ada kriteria kost terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="fa-solid fa-plus-circle"></i> Tambah Kriteria Baru</h3>
            </div>
            <div class="box-body">
                <form action="{{ route('admin.kriteria.simpan') }}" method="post">
                    @csrf
                    
                    <div class="form-group">
                        <label>Nama Kriteria</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Dapur Bersama" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Kategori Penempatan</label>
                        <select name="category" class="form-control" required>
                            <option value="umum">Kriteria Umum</option>
                            <option value="pribadi">Fasilitas Pribadi</option>
                            <option value="bersama">Fasilitas Bersama</option>
                        </select>
                    </div>
                    
                    
                    <input type="hidden" name="type" value="select">
                    
                    <button type="submit" class="btn-custom btn-primary-custom" style="width: 100%; font-weight: bold; margin-top: 10px;">
                        <i class="fa fa-save"></i> Simpan Kriteria
                    </button>
                </form>
            </div>
        </div>
    </div>

    
    <div>
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="fa-solid fa-tags"></i> Opsi Pilihan Kriteria</h3>
            </div>
            <div class="box-body">
                <p style="color: #666; font-size: 13.5px; margin-bottom: 15px;">Di bawah ini adalah opsi pilihan (atribut) untuk setiap kriteria. Di halaman Mahasiswa dan Pengelola, opsi-opsi ini akan otomatis dirender sebagai **Checkbox Pilihan** yang rapi dan hemat ruang.</p>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    @forelse($kriterias as $kr)
                        <div style="border: 1px solid #e2e8f0; padding: 12px; border-radius: 8px; background-color: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.01); display: flex; flex-direction: column; justify-content: space-between; min-height: 160px;">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">
                                    <strong style="color: #0f172a; font-size: 13px;">
                                        {{ $kr->name }} 
                                        <span style="font-weight: normal; font-size: 10px; color: #64748b;">
                                            ({{ strtoupper($kr->category) }})
                                        </span>
                                    </strong>
                                </div>
                                
                                
                                <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 10px;">
                                    @forelse($kr->opsiKriteria as $opsi)
                                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 2px 0;">
                                            <div style="display: flex; align-items: center; gap: 6px;">
                                                <input type="checkbox" checked disabled style="width: 14px; height: 14px; accent-color: #3c8dbc; cursor: not-allowed;">
                                                <span style="font-size: 12.5px; color: #334155;">{{ $opsi->value }}</span>
                                            </div>
                                            <form action="{{ route('admin.kriteria.opsi.hapus', $opsi->id) }}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus opsi pilihan ini?')">
                                                @csrf
                                                <button type="submit" style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 2px; font-size: 12px;" title="Hapus Opsi">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @empty
                                        <span style="color: #94a3b8; font-style: italic; font-size: 12px;">Belum ada opsi pilihan.</span>
                                    @endforelse
                                </div>
                            </div>
                            
                            
                            <form action="{{ route('admin.kriteria.opsi.simpan', $kr->id) }}" method="post" style="display: flex; gap: 4px; margin-top: auto; border-top: 1px solid #f1f5f9; padding-top: 8px;">
                                @csrf
                                <input type="text" name="value" class="form-control" placeholder="Tambah opsi..." style="height: 26px; font-size: 11px; padding: 2px 6px;" required>
                                <button type="submit" class="btn-custom btn-primary-custom" style="padding: 0 10px; height: 26px; font-size: 11px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div style="grid-column: span 2; text-align: center; color: #888; padding: 10px;">Tidak ada kriteria untuk dikelola opsinya.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
