@extends('layouts.dashboard')

@section('title', 'Verifikasi Pengelola')
@section('header-title', 'Pengajuan Registrasi Pengelola')
@section('breadcrumb-active', 'Verifikasi Pengelola')

@section('content')
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><i class="fa-solid fa-shield-halved"></i> Daftar Pengajuan Registrasi & Kost Baru</h3>
    </div>

    <!-- Tabel Pengajuan -->
    <div class="box-body table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Identitas Pengelola</th>
                    <th>No. KTP & Dokumen</th>
                    <th>Detail Draft Kost Diajukan</th>
                    <th style="width: 130px; text-align: center;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuans as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $p->name }}</strong><br>
                            <span style="font-size: 11.5px; color: #64748b; font-family: monospace;">username: {{ $p->username }}</span><br>
                            <span style="font-size: 12px; color: #334155;"><i class="fa-solid fa-envelope"></i> {{ $p->email }}</span><br>
                            <span style="font-size: 12px; color: #334155;"><i class="fa-solid fa-phone"></i> {{ $p->profilPengelola->phone ?? '-' }}</span>
                        </td>
                        <td>
                            <strong style="font-size: 13px;">No. KTP:</strong> {{ $p->profilPengelola->ktp_number ?? '-' }}<br>
                            @if($p->profilPengelola && $p->profilPengelola->ktp_file)
                                <a href="{{ asset($p->profilPengelola->ktp_file) }}" target="_blank" class="badge badge-info" style="margin-top: 5px; cursor: pointer; text-decoration: none;">
                                    <i class="fa-solid fa-file-invoice"></i> Lihat Dokumen KTP
                                </a>
                            @else
                                <span style="font-size: 11px; color: #a0aec0; font-style: italic;">Tidak ada berkas</span>
                            @endif
                        </td>
                        <td>
                            @if($p->kosts->isNotEmpty())
                                @php $kostDraft = $p->kosts->first(); @endphp
                                <div style="display: flex; gap: 12px; align-items: flex-start;">
                                    @if($kostDraft->fotoUtama() && $kostDraft->fotoUtama() !== 'images/default-kost.jpg')
                                        <a href="{{ asset($kostDraft->fotoUtama()) }}" target="_blank" title="Klik untuk memperbesar gambar">
                                            <img src="{{ asset($kostDraft->fotoUtama()) }}" alt="Foto Kost" style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                        </a>
                                    @else
                                        <div style="width: 70px; height: 70px; background-color: #f1f5f9; border-radius: 6px; display: flex; align-items: center; justify-content: center; border: 1px solid #cbd5e1; color: #94a3b8; font-size: 24px;" title="Tidak ada foto">
                                            <i class="fa-solid fa-image"></i>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <strong style="font-size: 13.5px; color: var(--primary-color);">{{ $kostDraft->name }}</strong><br>
                                        <span style="font-size: 11.5px; color: #475569; display: block; margin-top: 2px;">
                                            <i class="fa-solid fa-map-location-dot"></i> Koordinat: <span style="font-family: monospace;">{{ $kostDraft->latitude }}, {{ $kostDraft->longitude }}</span>
                                        </span>
                                        <span style="font-size: 11.5px; color: #64748b; display: block; margin-top: 2px; max-width: 320px; line-height: 1.35;">
                                            <i class="fa-solid fa-map-pin"></i> Alamat: {{ $kostDraft->address }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <span style="color: #a0aec0; font-style: italic; font-size: 12px;">Draft kost tidak ditemukan</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 6px; align-items: center; justify-content: center;">
                                <button type="button" onclick="verifyPengelola({{ $p->id }}, '{{ $p->name }}', 'approve')" class="btn-custom btn-success-custom btn-xs" style="width: 100%; font-weight: bold; padding: 6px 10px;">
                                    <i class="fa-solid fa-check-double"></i> Setujui
                                </button>
                                <button type="button" onclick="verifyPengelola({{ $p->id }}, '{{ $p->name }}', 'reject')" class="btn-custom btn-danger-custom btn-xs" style="width: 100%; font-weight: bold; padding: 6px 10px;">
                                    <i class="fa-solid fa-xmark"></i> Tolak & Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #64748b; padding: 40px 15px;">
                            <div style="font-size: 40px; color: #cbd5e1; margin-bottom: 12px;">
                                <i class="fa-solid fa-folder-open"></i>
                            </div>
                            <span style="font-size: 14px; font-weight: 500;">Tidak ada pengajuan pendaftaran baru yang perlu diverifikasi.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function verifyPengelola(id, name, action) {
        let actionText = action === 'approve' ? 'menyetujui' : 'menolak & menghapus';
        let confirmColor = action === 'approve' ? '#10b981' : '#ef4444';
        let confirmText = action === 'approve' ? 'Ya, Setujui!' : 'Ya, Tolak!';
        
        Swal.fire({
            title: 'Konfirmasi Verifikasi',
            text: `Apakah Anda yakin ingin ${actionText} pendaftaran pengelola "${name}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#64748b',
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading spinner
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Harap tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Kirim request via AJAX Fetch
                fetch(`/admin/pengajuan/${id}/verifikasi`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ action: action })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#10b981',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Gagal menghubungi server.', 'error');
                });
            }
        });
    }
</script>
@endsection
