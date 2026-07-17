<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Sistem Rekomendasi Kost</title>
    
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}"></script>
    
    
    <link rel="stylesheet" href="{{ asset('css/dashboard-style.css') }}?v={{ time() }}">
    
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @yield('styles')
</head>
<body>
    <div class="wrapper">
        
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header" style="font-size: 13.5px; font-weight: bold; white-space: nowrap;">
                <i class="fa-solid fa-house-laptop"></i> RUMAH KOST CBF
            </div>
            
            <div class="sidebar-user">
                <span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}</span>
                <div class="user-info">
                    <span class="name">{{ auth()->user()->name ?? 'Pengguna' }}</span>
                    <span class="status">
                        @if(auth()->user()->role == 'admin')
                            Admin
                        @elseif(auth()->user()->role == 'pengelola')
                            Pengelola Kost
                        @else
                            Penghuni
                        @endif
                    </span>
                </div>
            </div>
            
            
            <div class="sidebar-search">
                <div class="search-container">
                    <input type="text" class="search-input" id="menuSearch" placeholder="Pencarian Menu...">
                    <button class="search-btn"><i class="fa fa-search"></i></button>
                </div>
            </div>
            
            
            <ul class="sidebar-menu" id="sidebarMenu">
                <li class="header-menu">MENU NAVIGASI</li>
                
                @if(auth()->user()->role == 'admin')
                    
                    <li class="{{ Request::is('admin') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa-solid fa-gauge"></i>
                            <span>Dashboard Monitoring</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('admin/pengguna*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pengguna') }}">
                            <i class="fa-solid fa-users-gear"></i>
                            <span>Kelola Pengguna</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('admin/pengajuan*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pengajuan') }}">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Verifikasi Pengelola</span>
                            @php $pendingCount = \App\Models\User::where('role', 'pengelola')->where('status', 'pending')->count(); @endphp
                            @if($pendingCount > 0)
                                <span class="badge badge-danger" style="margin-left: auto; padding: 2px 6px; font-size: 10px; border-radius: 10px; font-weight: bold; background-color: #ef4444; border-color: #fecaca; color: #fff; line-height: 1;">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="{{ Request::is('admin/kriteria*') ? 'active' : '' }}">
                        <a href="{{ route('admin.kriteria') }}">
                            <i class="fa-solid fa-list-check"></i>
                            <span>Kelola Kriteria Kost</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('admin/log*') ? 'active' : '' }}">
                        <a href="{{ route('admin.logs') }}">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span>Log Aktivitas CBF</span>
                        </a>
                    </li>
                @elseif(auth()->user()->role == 'pengelola')
                    
                    <li class="{{ Request::is('pengelola') ? 'active' : '' }}">
                        <a href="{{ route('pengelola.dashboard') }}">
                            <i class="fa-solid fa-gauge"></i>
                            <span>Dashboard Pengelola</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('pengelola/kost*') ? 'active' : '' }}">
                        <a href="{{ route('pengelola.kost') }}">
                            <i class="fa-solid fa-house-chimney-window"></i>
                            <span>Kelola Data Kost</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('pengelola/profil*') ? 'active' : '' }}">
                        <a href="{{ route('pengelola.profil') }}">
                            <i class="fa-solid fa-user-tie"></i>
                            <span>Profil Pengelola</span>
                        </a>
                    </li>
                @elseif(auth()->user()->role == 'mahasiswa')
                    
                    <li class="{{ Request::is('mahasiswa') ? 'active' : '' }}">
                        <a href="{{ route('mahasiswa.dashboard') }}">
                            <i class="fa-solid fa-gauge"></i>
                            <span>Dashboard Penghuni</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('mahasiswa/rekomendasi*') ? 'active' : '' }}">
                        <a href="{{ route('mahasiswa.rekomendasi') }}">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                            <span>Cari Rekomendasi</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('mahasiswa/profil*') ? 'active' : '' }}">
                        <a href="{{ route('mahasiswa.profil') }}">
                            <i class="fa-solid fa-user-graduate"></i>
                            <span>Profil Penghuni</span>
                        </a>
                    </li>
                @endif
                
                <li>
                    <a href="{{ route('home') }}" target="_blank">
                        <i class="fa-solid fa-globe"></i>
                        <span>Lihat Landing Page</span>
                    </a>
                </li>
            </ul>
            
            
            <div style="border-top: 1px solid #e2e8f0; padding: 12px 15px; background-color: #f8fafc; flex-shrink: 0; margin-top: auto;">
                <a href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();" 
                   style="display: flex; align-items: center; gap: 10px; color: #dc2626 !important; font-weight: 600; font-size: 13.5px; text-decoration: none; padding: 8px 12px; border-radius: 4px; transition: background 0.2s;"
                   onmouseover="this.style.backgroundColor='#fee2e2'" 
                   onmouseout="this.style.backgroundColor='transparent'">
                    <i class="fa-solid fa-power-off" style="color: #dc2626 !important;"></i>
                    <span>Keluar / Logout</span>
                </a>
                <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </aside>
        
        
        <main class="main-content">
            
            <header class="main-header">
                <div class="toggle-btn" id="sidebarToggle">
                    <i class="fa-solid fa-bars"></i>
                </div>
                <div class="user-menu">
                    <span>Selamat datang, <strong>{{ auth()->user()->name }}</strong></span>
                </div>
            </header>
            
            
            <div class="content-header">
                <h1>@yield('header-title', 'Dashboard')</h1>
                <ul class="breadcrumb">
                    <li><a href="#"><i class="fa-solid fa-house"></i> Home</a></li>
                    <li class="active">@yield('breadcrumb-active', 'Dashboard')</li>
                </ul>
            </div>
            
            
            <div class="content-body">
                @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: "{{ session('success') }}",
                                confirmButtonColor: '#2563eb'
                            });
                        });
                    </script>
                @endif
                
                @if(session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: "{{ session('error') }}",
                                confirmButtonColor: '#2563eb'
                            });
                        });
                    </script>
                @endif

                @if($errors->any())
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal!',
                                html: `{!! implode('<br>', $errors->all()) !!}`,
                                confirmButtonColor: '#2563eb'
                            });
                        });
                    </script>
                @endif

                @yield('content')
            </div>
        </main>
    </div>


    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            const toggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            if (toggle && sidebar) {
                toggle.addEventListener('click', function() {
                    if (sidebar.style.width === '0px' || sidebar.style.display === 'none') {
                        sidebar.style.width = '250px';
                        sidebar.style.display = 'flex';
                    } else {
                        sidebar.style.width = '0px';
                        sidebar.style.display = 'none';
                    }
                });
            }
            
            
            const menuSearch = document.getElementById('menuSearch');
            const sidebarMenu = document.getElementById('sidebarMenu');
            if (menuSearch && sidebarMenu) {
                menuSearch.addEventListener('input', function() {
                    const filter = menuSearch.value.toUpperCase();
                    const liItems = sidebarMenu.getElementsByTagName('li');
                    
                    for (let i = 0; i < liItems.length; i++) {
                        const li = liItems[i];
                        if (li.classList.contains('header-menu')) continue; 
                        
                        const textVal = li.textContent || li.innerText;
                        if (textVal.toUpperCase().indexOf(filter) > -1) {
                            li.style.display = "";
                        } else {
                            li.style.display = "none";
                        }
                    }
                });
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
