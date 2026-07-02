<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $kost->name }} - Detail Kost</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WLEo=" crossorigin=""></script>

    <!-- Custom Style CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard-style.css') }}?v={{ time() }}">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ===== LAYOUT ===== */
        .detail-wrapper {
            max-width: 1180px;
            margin: 28px auto;
            padding: 0 16px;
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 20px;
            align-items: start;
        }

        /* ===== CARD ===== */
        .detail-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            margin-bottom: 18px;
        }
        .detail-card-header {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fafbfc;
        }
        .detail-card-header h3 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: #334155;
        }
        .detail-card-header i {
            color: #3c8dbc;
            font-size: 14px;
        }
        .detail-card-body {
            padding: 16px;
        }

        /* ===== FOTO GALERI ===== */
        .kost-main-img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            display: block;
            border-radius: 0;
        }
        .kost-thumbs {
            display: flex;
            gap: 8px;
            padding: 10px 12px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            overflow-x: auto;
        }
        .kost-thumbs img {
            width: 64px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: border-color 0.2s;
            flex-shrink: 0;
        }
        .kost-thumbs img:hover { border-color: #3c8dbc; }

        /* ===== KAMAR CARD ===== */
        .kamar-item {
            display: flex;
            gap: 12px;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            transition: all 0.2s;
            margin-bottom: 10px;
        }
        .kamar-item:last-child { margin-bottom: 0; }
        .kamar-item.active {
            border-color: #3c8dbc;
            background: #f0f7fd;
            box-shadow: 0 0 0 3px rgba(60,141,188,0.12);
        }
        .kamar-thumb {
            width: 90px;
            height: 90px;
            border-radius: 6px;
            object-fit: cover;
            flex-shrink: 0;
            border: 1px solid #e2e8f0;
        }
        .kamar-info { flex: 1; }
        .kamar-name {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 4px 0;
        }
        .kamar-price {
            font-size: 15px;
            font-weight: 700;
            color: #16a34a;
        }
        .kamar-price span { font-size: 11px; font-weight: 400; color: #64748b; }
        .kamar-attr-tag {
            display: inline-block;
            font-size: 10.5px;
            background: #f1f5f9;
            color: #475569;
            padding: 2px 7px;
            border-radius: 99px;
            border: 1px solid #e2e8f0;
            margin: 2px 2px 0 0;
        }

        /* ===== PETA ===== */
        .map-info-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f0f7fd;
            border: 1px solid #bfdbfe;
            border-radius: 7px;
            padding: 10px 14px;
            margin-bottom: 12px;
            font-size: 13px;
            color: #334155;
        }
        .map-distance {
            font-weight: 700;
            color: #3c8dbc;
            font-size: 15px;
        }
        #kostMap {
            width: 100%;
            height: 260px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .map-btn-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 12px;
        }
        .btn-map-blue {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: #4285F4; color: #fff !important;
            padding: 11px 12px; border-radius: 7px;
            font-size: 13px; font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 1px 4px rgba(66,133,244,0.3);
        }
        .btn-map-blue:hover { background: #3472d8; transform: translateY(-1px); text-decoration: none; }
        .btn-map-green {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: #16a34a; color: #fff !important;
            padding: 11px 12px; border-radius: 7px;
            font-size: 13px; font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 1px 4px rgba(22,163,74,0.3);
        }
        .btn-map-green:hover { background: #15803d; transform: translateY(-1px); text-decoration: none; }

        /* ===== STICKY PANEL KANAN ===== */
        .sticky-panel {
            position: sticky;
            top: 76px;
        }

        /* ===== NAMA & HARGA ===== */
        .kost-title-panel {
            padding: 18px 16px 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .kost-title-panel h1 {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 6px 0;
            line-height: 1.3;
        }
        .kost-address-small {
            font-size: 12.5px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Kamar Terpilih box */
        .selected-kamar-box {
            margin: 12px 16px;
            padding: 10px 14px;
            border-radius: 8px;
            border-left: 4px solid #3c8dbc;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-left: 4px solid #3c8dbc;
        }
        .selected-kamar-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            display: block;
            margin-bottom: 3px;
        }
        .selected-kamar-name {
            font-size: 16px;
            font-weight: 700;
            color: #1d4ed8;
            display: block;
        }

        /* Harga box */
        .price-display-box {
            margin: 0 16px 14px;
            padding: 12px 14px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #86efac;
            border-radius: 8px;
        }
        .price-label-sm { font-size: 11px; color: #64748b; display: block; margin-bottom: 2px; }
        .price-value-lg {
            font-size: 22px;
            font-weight: 800;
            color: #15803d;
        }
        .price-value-lg span { font-size: 12px; font-weight: 400; color: #64748b; }

        /* Tombol aksi */
        .action-btns { padding: 0 16px 16px; display: flex; flex-direction: column; gap: 8px; }
        .btn-wa {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: #25d366; color: #fff !important;
            padding: 11px 16px; border-radius: 7px;
            font-size: 14px; font-weight: 700;
            text-decoration: none; border: none; cursor: pointer;
            transition: all 0.2s;
        }
        .btn-wa:hover { background: #1fba57; text-decoration: none; }
        .btn-telp {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: #3c8dbc; color: #fff !important;
            padding: 11px 16px; border-radius: 7px;
            font-size: 14px; font-weight: 700;
            text-decoration: none; border: none; cursor: pointer;
            transition: all 0.2s;
        }
        .btn-telp:hover { background: #31729b; text-decoration: none; }
        .btn-fav {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: #fff; color: #dc2626 !important;
            padding: 10px 16px; border-radius: 7px;
            font-size: 13.5px; font-weight: 600;
            text-decoration: none; border: 1px solid #fca5a5; cursor: pointer;
            transition: all 0.2s;
        }
        .btn-fav:hover { background: #fff5f5; text-decoration: none; }
        .btn-fav-active {
            background: #fef2f2; color: #dc2626 !important;
            border: 1px solid #dc2626;
        }
        .btn-disabled {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: #f1f5f9; color: #94a3b8 !important;
            padding: 11px 16px; border-radius: 7px;
            font-size: 13.5px; font-weight: 600;
            text-decoration: none; border: 1px solid #e2e8f0; cursor: not-allowed;
        }

        /* ===== SPESIFIKASI ===== */
        .spec-section {
            margin-bottom: 14px;
        }
        .spec-section-header {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748b;
            padding: 8px 16px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
        }
        .spec-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 16px;
            border-bottom: 1px solid #f8fafc;
            font-size: 13px;
        }
        .spec-row:last-child { border-bottom: none; }
        .spec-key { color: #64748b; }
        .spec-val { font-weight: 600; color: #1e293b; text-align: right; max-width: 55%; }
        .badge-yes {
            background: #dcfce7; color: #16a34a;
            border: 1px solid #86efac;
            padding: 2px 8px; border-radius: 99px;
            font-size: 11.5px; font-weight: 600;
        }
        .badge-no {
            background: #fee2e2; color: #dc2626;
            border: 1px solid #fca5a5;
            padding: 2px 8px; border-radius: 99px;
            font-size: 11.5px; font-weight: 600;
        }
        .badge-neutral {
            background: #f1f5f9; color: #475569;
            border: 1px solid #e2e8f0;
            padding: 2px 8px; border-radius: 99px;
            font-size: 11.5px; font-weight: 600;
        }

        @media (max-width: 900px) {
            .detail-wrapper { grid-template-columns: 1fr; }
            .sticky-panel { position: static; }
        }
    </style>
</head>
<body style="background-color: #f1f5f9;">

    <!-- Navbar -->
    <nav class="guest-navbar">
        <div class="brand">
            <i class="fa-solid fa-house-laptop" style="color:#3c8dbc;"></i> KOST MAHASISWA CBF
        </div>
        <div class="nav-links">
            <a href="{{ route('home') }}"><i class="fa-solid fa-arrow-left"></i> Beranda</a>
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn-custom btn-primary-custom" style="color:#fff;"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                @elseif(auth()->user()->role === 'pengelola')
                    <a href="{{ route('pengelola.dashboard') }}" class="btn-custom btn-success-custom" style="color:#fff;"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                @else
                    <a href="{{ route('mahasiswa.dashboard') }}" class="btn-custom btn-primary-custom" style="color:#fff;"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                @endif
            @else
                <a href="{{ route('login') }}"><i class="fa-solid fa-right-to-bracket"></i> Masuk</a>
                <a href="{{ route('register') }}" class="btn-custom btn-primary-custom" style="color:#fff;"><i class="fa-solid fa-user-plus"></i> Registrasi</a>
            @endauth
        </div>
    </nav>

    <main class="detail-wrapper">

        <!-- ===== KOLOM KIRI ===== -->
        <div>

            <!-- Galeri Foto -->
            <div class="detail-card">
                <img src="{{ asset($kost->fotoUtama()) }}" id="mainPhoto" class="kost-main-img" alt="{{ $kost->name }}">
                @if($kost->fotos->count() > 1)
                    <div class="kost-thumbs">
                        @foreach($kost->fotos as $f)
                            <img src="{{ asset($f->image_path) }}" onclick="changePhoto('{{ asset($f->image_path) }}')" alt="Foto" style="border: 2px solid {{ $kost->fotos->first() && $kost->fotos->first()->id === $f->id ? '#3c8dbc' : 'transparent' }};">
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Deskripsi -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-file-lines"></i>
                    <h3>Deskripsi Kost</h3>
                </div>
                <div class="detail-card-body" style="font-size:14px; line-height:1.7; color:#475569;">
                    {{ $kost->description ?? 'Tidak ada deskripsi tambahan.' }}
                </div>
            </div>

            <!-- Daftar Kamar -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-bed"></i>
                    <h3>Tipe & Pilihan Kamar <span style="font-weight:400; color:#94a3b8;">({{ $kamars->count() }} kamar)</span></h3>
                </div>
                <div class="detail-card-body">
                    @if($kamars->isEmpty())
                        <div style="text-align:center; padding:20px; color:#94a3b8;">
                            <i class="fa-solid fa-door-closed" style="font-size:32px; margin-bottom:8px; display:block;"></i>
                            Pengelola belum menambahkan data kamar.
                        </div>
                    @else
                        @foreach($kamars as $k)
                            @php $isActive = ($highlightedKamar && $highlightedKamar->id === $k->id); @endphp
                            <div class="kamar-item {{ $isActive ? 'active' : '' }}">
                                <img src="{{ asset($k->fotoKamar()) }}" class="kamar-thumb" alt="{{ $k->name }}">
                                <div class="kamar-info">
                                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:4px;">
                                        <p class="kamar-name">
                                            {{ $k->name }}
                                            @if($isActive)
                                                <span style="font-size:10px; background:#3c8dbc; color:#fff; padding:2px 7px; border-radius:99px; font-weight:600; margin-left:4px;"><i class="fa fa-check"></i> Dipilih</span>
                                            @endif
                                        </p>
                                        <p class="kamar-price">Rp {{ number_format($k->price, 0, ',', '.') }} <span>/ bln</span></p>
                                    </div>
                                    <div style="margin-bottom:7px;">
                                        @if($k->status === 'tersedia')
                                            <span style="font-size:11px; background:#dcfce7; color:#16a34a; border:1px solid #86efac; padding:2px 8px; border-radius:99px; font-weight:600;">Tersedia</span>
                                        @else
                                            <span style="font-size:11px; background:#fee2e2; color:#dc2626; border:1px solid #fca5a5; padding:2px 8px; border-radius:99px; font-weight:600;">Terisi</span>
                                        @endif
                                    </div>
                                    <div style="margin-bottom:8px;">
                                        @foreach($k->atributKamar as $attr)
                                            @if(strpos($attr->opsiKriteria->value, 'Tidak') === false && strpos($attr->opsiKriteria->value, 'Tanpa') === false)
                                                <span class="kamar-attr-tag">{{ $attr->opsiKriteria->value }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div style="text-align:right;">
                                        @if($isActive)
                                            <span style="font-size:12px; color:#64748b; font-style:italic;">Sedang ditampilkan</span>
                                        @else
                                            <a href="{{ route('kost.detail', $kost->id) }}?kamar={{ $k->id }}"
                                               style="font-size:12.5px; background:#3c8dbc; color:#fff; padding:5px 14px; border-radius:6px; text-decoration:none; font-weight:600; transition:all 0.2s;">
                                                Pilih Kamar &rarr;
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Peta Lokasi -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <h3>Peta Lokasi & Jarak ke Kampus UNSUR</h3>
                </div>
                <div class="detail-card-body">
                    <div class="map-info-bar">
                        <div style="display:flex; align-items:center; gap:7px; color:#334155; font-size:13px;">
                            <i class="fa-solid fa-graduation-cap" style="color:#3c8dbc;"></i>
                            <span>{{ $kampus->name }}</span>
                        </div>
                        <div>
                            Jarak: <span class="map-distance" id="distanceText">Menghitung...</span>
                        </div>
                    </div>

                    <div id="kostMap"></div>

                    <div class="map-btn-group">
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $kost->latitude }},{{ $kost->longitude }}&travelmode=driving"
                           target="_blank" class="btn-map-blue">
                            <i class="fa-solid fa-location-arrow"></i> Lokasi Saya ke Kost
                        </a>
                        <a href="https://www.google.com/maps/dir/?api=1&origin={{ $kost->latitude }},{{ $kost->longitude }}&destination=-6.81245,107.14090&travelmode=driving"
                           target="_blank" class="btn-map-green">
                            <i class="fa-solid fa-route"></i> Kost ke Kampus UNSUR
                        </a>
                    </div>

                    <div style="font-size:12px; color:#64748b; margin-top:10px; display:flex; align-items:start; gap:5px; line-height:1.5;">
                        <i class="fa-solid fa-circle-info" style="color:#3c8dbc; margin-top:2px; flex-shrink:0;"></i>
                        <span>Jarak lurus dihitung menggunakan rumus Haversine. Klik tombol <strong>Kost ke Kampus UNSUR</strong> untuk rute nyata via Google Maps.</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- ===== KOLOM KANAN (STICKY) ===== -->
        <div class="sticky-panel">
            <div class="detail-card" style="margin-bottom:0;">

                <!-- Judul Kost -->
                <div class="kost-title-panel">
                    <h1>{{ $kost->name }}</h1>
                    <p class="kost-address-small">
                        <i class="fa-solid fa-location-dot" style="color:#3c8dbc;"></i>
                        {{ Str::limit($kost->address, 60) }}
                    </p>
                </div>

                <!-- Kamar Terpilih -->
                @if($highlightedKamar)
                    <div style="padding: 12px 16px 0;">
                        <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:#94a3b8; margin:0 0 3px 0;">Kamar Terpilih</p>
                        <p style="font-size:17px; font-weight:800; color:#1e293b; margin:0 0 5px 0;">{{ $highlightedKamar->name }}</p>
                        @if($highlightedKamar->status === 'tersedia')
                            <span style="font-size:11px; background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; padding:2px 9px; border-radius:99px; font-weight:600;">Tersedia</span>
                        @else
                            <span style="font-size:11px; background:#fee2e2; color:#dc2626; border:1px solid #fca5a5; padding:2px 9px; border-radius:99px; font-weight:600;">Terisi</span>
                        @endif
                    </div>
                    <hr style="border:none; border-top:1px solid #f1f5f9; margin: 12px 0 0 0;">
                @endif

                <!-- Harga -->
                <div style="padding: 12px 16px 10px;">
                    <p style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:#94a3b8; margin:0 0 3px 0;">Harga Sewa per Bulan</p>
                    <p style="font-size:22px; font-weight:800; color:#1e293b; margin:0;">
                        Rp {{ number_format($highlightedKamar ? $highlightedKamar->price : $kost->price, 0, ',', '.') }}
                        <span style="font-size:12px; font-weight:400; color:#94a3b8;">/ bulan</span>
                    </p>
                </div>
                <hr style="border:none; border-top:1px solid #f1f5f9; margin: 0;">

                <!-- Tombol Aksi -->
                <div class="action-btns">
                    @auth
                        @if(auth()->user()->role === 'mahasiswa')
                            @if($highlightedKamar)
                                <a href="{{ route('mahasiswa.contact', [$highlightedKamar->id, 'whatsapp']) }}" class="btn-wa" target="_blank">
                                    <i class="fa-brands fa-whatsapp" style="font-size:18px;"></i> Hubungi via WhatsApp
                                </a>

                            @else
                                <div class="btn-disabled"><i class="fa-solid fa-ban"></i> Pilih kamar terlebih dahulu</div>
                            @endif
                        @else
                            <div class="btn-disabled"><i class="fa-brands fa-whatsapp"></i> Kontak hanya untuk Mahasiswa</div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-wa">
                            <i class="fa-brands fa-whatsapp" style="font-size:18px;"></i> Login untuk Hubungi Pengelola
                        </a>
                    @endauth
                </div>

                <!-- ===== SPESIFIKASI ===== -->
                <div style="border-top: 2px solid #f1f5f9; margin-top: 4px;">

                    <!-- Kriteria Umum -->
                    <div class="spec-section">
                        <div class="spec-section-header">
                            <i class="fa-solid fa-sliders"></i> Kriteria Umum
                        </div>
                        @forelse($atributUmum as $um)
                            <div class="spec-row">
                                <span class="spec-key">{{ $um['name'] }}</span>
                                <span class="badge-neutral">{{ $um['value'] }}</span>
                            </div>
                        @empty
                            <div class="spec-row"><span style="color:#aaa; font-style:italic; font-size:12px;">Tidak ada data kriteria umum.</span></div>
                        @endforelse
                        <div class="spec-row">
                            <span class="spec-key">Alamat</span>
                            <span class="spec-val" style="font-size:12px; font-weight:500; color:#475569;">{{ $kost->address }}</span>
                        </div>
                    </div>

                    <!-- Fasilitas Pribadi -->
                    <div class="spec-section">
                        <div class="spec-section-header">
                            <i class="fa-solid fa-bed"></i> Fasilitas Pribadi (Dalam Kamar)
                        </div>
                        @forelse($atributPribadi as $pr)
                            <div class="spec-row">
                                <span class="spec-key">{{ $pr['name'] }}</span>
                                <span class="badge-neutral">{{ $pr['value'] }}</span>
                            </div>
                        @empty
                            <div class="spec-row"><span style="color:#aaa; font-style:italic; font-size:12px;">Tidak ada data fasilitas pribadi.</span></div>
                        @endforelse
                    </div>

                    <!-- Fasilitas Bersama -->
                    <div class="spec-section" style="margin-bottom:0;">
                        <div class="spec-section-header">
                            <i class="fa-solid fa-users"></i> Fasilitas Bersama
                        </div>
                        @forelse($atributBersama as $be)
                            <div class="spec-row">
                                <span class="spec-key">{{ $be['name'] }}</span>
                                <span class="badge-neutral">{{ $be['value'] }}</span>
                            </div>
                        @empty
                            <div class="spec-row"><span style="color:#aaa; font-style:italic; font-size:12px;">Tidak ada data fasilitas bersama.</span></div>
                        @endforelse
                        <div style="height:4px;"></div>
                    </div>

                </div>
            </div>
        </div>

    </main>


    <script>
        function changePhoto(src) {
            document.getElementById('mainPhoto').src = src;
        }

        document.addEventListener('DOMContentLoaded', function() {
            var kostLat   = parseFloat("{{ $kost->latitude }}");
            var kostLng   = parseFloat("{{ $kost->longitude }}");
            var campusLat = -6.81245;
            var campusLng = 107.14090;
            var campusName = "{{ $kampus->name }}";

            function haversine(lat1, lon1, lat2, lon2) {
                var R = 6371;
                var dLat = (lat2 - lat1) * Math.PI / 180;
                var dLon = (lon2 - lon1) * Math.PI / 180;
                var a = Math.sin(dLat/2) ** 2 +
                        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon/2) ** 2;
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            }

            var dist = haversine(kostLat, kostLng, campusLat, campusLng);
            document.getElementById('distanceText').innerText = dist.toFixed(2) + ' km';

            var map = L.map('kostMap').setView(
                [(kostLat + campusLat) / 2, (kostLng + campusLng) / 2], 14
            );
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19
            }).addTo(map);

            var blueIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
            });
            var redIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
            });

            L.marker([kostLat, kostLng], { icon: blueIcon })
                .addTo(map)
                .bindPopup('<strong>🏠 {{ $kost->name }}</strong><br><em style="color:#64748b;">{{ $kost->address }}</em>')
                .openPopup();

            L.marker([campusLat, campusLng], { icon: redIcon })
                .addTo(map)
                .bindPopup('<strong>🎓 ' + campusName + '</strong><br><span style="color:#3c8dbc; font-weight:600;">Jarak: ' + dist.toFixed(2) + ' km</span>');

            L.polyline([[kostLat, kostLng], [campusLat, campusLng]], {
                color: '#3c8dbc', weight: 3, opacity: 0.7, dashArray: '6, 8'
            }).addTo(map);

            map.fitBounds([[kostLat, kostLng], [campusLat, campusLng]], { padding: [40, 40] });
        });


    </script>
</body>
</html>
