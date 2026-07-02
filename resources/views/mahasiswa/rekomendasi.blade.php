@extends('layouts.dashboard')

@section('title', 'Cari Rekomendasi Kost')
@section('header-title', 'Rekomendasi Kost (Content-Based Filtering)')
@section('breadcrumb-active', 'Rekomendasi')

@section('styles')
<style>
    .tab-btn {
        background: none;
        border: none;
        padding: 12px 20px;
        font-weight: 700;
        font-size: 14px;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .tab-btn:hover {
        color: #3c8dbc;
    }
    .tab-btn.active {
        color: #3c8dbc;
        border-bottom-color: #3c8dbc;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
        animation: fadeIn 0.2s ease;
    }
    .check-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.01);
    }
    .check-card strong {
        display: block;
        font-size: 13.5px;
        color: #0f172a;
        margin-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 5px;
    }
    .check-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 3px 0;
    }
    .check-item input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #3c8dbc;
    }
    .check-item label {
        margin-bottom: 0;
        font-weight: 500;
        cursor: pointer;
        font-size: 13px;
        color: #334155;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Radar Scanner Animation */
    .radar-container {
        position: relative;
        width: 84px;
        height: 84px;
        margin: 0 auto 20px;
        border-radius: 50%;
        background: rgba(14, 165, 233, 0.05);
        border: 2px dashed rgba(14, 165, 233, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .radar-pulse {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.2) 0%, transparent 75%);
        animation: pulseRadar 1.8s infinite linear;
    }
    .radar-scan-line {
        position: absolute;
        width: 50%;
        height: 3px;
        background: linear-gradient(90deg, #0ea5e9, transparent);
        top: 50%;
        left: 50%;
        transform-origin: 0 50%;
        animation: rotateScan 1.8s infinite linear;
        z-index: 2;
    }
    @keyframes pulseRadar {
        0% { transform: scale(0.6); opacity: 0.9; }
        100% { transform: scale(1.3); opacity: 0; }
    }
    @keyframes rotateScan {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Code Terminal Display */
    .terminal-container {
        background: #090d16;
        border: 1px solid #1e293b;
        border-radius: 8px;
        padding: 14px;
        font-family: 'Courier New', Courier, monospace;
        color: #38bdf8;
        font-size: 12px;
        text-align: left;
        margin-bottom: 20px;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.6);
    }
    .terminal-line {
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
    }
    .terminal-header {
        color: #475569;
        font-weight: bold;
        border-bottom: 1px solid #1e293b;
        padding-bottom: 4px;
        margin-bottom: 8px;
        font-size: 11px;
    }
</style>
@endsection

@section('content')

<!-- PANEL PENGISIAN PREFERENSI (COLLAPSIBLE JIKA SUDAH ADA HASIL) -->
<div class="box box-primary">
    <div class="box-header" style="cursor: pointer;" onclick="togglePreferenceForm()">
        <h3 class="box-title">
            <i class="fa-solid fa-sliders"></i> 
            @if($mahasiswaPrefs->isEmpty())
                Tentukan Preferensi Kriteria Kost Impian Anda
            @else
                Atur Ulang / Sesuaikan Preferensi Kost
            @endif
        </h3>
        <div class="box-tools">
            <button type="button" class="btn-custom btn-xs" style="background-color: #eee; color: #555;">
                <i class="fa-solid {{ $mahasiswaPrefs->isEmpty() ? 'fa-chevron-up' : 'fa-chevron-down' }}" id="collapseIcon"></i>
            </button>
        </div>
    </div>
    
    <div class="box-body" id="preferenceFormBody" style="display: {{ $mahasiswaPrefs->isEmpty() ? 'block' : 'none' }};">
        <p style="color: #666; margin-bottom: 20px; font-size: 14px;">
            Silakan tentukan pilihan kriteria di bawah ini. Pilihan ini akan dipetakan menjadi vektor biner preferensi Anda ($\vec{p}$) untuk dicocokkan dengan vektor spesifikasi kost ($\vec{k}$) menggunakan rumus **Cosine Similarity**.
        </p>
        
        <form action="{{ route('mahasiswa.preferensi.save') }}" method="post" id="preferenceForm">
            @csrf
            
            <!-- Tab Navigation Buttons -->
            <div style="display: flex; gap: 5px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px;">
                <button type="button" class="tab-btn active" onclick="switchPrefTab(event, 'tab-umum')">
                    <i class="fa-solid fa-gears"></i> 1. Kriteria Umum
                </button>
                <button type="button" class="tab-btn" onclick="switchPrefTab(event, 'tab-pribadi')">
                    <i class="fa-solid fa-bed"></i> 2. Fasilitas Pribadi
                </button>
                <button type="button" class="tab-btn" onclick="switchPrefTab(event, 'tab-bersama')">
                    <i class="fa-solid fa-users"></i> 3. Fasilitas Bersama
                </button>
            </div>
            
            <!-- Tab 1: Kriteria Umum -->
            <div id="tab-umum" class="tab-content active">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 15px;">
                    @foreach($kriteriaUmum as $k)
                        <div class="check-card">
                            <strong>{{ $k->name }}</strong>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @forelse($k->opsiKriteria as $o)
                                    <div class="check-item">
                                        <input type="checkbox" name="prefs[{{ $k->id }}][]" id="pref_opsi_{{ $o->id }}" value="{{ $o->id }}" {{ in_array($o->id, $currentPrefs) ? 'checked' : '' }}>
                                        <label for="pref_opsi_{{ $o->id }}">{{ $o->value }}</label>
                                    </div>
                                @empty
                                    <span style="color: #aaa; font-style: italic; font-size: 11.5px;">Belum ada opsi.</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Tab 2: Fasilitas Pribadi -->
            <div id="tab-pribadi" class="tab-content">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 15px;">
                    @foreach($kriteriaPribadi as $k)
                        <div class="check-card">
                            <strong>{{ $k->name }}</strong>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @forelse($k->opsiKriteria as $o)
                                    <div class="check-item">
                                        <input type="checkbox" name="prefs[{{ $k->id }}][]" id="pref_opsi_{{ $o->id }}" value="{{ $o->id }}" {{ in_array($o->id, $currentPrefs) ? 'checked' : '' }}>
                                        <label for="pref_opsi_{{ $o->id }}">{{ $o->value }}</label>
                                    </div>
                                @empty
                                    <span style="color: #aaa; font-style: italic; font-size: 11.5px;">Belum ada opsi.</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Tab 3: Fasilitas Bersama -->
            <div id="tab-bersama" class="tab-content">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 15px;">
                    @foreach($kriteriaBersama as $k)
                        <div class="check-card">
                            <strong>{{ $k->name }}</strong>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @forelse($k->opsiKriteria as $o)
                                    <div class="check-item">
                                        <input type="checkbox" name="prefs[{{ $k->id }}][]" id="pref_opsi_{{ $o->id }}" value="{{ $o->id }}" {{ in_array($o->id, $currentPrefs) ? 'checked' : '' }}>
                                        <label for="pref_opsi_{{ $o->id }}">{{ $o->value }}</label>
                                    </div>
                                @empty
                                    <span style="color: #aaa; font-style: italic; font-size: 11.5px;">Belum ada opsi.</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div style="text-align: right; margin-top: 25px; padding-top: 15px; border-top: 1px solid #f1f5f9;">
                <button type="submit" class="btn-custom btn-primary-custom" style="padding: 11px 28px; font-weight: bold; font-size: 14.5px;">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Cari Rekomendasi Kost
                </button>
            </div>
        </form>

        <!-- OVERLAY LOADING HITUNG COSINE SIMILARITY (NATIVE ADMINLTE/BOOTSTRAP STYLE) -->
        <div id="calcLoadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.5); z-index: 9999; justify-content: center; align-items: center;">
            <div style="background: #ffffff; border: 1px solid #d2d6de; border-radius: 4px; padding: 25px 20px; width: 400px; max-width: 90%; box-shadow: 0 4px 12px rgba(0,0,0,0.15); color: #333; text-align: left;">
                
                <h4 style="margin: 0 0 15px 0; font-weight: 600; color: #333; font-size: 15px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-spinner fa-spin" style="color: #3c8dbc;"></i> 
                    Mencari kost yang paling sesuai...
                </h4>
                
                <!-- Native Bootstrap Progress Bar -->
                <div style="background: #f1f3f5; border: 1px solid #dee2e6; border-radius: 4px; height: 22px; overflow: hidden; width: 100%; margin-bottom: 12px;">
                    <div id="nativeProgressBar" style="width: 0%; height: 100%; background-color: #3c8dbc; color: #fff; font-size: 11px; font-weight: bold; text-align: center; line-height: 20px; transition: width 0.15s ease;">
                        0%
                    </div>
                </div>
                
                <!-- Changing Status Text -->
                <div id="changingStatusText" style="font-size: 13px; color: #666; font-weight: 500; min-height: 18px; padding-left: 2px;">
                    Mengambil data kost...
                </div>
                
            </div>
        </div>
    </div>
</div>

<!-- PANEL HASIL REKOMENDASI (DITAMPILKAN JIKA PREFERENSI SUDAH DIISI) -->
@if($mahasiswaPrefs->isNotEmpty())
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa-solid fa-list-ol"></i> Peringkat Rekomendasi Kost ({{ count($recommendations) }} Hasil)</h3>
        </div>
        
        <div class="box-body">
            <!-- Tampilan Ringkasan Preferensi Aktif -->
            <div style="background-color: #fff8eb; border-left: 4px solid var(--card-yellow); padding: 12px 15px; border-radius: 3px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #ffe8cc; border-left: 4px solid var(--card-yellow);">
                <div style="font-size: 13.5px; line-height: 1.4; flex-grow: 1;">
                    <i class="fa-solid fa-info-circle" style="color: #f39c12;"></i> Preferensi Aktif Anda: <br>
                    <div style="display: flex; flex-wrap: wrap; gap: 5px; margin-top: 5px;">
                        @foreach($mahasiswaPrefs as $pref)
                            <span class="badge" style="background-color: #fff; color: #d9480f; border: 1px solid #ffd8a8; font-weight: normal;">
                                <strong>{{ $pref->kriteria->name }}:</strong> {{ $pref->opsiKriteria->value }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div style="display: flex; gap: 8px; flex-shrink: 0; align-items: center;">
                    <button class="btn-custom btn-warning-custom btn-sm" onclick="togglePreferenceForm()"><i class="fa fa-sliders"></i> Sesuaikan</button>
                    <form id="clearPreferencesForm" action="{{ route('mahasiswa.preferensi.clear') }}" method="POST" style="display: inline; margin: 0;">
                        @csrf
                        <button type="button" class="btn-custom btn-danger-custom btn-sm" onclick="confirmClearPreferences()"><i class="fa fa-trash"></i> Hapus Preferensi</button>
                    </form>
                </div>
            </div>

            @if(empty($recommendations))
                <div style="text-align: center; padding: 30px;">
                    <i class="fa-solid fa-house-circle-xmark" style="font-size: 40px; color: #ccc;"></i>
                    <p style="color: #666; margin-top: 10px;">Tidak ada kost yang terdaftar di sistem saat ini.</p>
                </div>
            @else
                <!-- Loop Hasil Rekomendasi -->
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    @foreach($recommendations as $index => $rec)
                        @php
                            $kamar = $rec['kamar'];
                            $kost = $kamar->kost;
                            $scorePercent = $rec['similarity'] * 100;
                            $calc = $rec['calculation'];
                        @endphp
                        
                        <div style="background-color: #ffffff; border: 1px solid #d2d6de; border-top: 3px solid {{ $index === 0 ? 'var(--card-green)' : 'var(--primary-color)' }}; border-radius: 3px; display: flex; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                            <!-- Thumbnail Image -->
                            <div style="width: 200px; flex-shrink: 0; position: relative;">
                                <img src="{{ asset($kamar->fotoKamar()) }}" alt="{{ $kamar->name }}" style="width: 100%; height: 100%; object-fit: cover; min-height: 150px;">
                                <div style="position: absolute; top: 10px; left: 10px; background-color: rgba(0,0,0,0.7); color: #fff; padding: 3px 8px; border-radius: 3px; font-weight: bold; font-size: 13px;">
                                    Rank #{{ $index + 1 }}
                                </div>
                            </div>
                            
                            <!-- Body -->
                            <div style="padding: 15px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                        <div>
                                            <h3 style="font-size: 18px; margin: 0; font-weight: 700; color: #333;">
                                                {{ $kamar->name }} 
                                                <span style="font-size: 14px; font-weight: normal; color: #666;">- {{ $kost->name }}</span>
                                            </h3>
                                            <span style="font-size: 12px; color: #777;"><i class="fa fa-road"></i> {{ $kost->kampus->name }}</span>
                                        </div>
                                        
                                        <!-- Similarity Badge -->
                                        <div class="similarity-badge" style="border-color: {{ $index === 0 ? '#b2f2bb' : '#ffd8a8' }}; color: {{ $index === 0 ? '#2b8a3e' : '#d9480f' }}; background-color: {{ $index === 0 ? '#ebfbee' : '#fff8eb' }}; font-size: 13.5px; padding: 5px 10px;">
                                            <i class="fa-solid fa-circle-nodes"></i> {{ number_format($scorePercent, 1, ',', '.') }}% Cocok
                                        </div>
                                    </div>
                                    
                                    <p style="font-size: 16px; font-weight: 700; color: var(--card-green); margin-bottom: 8px;">
                                        Rp {{ number_format($kamar->price, 0, ',', '.') }} <span style="font-size: 11px; font-weight: normal; color: #777;">/ bulan</span>
                                    </p>
                                    
                                    <p style="font-size: 12px; color: #666; margin-bottom: 10px;"><i class="fa fa-map-marker-alt"></i> {{ $kost->address }}</p>
                                    
                                    <div style="display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 10px;">
                                        @php
                                            $jenis = $kost->atributKost->where('kriteria.name', 'Jenis Kost')->first();
                                            $jenisVal = $jenis ? $jenis->opsiKriteria->value : 'Umum';
                                        @endphp
                                        @if($jenisVal == 'Putra')
                                            <span class="badge" style="background-color: #007bff; color: #fff;">Putra</span>
                                        @elseif($jenisVal == 'Putri')
                                            <span class="badge" style="background-color: #e83e8c; color: #fff;">Putri</span>
                                        @else
                                            <span class="badge" style="background-color: #28a745; color: #fff;">Campur</span>
                                        @endif
 
                                        @foreach($calc['matched_options'] as $mOpt)
                                            @if(strpos($mOpt, 'Wi-Fi') !== false || strpos($mOpt, 'AC') !== false || strpos($mOpt, 'Kamar Mandi') !== false)
                                                <span class="badge" style="background-color: #f1f3f5; color: #495057; border: 1px solid #dee2e6;">{{ explode(': ', $mOpt)[1] }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div style="border-top: 1px solid #eee; padding-top: 10px; display: flex; justify-content: flex-end; gap: 8px;">
                                    <button type="button" class="btn-custom btn-warning-custom btn-sm" onclick="showCalculationModal('calc_{{ $kamar->id }}')">
                                        <i class="fa-solid fa-list-check"></i> Detail Kecocokan Fasilitas
                                    </button>
                                    <a href="{{ route('kost.detail', $kost->id) }}?kamar={{ $kamar->id }}" class="btn-custom btn-primary-custom btn-sm"><i class="fa fa-eye"></i> Detail Peta & Kontak</a>
                                </div>
                            </div>
                        </div>
 
                        <!-- MODAL DETAIL KECOCOKAN FASILITAS TANPA PERHITUNGAN MATEMATIKA -->
                        <div class="modal" id="calc_{{ $kamar->id }}">
                            <div class="modal-content" style="width: 600px; max-width: 95%;">
                                <div class="modal-header">
                                    <h3><i class="fa-solid fa-list-check"></i> Detail Kecocokan Fasilitas - {{ $kamar->name }}</h3>
                                    <button type="button" class="close-btn" onclick="closeCalculationModal('calc_{{ $kamar->id }}')">&times;</button>
                                </div>
                                <div class="modal-body" style="font-size: 14px; line-height: 1.6; padding: 20px;">
                                    <div style="background-color: #f8f9fa; border: 1px solid #e9ecef; padding: 12px; border-radius: 4px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                                        <span>Skor Persentase Kecocokan:</span>
                                        <strong style="color: var(--primary-color); font-size: 16px;">{{ number_format($scorePercent, 1, ',', '.') }}% Cocok</strong>
                                    </div>
                                    
                                    <div style="margin-bottom: 20px;">
                                        <h4 style="font-weight: 700; margin-bottom: 10px; color: #2b8a3e; border-bottom: 1px solid #ebfbee; padding-bottom: 5px;">
                                            <i class="fa fa-check-circle"></i> Fasilitas yang Cocok ({{ count($calc['matched_options']) }}):
                                        </h4>
                                        <ul style="list-style: none; padding-left: 0; display: flex; flex-direction: column; gap: 8px;">
                                            @forelse($calc['matched_options'] as $match)
                                                <li style="display: flex; align-items: start; gap: 8px;">
                                                    <i class="fa fa-check-circle" style="color: #2b8a3e; margin-top: 4px;"></i>
                                                    <span>{{ $match }}</span>
                                                </li>
                                            @empty
                                                <li style="color: #888;">Tidak ada kriteria yang cocok.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                    
                                    <div>
                                        <h4 style="font-weight: 700; margin-bottom: 10px; color: #c92a2a; border-bottom: 1px solid #fff5f5; padding-bottom: 5px;">
                                            <i class="fa class fa-times-circle"></i> Preferensi Anda yang Tidak Terpenuhi ({{ count($calc['unmatched_preferences']) }}):
                                        </h4>
                                        <ul style="list-style: none; padding-left: 0; display: flex; flex-direction: column; gap: 8px;">
                                            @forelse($calc['unmatched_preferences'] as $unmatch)
                                                <li style="display: flex; align-items: start; gap: 8px;">
                                                    <i class="fa fa-times-circle" style="color: #c92a2a; margin-top: 4px;"></i>
                                                    <span>{{ $unmatch }}</span>
                                                </li>
                                            @empty
                                                <li style="color: #2b8a3e; display: flex; align-items: center; gap: 6px;">
                                                    <i class="fa fa-star" style="color: #ffd43b;"></i>
                                                    <strong>Luar Biasa! Semua kriteria preferensi Anda terpenuhi pada kamar ini!</strong>
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn-custom" style="background-color: #eee; color: #333;" onclick="closeCalculationModal('calc_{{ $kamar->id }}')">Tutup</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@else
    <!-- TAMPILAN JIKA BELUM MENGISI PREFERENSI -->
    <div style="text-align: center; padding: 40px; background-color: #ffffff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-top: 3px solid var(--primary-color);">
        <i class="fa-solid fa-wand-magic-sparkles" style="font-size: 50px; color: #ccc; margin-bottom: 15px; display: block;"></i>
        <p style="color: #666; font-size: 15.5px;">
            Anda belum mengisi preferensi kriteria kost. Silakan pilih kriteria kost impian Anda pada formulir di atas untuk memulai pencarian rekomendasi kost berbasis *Content-Based Filtering*.
        </p>
    </div>
@endif

@endsection

@section('scripts')
<script>
    // Tab switcher for preferences
    function switchPrefTab(evt, tabId) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(tab => tab.classList.remove('active'));
        
        // Remove active class from all buttons
        const tabBtns = document.querySelectorAll('.tab-btn');
        tabBtns.forEach(btn => btn.classList.remove('active'));
        
        // Show selected tab content and active button
        document.getElementById(tabId).classList.add('active');
        evt.currentTarget.classList.add('active');
    }

    // Intercept form submission to show mathematical calculation animation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('preferenceForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Check if at least one checkbox is checked
                const checkedBoxes = form.querySelectorAll('input[type="checkbox"]:checked');
                if (checkedBoxes.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Kriteria Kosong',
                        text: 'Silakan pilih minimal satu preferensi kriteria untuk mendapatkan rekomendasi.',
                        confirmButtonColor: '#3c8dbc'
                    });
                    return;
                }

                e.preventDefault(); // Stop submission for animation

                // Show loading overlay
                const overlay = document.getElementById('calcLoadingOverlay');
                overlay.style.display = 'flex';

                const nativeBar = document.getElementById('nativeProgressBar');
                const statusText = document.getElementById('changingStatusText');
                
                // Step-by-step progress simulation
                let currentPercent = 0;
                
                // Update percentage and text sequentially
                const progressInterval = setInterval(() => {
                    currentPercent += 5;
                    if (currentPercent > 100) currentPercent = 100;
                    
                    // Update native progress bar width and inner text
                    nativeBar.style.width = currentPercent + '%';
                    nativeBar.innerText = currentPercent + '%';
                    
                    // Update changing status text based on percentage range
                    if (currentPercent < 25) {
                        statusText.innerText = "Mengambil data kost...";
                    } else if (currentPercent < 50) {
                        statusText.innerText = "Membandingkan atribut...";
                    } else if (currentPercent < 75) {
                        statusText.innerText = "Menghitung tingkat kemiripan...";
                    } else if (currentPercent <= 100) {
                        statusText.innerText = "Menyusun rekomendasi...";
                    }

                    if (currentPercent >= 100) {
                        clearInterval(progressInterval);
                        // Submit form after reaching 100%
                        setTimeout(() => {
                            form.submit();
                        }, 400);
                    }
                }, 130); // 130ms * 20 steps = ~2.6 seconds total
            });
        }
    });

    // Toggle Slide Up/Down Form Preferensi
    function togglePreferenceForm() {
        var formBody = document.getElementById('preferenceFormBody');
        var icon = document.getElementById('collapseIcon');
        
        if (formBody.style.display === 'none') {
            formBody.style.display = 'block';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            formBody.style.display = 'none';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    }

    // Modal Control
    function showCalculationModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
        }
    }

    // SweetAlert Konfirmasi Reset Preferensi
    function confirmClearPreferences() {
        Swal.fire({
            title: 'Hapus Preferensi?',
            text: "Seluruh kriteria preferensi terisi Anda akan dihapus, dan halaman akan di-reset.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('clearPreferencesForm').submit();
            }
        });
    }

    function closeCalculationModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
        }
    }

    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.classList.remove('active');
        }
    });
</script>
@endsection
