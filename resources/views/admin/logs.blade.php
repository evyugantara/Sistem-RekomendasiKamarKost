@extends('layouts.dashboard')

@section('title', 'Log Aktivitas CBF')
@section('header-title', 'Log Riwayat Rekomendasi Mahasiswa')
@section('breadcrumb-active', 'Log Aktivitas')

@section('styles')
<style>
    .pref-badge {
        display: inline-block;
        background-color: 
        border: 1px solid 
        color: 
        font-size: 11px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 99px;
        margin: 2px 2px 2px 0;
        white-space: nowrap;
    }
    .log-card-row td {
        vertical-align: middle !important;
        padding: 12px 10px !important;
    }
    .log-card-row:hover {
        background-color: 
    }
    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: 
        color: 
        font-weight: 700;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
        flex-shrink: 0;
    }
</style>
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Proses Rekomendasi (Content-Based Filtering)</h3>
    </div>

    <div class="box-body">
        <p style="color: #666; font-size: 13.5px; margin-bottom: 18px;">
            Daftar ini mencatat setiap kali Mahasiswa menekan tombol <strong>"Cari Rekomendasi Kost"</strong>. Berguna untuk memantau preferensi yang sering dicari oleh mahasiswa di sekitar kampus.
        </p>

        <div class="table-responsive">
            <table class="table-custom" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align:center;">No</th>
                        <th style="width: 170px;">Mahasiswa</th>
                        <th style="width: 150px;">Info Akademik</th>
                        <th>Preferensi yang Dicari</th>
                        <th style="width: 110px; text-align: center;">Kost Cocok</th>
                        <th style="width: 120px;">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $index => $log)
                        @php
                            
                            $prefItems = $log->preference_summary
                                ? array_filter(array_map('trim', explode(',', $log->preference_summary)))
                                : [];
                        @endphp
                        <tr class="log-card-row">
                            <td style="text-align:center; font-weight:600; color:#64748b;">{{ $index + 1 }}</td>

                            
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <span class="user-avatar">{{ strtoupper(substr($log->user->name, 0, 1)) }}</span>
                                    <div>
                                        <strong style="font-size:13px; display:block;">{{ $log->user->name }}</strong>
                                        <span style="font-size:11px; color:#94a3b8;">{{ $log->user->username }}</span>
                                    </div>
                                </div>
                            </td>

                            
                            <td>
                                <span style="font-size: 11.5px; color:#334155; display:block; line-height:1.5;">
                                    <i class="fa-solid fa-id-card" style="color:#94a3b8; width:14px;"></i>
                                    {{ $log->user->profilMahasiswa->nim ?? '-' }}<br>
                                    <i class="fa-solid fa-graduation-cap" style="color:#94a3b8; width:14px;"></i>
                                    {{ $log->user->profilMahasiswa->major ?? '-' }}
                                </span>
                            </td>

                            
                            <td>
                                @if(count($prefItems) > 0)
                                    <div style="display: flex; flex-wrap: wrap; gap: 2px; max-width: 500px;">
                                        @foreach($prefItems as $item)
                                            @php
                                                
                                                $parts = explode(':', $item, 2);
                                                $label = count($parts) === 2 ? trim($parts[1]) : trim($item);
                                                $key   = count($parts) === 2 ? trim($parts[0]) : '';
                                            @endphp
                                            <span class="pref-badge" title="{{ $key }}">{{ $label }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color:#aaa; font-style:italic; font-size:12px;">Tidak ada data preferensi.</span>
                                @endif
                            </td>

                            
                            <td style="text-align:center;">
                                @if($log->results_count > 0)
                                    <span class="badge badge-success" style="font-size:12px; padding:5px 10px; border-radius:99px;">
                                        <i class="fa-solid fa-house"></i> {{ $log->results_count }} Kost
                                    </span>
                                @else
                                    <span class="badge badge-danger" style="font-size:12px; padding:5px 10px; border-radius:99px;">
                                        <i class="fa-solid fa-xmark"></i> 0 Kost
                                    </span>
                                @endif
                            </td>

                            
                            <td>
                                <span style="font-size:12px; color:#334155; display:block;">
                                    {{ $log->created_at->format('d-m-Y') }}
                                </span>
                                <span style="font-size:11px; color:#94a3b8;">
                                    {{ $log->created_at->format('H:i:s') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #888; padding: 40px;">
                                <i class="fa-solid fa-magnifying-glass" style="font-size: 32px; color: #ddd; display: block; margin-bottom: 10px;"></i>
                                Belum ada log aktivitas pencarian rekomendasi kost dalam sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
