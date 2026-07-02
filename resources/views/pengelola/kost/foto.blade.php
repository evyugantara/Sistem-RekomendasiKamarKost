@extends('layouts.dashboard')

@section('title', 'Galeri Foto Kost')
@section('header-title', 'Kelola Galeri Foto Kost')
@section('breadcrumb-active', 'Galeri Foto')

@section('content')
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><i class="fa-solid fa-images"></i> Galeri Foto: <strong>{{ $kost->name }}</strong></h3>
        <div class="box-tools">
            <a href="{{ route('pengelola.kost') }}" class="btn-custom btn-xs" style="background-color: #ddd; color: #333;"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>
    </div>
    
    <div class="box-body">
        <div class="grid-2" style="grid-template-columns: 1fr 1.5fr; gap: 20px;">
            
            <div style="background-color: #fcfcfc; border: 1px solid #ddd; padding: 20px; border-radius: 4px; height: fit-content;">
                <h4 style="font-weight: 700; font-size: 15px; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 15px; color: #555;">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Unggah Foto Baru
                </h4>
                
                <form action="{{ route('pengelola.kost.fotos.simpan', $kost->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group">
                        <label>Pilih File Foto</label>
                        <input type="file" name="image" class="form-control" style="padding: 5px; height: auto;" required>
                        <small style="color: #888; display: block; margin-top: 5px;">Format file: jpeg, png, jpg, gif. Ukuran maksimum: 2MB.</small>
                    </div>
                    
                    <button type="submit" class="btn-custom btn-primary-custom" style="width: 100%; font-weight: bold; margin-top: 10px;">
                        <i class="fa-solid fa-upload"></i> Unggah Foto
                    </button>
                </form>
            </div>
            
            
            <div>
                <h4 style="font-weight: 700; font-size: 15px; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 15px; color: #555;">
                    <i class="fa-solid fa-photo-film"></i> Daftar Foto Diunggah ({{ $kost->fotos->count() }})
                </h4>
                
                @if($kost->fotos->isEmpty())
                    <div style="text-align: center; padding: 40px; background-color: #fcfcfc; border: 1px dashed #ccc; border-radius: 4px;">
                        <i class="fa-regular fa-image" style="font-size: 40px; color: #ccc; margin-bottom: 10px; display: block;"></i>
                        Belum ada foto yang diunggah untuk kost ini.
                    </div>
                @else
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px;">
                        @foreach($kost->fotos as $foto)
                            <div style="border: 1px solid #ddd; border-radius: 4px; overflow: hidden; background-color: #fff; display: flex; flex-direction: column; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                <div style="height: 120px; overflow: hidden; position: relative;">
                                    <img src="{{ asset($foto->image_path) }}" alt="Foto Kost" style="width: 100%; height: 100%; object-fit: cover;">
                                    @if($foto->is_primary)
                                        <div style="position: absolute; top: 5px; left: 5px; background-color: var(--card-green); color: #fff; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 3px;">
                                            Foto Utama
                                        </div>
                                    @endif
                                </div>
                                
                                <div style="padding: 10px; display: flex; flex-direction: column; gap: 5px; margin-top: auto; border-top: 1px solid #eee; background-color: #fafafa;">
                                    @if(!$foto->is_primary)
                                        <form action="{{ route('pengelola.kost.fotos.utama', [$kost->id, $foto->id]) }}" method="post" style="width: 100%;">
                                            @csrf
                                            <button type="submit" class="btn-custom btn-xs" style="width: 100%; background-color: #3c8dbc; color: #fff;">
                                                <i class="fa-solid fa-star"></i> Set Utama
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('pengelola.kost.fotos.hapus', [$kost->id, $foto->id]) }}" method="post" style="width: 100%;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto ini?')">
                                            @csrf
                                            <button type="submit" class="btn-custom btn-danger-custom btn-xs" style="width: 100%;">
                                                <i class="fa-solid fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn-custom btn-xs" style="width: 100%; background-color: #eee; color: #999; cursor: not-allowed;" disabled>
                                            <i class="fa-solid fa-star"></i> Foto Utama Aktif
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
