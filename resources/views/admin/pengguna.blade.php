@extends('layouts.dashboard')

@section('title', 'Kelola Pengguna')
@section('header-title', 'Kelola Akun Pengguna')
@section('breadcrumb-active', 'Kelola Pengguna')

@section('content')
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><i class="fa-solid fa-users-gear"></i> Daftar Akun Pengguna Terdaftar</h3>
    </div>
    
    
    <div class="box-body" style="background-color: #fafafa; border-bottom: 1px solid #f4f4f4; padding: 15px;">
        <form action="{{ route('admin.pengguna') }}" method="get">
            <div class="grid-3" style="grid-template-columns: 2fr 1fr 1fr; gap: 10px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, username, atau email..." value="{{ request('search') }}">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <select name="role" class="form-control">
                        <option value="">-- Semua Peran --</option>
                        <option value="mahasiswa" {{ request('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="pengelola" {{ request('role') == 'pengelola' ? 'selected' : '' }}>Pengelola Kost</option>
                    </select>
                </div>
                <div style="display: flex; gap: 5px;">
                    <button type="submit" class="btn-custom btn-primary-custom" style="flex: 1;"><i class="fa fa-filter"></i> Filter</button>
                    @if(request()->anyFilled(['search', 'role']))
                        <a href="{{ route('admin.pengguna') }}" class="btn-custom" style="background-color: #ddd; color: #333;"><i class="fa fa-undo"></i> Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    
    <div class="box-body table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Pengguna</th>
                    <th>Kontak & Email</th>
                    <th>Peran (Role)</th>
                    <th>Detail Identitas (NIM/KTP)</th>
                    <th style="width: 120px;">Status Akun</th>
                    <th style="width: 150px; text-align: center;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $u)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $u->name }}</strong><br>
                            <span style="font-size: 11.5px; color: #777; font-family: monospace;">username: {{ $u->username }}</span>
                        </td>
                        <td>
                            {{ $u->email }}<br>
                            <span style="font-size: 11.5px; color: #555;">
                                <i class="fa fa-phone"></i> 
                                @if($u->role === 'mahasiswa')
                                    {{ $u->profilMahasiswa->phone ?? '-' }}
                                @else
                                    {{ $u->profilPengelola->phone ?? '-' }}
                                @endif
                            </span>
                        </td>
                        <td>
                            @if($u->role === 'mahasiswa')
                                <span class="badge badge-info"><i class="fa-solid fa-user-graduate"></i> Mahasiswa</span>
                            @else
                                <span class="badge badge-success"><i class="fa-solid fa-user-tie"></i> Pengelola Kost</span>
                            @endif
                        </td>
                        <td style="font-size: 12.5px;">
                            @if($u->role === 'mahasiswa')
                                <strong>NIM:</strong> {{ $u->profilMahasiswa->nim ?? '-' }}<br>
                                <span style="font-size: 11px; color: #666; line-height: 1.2; display: block; max-width: 250px;">Alamat: {{ $u->profilMahasiswa->address ?? '-' }}</span>
                            @else
                                <strong>KTP:</strong> {{ $u->profilPengelola->ktp_number ?? '-' }}<br>
                                <span style="font-size: 11px; color: #666; line-height: 1.2; display: block; max-width: 250px;">Alamat: {{ $u->profilPengelola->address ?? '-' }}</span>
                            @endif
                        </td>
                        <td>
                            @if($u->status === 'active')
                                <span class="badge badge-success"><i class="fa fa-check"></i> Aktif</span>
                            @else
                                <span class="badge badge-danger"><i class="fa fa-ban"></i> Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <form action="{{ route('admin.pengguna.toggle', $u->id) }}" method="post">
                                @csrf
                                @if($u->status === 'active')
                                    <button type="submit" class="btn-custom btn-danger-custom btn-xs" style="width: 100%;">
                                        <i class="fa-solid fa-user-slash"></i> Nonaktifkan
                                    </button>
                                @else
                                    <button type="submit" class="btn-custom btn-success-custom btn-xs" style="width: 100%;">
                                        <i class="fa-solid fa-user-check"></i> Aktifkan Akun
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #888; padding: 25px;">Tidak ada akun pengguna terdaftar yang sesuai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
