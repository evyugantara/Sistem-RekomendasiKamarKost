<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Sistem Rekomendasi Kost Penghuni</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Style CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard-style.css') }}?v={{ time() }}">
</head>
<body style="background-color: var(--body-bg);">
    
    <!-- Header Guest Nav -->
    <nav class="guest-navbar">
        <div class="brand">
            <i class="fa-solid fa-house-laptop" style="color: #3c8dbc;"></i> RUMAH KOST CBF
        </div>
        <div class="nav-links">
            <a href="{{ route('home') }}">Beranda</a>
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn-custom btn-primary-custom" style="color: #fff;">Dashboard Admin</a>
                @elseif(auth()->user()->role === 'pengelola')
                    <a href="{{ route('pengelola.dashboard') }}" class="btn-custom btn-success-custom" style="color: #fff;">Dashboard Pengelola</a>
                @else
                    <a href="{{ route('mahasiswa.dashboard') }}" class="btn-custom btn-primary-custom" style="color: #fff;">Dashboard Penghuni</a>
                @endif
            @else
                <a href="{{ route('login') }}">Masuk</a>
                <a href="{{ route('register') }}" class="btn-custom btn-primary-custom" style="color: #fff;">Registrasi</a>
            @endauth
        </div>
    </nav>
 
    <!-- Hero Section -->
    <header class="hero-section">
        <h1>Temukan Kost Terbaik Sesuai Preferensi Anda</h1>
        <p>Gunakan Sistem pencarian dan rekomendasi kost dengan menggunakan metode Content-Based Filtering untuk membantu Anda menemukan kost terbaik di sekitar kampus secara mudah dan cepat.</p>
        @guest
            <a href="{{ route('register') }}" class="btn-custom btn-primary-custom" style="font-size: 16px; padding: 10px 20px; font-weight: bold;">Dapatkan Rekomendasi Kost Sekarang</a>
        @else
            @if(auth()->user()->role === 'mahasiswa')
                <a href="{{ route('mahasiswa.rekomendasi') }}" class="btn-custom btn-primary-custom" style="font-size: 16px; padding: 10px 20px; font-weight: bold;">Dapatkan Rekomendasi Kost Sekarang</a>
            @endif
        @endguest
    </header>

    <!-- Search & Filter Box -->
    <section class="search-filter-box">
        <form action="{{ route('home') }}" method="GET">
            <div class="search-filter-grid">
                <div class="form-group" style="margin-bottom: 0;">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama kost atau alamat..." value="{{ request('search') }}">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <select name="jenis" class="form-control">
                        <option value="">-- Semua Jenis --</option>
                        <option value="Putra" {{ request('jenis') == 'Putra' ? 'selected' : '' }}>Putra</option>
                        <option value="Putri" {{ request('jenis') == 'Putri' ? 'selected' : '' }}>Putri</option>
                        <option value="Campur" {{ request('jenis') == 'Campur' ? 'selected' : '' }}>Campur</option>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <select name="harga" class="form-control">
                        <option value="">-- Semua Harga --</option>
                        <option value="under_500" {{ request('harga') == 'under_500' ? 'selected' : '' }}>&lt; Rp 500.000 / bln</option>
                        <option value="500_1m" {{ request('harga') == '500_1m' ? 'selected' : '' }}>Rp 500k - Rp 1 Juta</option>
                        <option value="1m_15m" {{ request('harga') == '1m_15m' ? 'selected' : '' }}>Rp 1 Juta - Rp 1.5 Juta</option>
                        <option value="above_15m" {{ request('harga') == 'above_15m' ? 'selected' : '' }}>&gt; Rp 1.5 Juta / bln</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-custom btn-primary-custom" style="flex: 1;">Cari</button>
                    @if(request()->anyFilled(['search', 'jenis', 'harga']))
                        <a href="{{ route('home') }}" class="btn-custom" style="background-color: #ddd; color: #333;">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </section>

    <!-- Kost Grid Section -->
    <main>
        <div class="content-header" style="padding: 0 30px 10px 30px; margin-bottom: 10px;">
            <h2>Daftar Kost Tersedia ({{ $kosts->count() }})</h2>
        </div>
        
        @if($kosts->isEmpty())
            <div style="text-align: center; padding: 40px; background-color: #ffffff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin: 0 30px 40px 30px;">
                <i class="fa-solid fa-house-circle-exclamation" style="font-size: 50px; color: #ccc; margin-bottom: 15px;"></i>
                <p style="color: #666; font-size: 16px;">Tidak ada data kost yang sesuai dengan kriteria pencarian Anda.</p>
            </div>
        @else
            <div class="kost-grid">
                @foreach($kosts as $k)
                    <article class="kost-card">
                        <!-- Tampilkan foto utama atau default -->
                        <img src="{{ $k->fotoUtama() }}" alt="{{ $k->name }}">
                        
                        <div class="body">
                            <!-- Badge Jenis Kost -->
                            <div style="margin-bottom: 8px;">
                                @php
                                    $jenis = $k->atributKost->where('kriteria.name', 'Jenis Kost')->first();
                                    $jenisVal = $jenis ? $jenis->opsiKriteria->value : 'Umum';
                                @endphp
                                @if($jenisVal == 'Putra')
                                    <span class="badge" style="background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;"><i class="fa fa-male" style="color: #0369a1;"></i> Kost Putra</span>
                                @elseif($jenisVal == 'Putri')
                                    <span class="badge" style="background-color: #fce7f3; color: #be185d; border: 1px solid #fbcfe8;"><i class="fa fa-female" style="color: #be185d;"></i> Kost Putri</span>
                                @else
                                    <span class="badge" style="background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;"><i class="fa fa-users" style="color: #15803d;"></i> Kost Campur</span>
                                @endif
                                

                            </div>
                            
                            <h3 class="title">{{ $k->name }}</h3>
                            <p class="price">
                                @if($k->kamars->isNotEmpty())
                                    @php
                                        $minPrice = $k->kamars->min('price');
                                        $maxPrice = $k->kamars->max('price');
                                    @endphp
                                    @if($minPrice == $maxPrice)
                                        Rp {{ number_format($minPrice, 0, ',', '.') }}
                                    @else
                                        Rp {{ number_format($minPrice, 0, ',', '.') }} - {{ number_format($maxPrice, 0, ',', '.') }}
                                    @endif
                                @else
                                    Rp {{ number_format($k->price, 0, ',', '.') }}
                                @endif
                                <span style="font-size: 11px; font-weight: normal; color: #777;">/ bulan</span>
                            </p>
                            <p class="address"><i class="fa fa-map-marker-alt"></i> {{ Str::limit($k->address, 65) }}</p>
                            
                            <!-- Badges Facilities -->
                            <div class="badges">
                                @php
                                    $wifi = $k->atributKost->where('kriteria.name', 'Fasilitas Wi-Fi')->first();
                                    $ac = $k->atributKost->where('kriteria.name', 'AC (Pendingin Ruangan)')->first();
                                    $km = $k->atributKost->where('kriteria.name', 'Kamar Mandi Dalam')->first();
                                @endphp
                                @if($wifi && $wifi->opsiKriteria->value === 'Ada Wi-Fi')
                                    <span class="badge" style="background-color: #e2f0fe; color: #1a73e8; border: 1px solid #cce4fc;"><i class="fa fa-wifi"></i> WiFi</span>
                                @endif
                                @if($ac && $ac->opsiKriteria->value === 'Ada AC')
                                    <span class="badge" style="background-color: #e6fcf5; color: #0ca678; border: 1px solid #c3fae8;"><i class="fa fa-wind"></i> AC</span>
                                @endif
                                @if($km && $km->opsiKriteria->value === 'Ada Kamar Mandi Dalam')
                                    <span class="badge" style="background-color: #fff0f6; color: #d6336c; border: 1px solid #ffdeeb;"><i class="fa fa-bath"></i> KM Dalam</span>
                                @endif
                            </div>
                            
                            <div class="footer" style="display: grid; grid-template-columns: 1fr auto; gap: 8px; align-items: center;">
                                <a href="{{ route('kost.detail', $k->id) }}" class="btn-custom btn-primary-custom btn-sm" style="text-align: center;">
                                    <i class="fa fa-eye"></i> Lihat Detail Kost
                                </a>
                                <!-- Tombol Rute Kost → UNSUR langsung di Google Maps -->
                                <a href="https://www.google.com/maps/dir/?api=1&origin={{ $k->latitude }},{{ $k->longitude }}&destination=-6.81245,107.14090&travelmode=driving"
                                   target="_blank"
                                   title="Rute Kost ke Kampus UNSUR"
                                   style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; background-color:#34A853; color:#fff; border-radius:4px; font-size:16px; flex-shrink:0; text-decoration:none; transition: background 0.2s;">
                                    <i class="fa-solid fa-route"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </main>


</body>
</html>
