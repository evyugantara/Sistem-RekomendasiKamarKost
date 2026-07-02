@extends('layouts.dashboard')

@section('title', 'Edit Kamar - ' . $kamar->name)
@section('header-title', 'Edit Data Kamar')
@section('breadcrumb-active', 'Edit Kamar')

@section('styles')
<style>
    .check-card {
        background: 
        border: 1px solid 
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .check-card strong {
        display: block;
        font-size: 13px;
        color: 
        margin-bottom: 10px;
        border-bottom: 1px solid 
        padding-bottom: 5px;
    }
    .check-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 3px 0;
    }
    .check-item input[type="checkbox"] {
        width: 16px; height: 16px;
        cursor: pointer;
        accent-color: 
    }
    .check-item label {
        margin-bottom: 0;
        font-size: 13px;
        font-weight: 500;
        color: 
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<form action="{{ route('pengelola.kost.kamar.update', [$kost->id, $kamar->id]) }}" method="post" enctype="multipart/form-data">
    @csrf
    
    <div style="display: grid; grid-template-columns: 1fr 1.4fr; gap: 20px; align-items: start;">

        
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="fa-solid fa-edit"></i> Informasi Kamar</h3>
                <span style="font-size: 12px; color: #888;">Kost: <strong>{{ $kost->name }}</strong></span>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label>Nama / Nomor Kamar <span style="color: red;">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Kamar A1, Kamar Deluxe" value="{{ old('name', $kamar->name) }}" required>
                </div>

                <div class="form-group">
                    <label>Harga Sewa Bulanan (Rupiah) <span style="color: red;">*</span></label>
                    <input type="number" name="price" class="form-control" placeholder="Contoh: 850000" value="{{ old('price', (int)$kamar->price) }}" required>
                </div>

                <div class="form-group">
                    <label>Status Ketersediaan <span style="color: red;">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="tersedia" {{ old('status', $kamar->status) === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="terisi" {{ old('status', $kamar->status) === 'terisi' ? 'selected' : '' }}>Terisi</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Deskripsi Tambahan (Ukuran, dll)</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Ukuran 3x4m, ventilasi jendela menghadap ke luar...">{{ old('description', $kamar->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label>Foto Kamar (Opsional)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small style="color: #777; display: block; margin-top: 3px;">jpeg, png, jpg, webp. Maks. 2MB. Kosongkan jika tidak ingin mengubah foto.</small>

                    @if($kamar->image_path)
                        <div style="margin-top: 10px;">
                            <span style="display: block; font-size: 12px; color: #555; margin-bottom: 5px;">Foto Saat Ini:</span>
                            <img src="{{ asset($kamar->image_path) }}" alt="Foto Kamar" style="width: 150px; height: 110px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="fa-solid fa-list-check"></i> Fasilitas Kamar (Pribadi)</h3>
            </div>
            <div class="box-body">
                <p style="color: #777; font-size: 12.5px; margin-bottom: 15px; line-height: 1.4;">
                    <i class="fa-solid fa-circle-info" style="color: var(--primary-color);"></i>
                    Centang (☑) fasilitas yang tersedia di dalam kamar ini. Bisa lebih dari satu pilihan per kriteria.
                </p>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 12px;">
                    @foreach($kriterias as $k)
                        <div class="check-card">
                            <strong>{{ $k->name }}</strong>
                            @forelse($k->opsiKriteria as $o)
                                <div class="check-item">
                                    <input type="checkbox"
                                        name="fasilitas[{{ $k->id }}][]"
                                        id="fasilitas_{{ $o->id }}"
                                        value="{{ $o->id }}"
                                        {{ in_array($o->id, $activeOpts) ? 'checked' : '' }}>
                                    <label for="fasilitas_{{ $o->id }}">{{ $o->value }}</label>
                                </div>
                            @empty
                                <span style="color: #aaa; font-style: italic; font-size: 11.5px;">Belum ada opsi.</span>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 10px; margin-top: 5px;">
        <button type="submit" class="btn-custom btn-success-custom" style="padding: 10px 25px; font-weight: bold; font-size: 14px;">
            <i class="fa-solid fa-save"></i> Perbarui Data Kamar
        </button>
        <a href="{{ route('pengelola.kost.kamar', $kost->id) }}" class="btn-custom" style="background-color: #ddd; color: #333; padding: 10px 20px; font-size: 14px;">Kembali</a>
    </div>
</form>
@endsection
