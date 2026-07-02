@extends('layouts.dashboard')

@section('title', 'Kamar Favorit Saya')
@section('header-title', 'Kamar Favorit Saya')
@section('breadcrumb-active', 'Kamar Favorit')

@section('content')
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><i class="fa-solid fa-heart" style="color: #dd4b39;"></i> Daftar Kamar yang Anda Simpan</h3>
    </div>
    
    <div class="box-body">
        @if($favorits->isEmpty())
            <div style="text-align: center; padding: 40px 20px;">
                <i class="fa-solid fa-heart-crack" style="font-size: 50px; color: #ccc; margin-bottom: 15px;"></i>
                <p style="color: #666; font-size: 15px;">Anda belum menyimpan kamar apa pun ke daftar favorit.</p>
                <a href="{{ route('home') }}" class="btn-custom btn-primary-custom" style="margin-top: 15px; font-weight: bold;"><i class="fa fa-search"></i> Telusuri Kost Sekarang</a>
            </div>
        @else
            <!-- Grid Card Favorit -->
            <div class="grid-3">
                @foreach($favorits as $fav)
                    @php
                        $kamar = $fav->kamar;
                        $kost = $kamar->kost;
                    @endphp
                    <div class="kost-card" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #d2d6de;">
                        <img src="{{ asset($kamar->fotoKamar()) }}" alt="{{ $kamar->name }}">
                        
                        <div class="body" style="padding: 15px;">
                            <div style="margin-bottom: 8px;">
                                @php
                                    $jenis = $kost->atributKost->where('kriteria.name', 'Jenis Kost')->first();
                                    $jenisVal = $jenis ? $jenis->opsiKriteria->value : 'Umum';
                                @endphp
                                @if($jenisVal == 'Putra')
                                    <span class="badge" style="background-color: #007bff; color: #fff;">Kost Putra</span>
                                @elseif($jenisVal == 'Putri')
                                    <span class="badge" style="background-color: #e83e8c; color: #fff;">Kost Putri</span>
                                @else
                                    <span class="badge" style="background-color: #28a745; color: #fff;">Kost Campur</span>
                                @endif
                            </div>
                            
                            <h3 class="title" style="font-size: 16px; margin: 0 0 5px 0; font-weight: 700;">
                                {{ $kamar->name }}
                                <span style="font-size: 12.5px; color: #666; display: block; font-weight: normal; margin-top: 2px;">{{ $kost->name }}</span>
                            </h3>
                            <p class="price" style="font-size: 15px; color: var(--card-green); font-weight: bold; margin-bottom: 8px;">
                                Rp {{ number_format($kamar->price, 0, ',', '.') }} <span style="font-size: 11px; font-weight: normal; color: #777;">/ bln</span>
                            </p>
                            <p class="address" style="font-size: 11.5px; color: #777; margin-bottom: 15px; min-height: 35px;"><i class="fa fa-map-marker-alt"></i> {{ Str::limit($kost->address, 70) }}</p>
                            
                            <div style="border-top: 1px solid #eee; padding-top: 10px; display: flex; justify-content: space-between; align-items: center; gap: 5px;">
                                <form action="{{ route('mahasiswa.favorit.toggle', $kamar->id) }}" method="post" style="flex: 1;">
                                    @csrf
                                    <button type="submit" class="btn-custom btn-danger-custom btn-xs" style="width: 100%;"><i class="fa fa-trash"></i> Hapus</button>
                                </form>
                                <a href="{{ route('kost.detail', $kost->id) }}?kamar={{ $kamar->id }}" class="btn-custom btn-primary-custom btn-xs" style="flex: 1.2; text-align: center;"><i class="fa fa-eye"></i> Detail Peta</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
