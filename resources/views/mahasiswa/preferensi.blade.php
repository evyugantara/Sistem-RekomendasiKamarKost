@extends('layouts.dashboard')

@section('title', 'Isi Preferensi Kost')
@section('header-title', 'Isi Preferensi Kriteria Kost')
@section('breadcrumb-active', 'Preferensi')

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
        display: flex;
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
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><i class="fa-solid fa-sliders"></i> Tentukan Kriteria Kost Impian Anda</h3>
    </div>
    
    <div class="box-body">
        <p style="color: #666; margin-bottom: 20px; font-size: 14px;">Silakan tentukan pilihan Anda untuk masing-masing kriteria di bawah ini dengan memberikan tanda centang (☑) pada opsi atribut yang Anda kehendaki. Klik tab untuk beralih kategori tanpa perlu scroll jauh.</p>
        
        <form action="{{ route('mahasiswa.preferensi.save') }}" method="post">
            @csrf
            
            <!-- Tab Navigation Buttons -->
            <div style="display: flex; gap: 5px; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px;">
                <button type="button" class="tab-btn active" onclick="switchTab(event, 'tab-umum')">
                    <i class="fa-solid fa-gears"></i> 1. Kriteria Umum
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-pribadi')">
                    <i class="fa-solid fa-bed"></i> 2. Fasilitas Pribadi
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-bersama')">
                    <i class="fa-solid fa-users"></i> 3. Fasilitas Bersama
                </button>
            </div>
            
            <!-- Tab 1: Kriteria Umum -->
            <div id="tab-umum" class="tab-content active">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                    @foreach($kriteriaUmum as $k)
                        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.01);">
                            <strong style="display: block; font-size: 13.5px; color: #0f172a; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">{{ $k->name }}</strong>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @forelse($k->opsiKriteria as $o)
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <input type="checkbox" name="prefs[{{ $k->id }}][]" id="pref_opsi_{{ $o->id }}" value="{{ $o->id }}" {{ in_array($o->id, $currentPrefs) ? 'checked' : '' }} style="width: 16px; height: 16px; cursor: pointer; accent-color: #3c8dbc;">
                                        <label for="pref_opsi_{{ $o->id }}" style="margin-bottom: 0; font-weight: 500; cursor: pointer; font-size: 13px; color: #334155;">{{ $o->value }}</label>
                                    </div>
                                @empty
                                    <span style="color: #aaa; font-style: italic; font-size: 12px;">Belum ada opsi kriteria.</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Tab 2: Fasilitas Pribadi -->
            <div id="tab-pribadi" class="tab-content">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                    @foreach($kriteriaPribadi as $k)
                        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.01);">
                            <strong style="display: block; font-size: 13.5px; color: #0f172a; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">{{ $k->name }}</strong>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @forelse($k->opsiKriteria as $o)
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <input type="checkbox" name="prefs[{{ $k->id }}][]" id="pref_opsi_{{ $o->id }}" value="{{ $o->id }}" {{ in_array($o->id, $currentPrefs) ? 'checked' : '' }} style="width: 16px; height: 16px; cursor: pointer; accent-color: #3c8dbc;">
                                        <label for="pref_opsi_{{ $o->id }}" style="margin-bottom: 0; font-weight: 500; cursor: pointer; font-size: 13px; color: #334155;">{{ $o->value }}</label>
                                    </div>
                                @empty
                                    <span style="color: #aaa; font-style: italic; font-size: 12px;">Belum ada opsi kriteria.</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Tab 3: Fasilitas Bersama -->
            <div id="tab-bersama" class="tab-content">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                    @foreach($kriteriaBersama as $k)
                        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.01);">
                            <strong style="display: block; font-size: 13.5px; color: #0f172a; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">{{ $k->name }}</strong>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @forelse($k->opsiKriteria as $o)
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <input type="checkbox" name="prefs[{{ $k->id }}][]" id="pref_opsi_{{ $o->id }}" value="{{ $o->id }}" {{ in_array($o->id, $currentPrefs) ? 'checked' : '' }} style="width: 16px; height: 16px; cursor: pointer; accent-color: #3c8dbc;">
                                        <label for="pref_opsi_{{ $o->id }}" style="margin-bottom: 0; font-weight: 500; cursor: pointer; font-size: 13px; color: #334155;">{{ $o->value }}</label>
                                    </div>
                                @empty
                                    <span style="color: #aaa; font-style: italic; font-size: 12px;">Belum ada opsi kriteria.</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="box-footer" style="padding-left: 0; padding-right: 0; background: none; display: flex; gap: 10px; margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee;">
                <button type="submit" class="btn-custom btn-primary-custom" style="padding: 10px 25px; font-size: 15px; font-weight: bold; border-radius: 4px;">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Simpan & Cari Rekomendasi Kost
                </button>
                @if(count($currentPrefs) > 0)
                    <a href="{{ route('mahasiswa.rekomendasi') }}" class="btn-custom" style="background-color: #ddd; color: #333; padding: 10px 20px; font-size: 15px; display: inline-flex; align-items: center;">
                        <i class="fa fa-eye"></i> Lihat Hasil Rekomendasi Terakhir
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function switchTab(evt, tabId) {
        // Hide all tab contents
        const tabContents = document.getElementsByClassName('tab-content');
        for (let i = 0; i < tabContents.length; i++) {
            tabContents[i].classList.remove('active');
        }
        
        // Remove active class from all buttons
        const tabBtns = document.getElementsByClassName('tab-btn');
        for (let i = 0; i < tabBtns.length; i++) {
            tabBtns[i].classList.remove('active');
        }
        
        // Show selected tab content and active button
        document.getElementById(tabId).classList.add('active');
        evt.currentTarget.classList.add('active');
    }
</script>
@endsection
