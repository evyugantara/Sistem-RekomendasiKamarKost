@extends('layouts.dashboard')

@section('title', 'Kelola Fasilitas Kost')
@section('header-title', 'Kelola Fasilitas & Spesifikasi Kost')
@section('breadcrumb-active', 'Kelola Fasilitas')

@section('styles')
<style>
    .tab-btn {
        background: none;
        border: none;
        padding: 12px 20px;
        font-weight: 700;
        font-size: 14px;
        color: 
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .tab-btn:hover {
        color: 
    }
    .tab-btn.active {
        color: 
        border-bottom-color: 
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
        <h3 class="box-title"><i class="fa-solid fa-list-check"></i> Atur Spesifikasi & Fasilitas: <strong>{{ $kost->name }}</strong></h3>
    </div>
    
    <div class="box-body">
        <p style="color: #666; margin-bottom: 20px; font-size: 14px;">Silakan beri tanda centang (☑) pada opsi atribut yang sesuai dengan kondisi nyata kost Anda. Klik tab untuk beralih kategori tanpa perlu scroll jauh.</p>
        
        <form action="{{ route('pengelola.kost.fasilitas.simpan', $kost->id) }}" method="post">
            @csrf
            
            
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
            
            
            <div id="tab-umum" class="tab-content active">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                    @foreach($kriteriaUmum as $k)
                        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.01);">
                            <strong style="display: block; font-size: 13.5px; color: #0f172a; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">{{ $k->name }}</strong>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @forelse($k->opsiKriteria as $o)
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <input type="checkbox" name="attrs[{{ $k->id }}][]" id="opsi_{{ $o->id }}" value="{{ $o->id }}" {{ in_array($o->id, $currentAttrs) ? 'checked' : '' }} style="width: 16px; height: 16px; cursor: pointer; accent-color: #3c8dbc;">
                                        <label for="opsi_{{ $o->id }}" style="margin-bottom: 0; font-weight: 500; cursor: pointer; font-size: 13px; color: #334155;">{{ $o->value }}</label>
                                    </div>
                                @empty
                                    <span style="color: #aaa; font-style: italic; font-size: 12px;">Belum ada opsi kriteria.</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            
            <div id="tab-pribadi" class="tab-content">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                    @foreach($kriteriaPribadi as $k)
                        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.01);">
                            <strong style="display: block; font-size: 13.5px; color: #0f172a; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">{{ $k->name }}</strong>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @forelse($k->opsiKriteria as $o)
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <input type="checkbox" name="attrs[{{ $k->id }}][]" id="opsi_{{ $o->id }}" value="{{ $o->id }}" {{ in_array($o->id, $currentAttrs) ? 'checked' : '' }} style="width: 16px; height: 16px; cursor: pointer; accent-color: #3c8dbc;">
                                        <label for="opsi_{{ $o->id }}" style="margin-bottom: 0; font-weight: 500; cursor: pointer; font-size: 13px; color: #334155;">{{ $o->value }}</label>
                                    </div>
                                @empty
                                    <span style="color: #aaa; font-style: italic; font-size: 12px;">Belum ada opsi kriteria.</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            
            <div id="tab-bersama" class="tab-content">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                    @foreach($kriteriaBersama as $k)
                        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.01);">
                            <strong style="display: block; font-size: 13.5px; color: #0f172a; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px;">{{ $k->name }}</strong>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @forelse($k->opsiKriteria as $o)
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <input type="checkbox" name="attrs[{{ $k->id }}][]" id="opsi_{{ $o->id }}" value="{{ $o->id }}" {{ in_array($o->id, $currentAttrs) ? 'checked' : '' }} style="width: 16px; height: 16px; cursor: pointer; accent-color: #3c8dbc;">
                                        <label for="opsi_{{ $o->id }}" style="margin-bottom: 0; font-weight: 500; cursor: pointer; font-size: 13px; color: #334155;">{{ $o->value }}</label>
                                    </div>
                                @empty
                                    <span style="color: #aaa; font-style: italic; font-size: 12px;">Belum ada opsi kriteria.</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="box-footer" style="padding-left: 0; padding-right: 0; background: none; border-top: 1px solid #eee; margin-top: 25px; padding-top: 15px;">
                <button type="submit" class="btn-custom btn-success-custom" style="padding: 10px 25px; font-weight: bold; font-size: 14px;">
                    <i class="fa-solid fa-check-circle"></i> Simpan Spesifikasi & Fasilitas Kost
                </button>
                <a href="{{ route('pengelola.kost') }}" class="btn-custom" style="background-color: #ddd; color: #333; padding: 10px 20px; font-size: 14px;">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function switchTab(evt, tabId) {
        
        const tabContents = document.getElementsByClassName('tab-content');
        for (let i = 0; i < tabContents.length; i++) {
            tabContents[i].classList.remove('active');
        }
        
        
        const tabBtns = document.getElementsByClassName('tab-btn');
        for (let i = 0; i < tabBtns.length; i++) {
            tabBtns[i].classList.remove('active');
        }
        
        
        document.getElementById(tabId).classList.add('active');
        evt.currentTarget.classList.add('active');
    }
</script>
@endsection
