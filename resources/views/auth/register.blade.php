<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - Sistem Rekomendasi Kost</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Style CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard-style.css') }}?v={{ time() }}">

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}"></script>
</head>
<body class="auth-wrapper" style="align-items: flex-start; padding-top: 50px; padding-bottom: 50px;">
    <div class="auth-box" style="width: 500px; max-width: 100%;">
        <div class="auth-logo">
            <a href="{{ route('home') }}"><b>KOST MAHASISWA</b> CBF</a>
        </div>
        
        <p class="msg" style="margin-bottom: 10px;">Daftar akun baru untuk mulai menggunakan sistem</p>
        
        <!-- Tab Pemilihan Peran -->
        <div class="auth-tabs">
            <div class="auth-tab active" id="tabMahasiswa" onclick="switchForm('mahasiswa')">
                <i class="fa-solid fa-user-graduate"></i> Mahasiswa
            </div>
            <div class="auth-tab" id="tabPengelola" onclick="switchForm('pengelola')">
                <i class="fa-solid fa-user-tie"></i> Pengelola Kost
            </div>
        </div>

        @if($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 3px; font-size: 13px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- FORM REGISTRASI MAHASISWA -->
        <div id="formMahasiswaContainer" class="auth-tab-content active">
            <form action="{{ route('register.mahasiswa') }}" method="post">
                @csrf
                <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Username" value="{{ old('username') }}" required>
                    </div>
                </div>
                
                <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                    <div class="form-group">
                        <label>Alamat Email</label>
                        <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label>No. Telepon / WA (Aktif)</label>
                        <input type="text" name="phone" class="form-control" placeholder="Contoh: 0812XXXXXXXX" value="{{ old('phone') }}" required>
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 10px;">
                    <label>Jenis Kelamin</label>
                    <select name="gender" class="form-control" required>
                        <option value="">Pilih...</option>
                        <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                
                <div class="form-group" style="margin-top: 10px;">
                    <label>Alamat Asal</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Alamat lengkap asal..." required>{{ old('address') }}</textarea>
                </div>
                
                <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn-custom btn-primary-custom" style="width: 100%; height: 40px; margin-top: 15px; font-weight: bold; border-radius: 4px;">
                    <i class="fa-solid fa-user-plus"></i> DAFTAR SEBAGAI MAHASISWA
                </button>
            </form>
        </div>
        
        <!-- FORM REGISTRASI PENGELOLA -->
        <div id="formPengelolaContainer" class="auth-tab-content">
            <form action="{{ route('register.pengelola') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Username" value="{{ old('username') }}" required>
                    </div>
                </div>
                
                <div class="grid-2" style="grid-template-columns: 1.2fr 1fr; gap: 10px; margin-top: 10px;">
                    <div class="form-group">
                        <label>Alamat Email</label>
                        <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label>No. Telepon / WA (Aktif)</label>
                        <input type="text" name="phone" class="form-control" placeholder="Contoh: 0812XXXXXXXX" value="{{ old('phone') }}" required>
                    </div>
                </div>
                
                <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                    <div class="form-group">
                        <label>No. KTP</label>
                        <input type="text" name="ktp_number" class="form-control" placeholder="16 digit No. KTP" value="{{ old('ktp_number') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Upload Foto KTP / Dokumen Pendukung</label>
                        <input type="file" name="ktp_file" class="form-control" accept="image/*,application/pdf" style="padding-top: 5px;" required>
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 10px;">
                    <label>Alamat Lengkap Rumah Pengelola</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Alamat lengkap rumah pengelola..." required>{{ old('address') }}</textarea>
                </div>
                
                <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                    </div>
                </div>

                <!-- Draft Data Kost Awal -->
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0; margin-bottom: 10px;">
                    <h4 style="font-size: 13.5px; font-weight: 700; color: #0f172a; margin-bottom: 3px;"><i class="fa-solid fa-house-chimney"></i> Draft Data Kost Awal</h4>
                    <p style="font-size: 11.5px; color: #64748b; margin-bottom: 8px;">Masukkan data detail kost Anda untuk melengkapi pengajuan awal.</p>
                </div>

                <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group">
                        <label>Nama Kost</label>
                        <input type="text" name="kost_name" class="form-control" placeholder="Contoh: Kost Pasir Gede Indah" value="{{ old('kost_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Foto Utama Kost</label>
                        <input type="file" name="kost_image" class="form-control" accept="image/*" style="padding-top: 5px;" required>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 10px;">
                    <label>Alamat Lengkap Lokasi Kost</label>
                    <textarea name="kost_address" class="form-control" rows="2" placeholder="Tuliskan alamat lengkap lokasi kost..." required>{{ old('kost_address') }}</textarea>
                </div>

                <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                    <div class="form-group">
                        <label>Latitude Kost</label>
                        <input type="text" name="kost_latitude" id="kost_latitude" class="form-control" placeholder="Mencari di peta..." value="{{ old('kost_latitude') }}" required readonly>
                    </div>
                    <div class="form-group">
                        <label>Longitude Kost</label>
                        <input type="text" name="kost_longitude" id="kost_longitude" class="form-control" placeholder="Mencari di peta..." value="{{ old('kost_longitude') }}" required readonly>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 10px;">
                    <label>Pilih Lokasi Kost pada Peta (Klik untuk memindahkan pin)</label>
                    <div id="registerKostMap" style="height: 220px; border-radius: 6px; border: 1px solid #cbd5e1; z-index: 1;"></div>
                </div>
                
                <button type="submit" class="btn-custom btn-success-custom" style="width: 100%; height: 40px; margin-top: 20px; font-weight: bold; border-radius: 4px;">
                    <i class="fa-solid fa-paper-plane"></i> KIRIM PENGAJUAN PENDAFTARAN
                </button>
            </form>
        </div>
        
        <div style="margin-top: 20px; text-align: center; font-size: 13px;">
            Sudah memiliki akun? <a href="{{ route('login') }}" style="font-weight: bold;">Masuk Sekarang</a>
        </div>
    </div>
    
    <script>
        function switchForm(role) {
            const tabM = document.getElementById('tabMahasiswa');
            const tabP = document.getElementById('tabPengelola');
            const formM = document.getElementById('formMahasiswaContainer');
            const formP = document.getElementById('formPengelolaContainer');
            
            if (role === 'mahasiswa') {
                tabM.classList.add('active');
                tabP.classList.remove('active');
                formM.classList.add('active');
                formP.classList.remove('active');
            } else {
                tabP.classList.add('active');
                tabM.classList.remove('active');
                formP.classList.add('active');
                formM.classList.remove('active');
                
                // Inisialisasi peta setelah tab aktif & kontainer terlihat
                setTimeout(function() {
                    initRegisterMap();
                }, 150);
            }
        }
        
        // Mempertahankan tab pengelola jika ada kesalahan validasi untuk pengelola
        @if(old('ktp_number') || old('kost_name'))
            switchForm('pengelola');
        @endif

        // --- Peta Google Maps Registrasi ---
        let regMap;
        let regMarker;

        function initRegisterMap() {
            if (regMap) {
                google.maps.event.trigger(regMap, 'resize');
                return;
            }

            // Koordinat pusat default: Cianjur (-6.81245000, 107.14090000)
            const defaultLat = -6.81245000;
            const defaultLng = 107.14090000;

            const latInput = document.getElementById('kost_latitude');
            const lngInput = document.getElementById('kost_longitude');

            // Set koordinat input default jika masih kosong
            if (!latInput.value) {
                latInput.value = defaultLat.toFixed(8);
            }
            if (!lngInput.value) {
                lngInput.value = defaultLng.toFixed(8);
            }

            const currentLat = parseFloat(latInput.value);
            const currentLng = parseFloat(lngInput.value);

            const initialLatLng = { lat: currentLat, lng: currentLng };

            regMap = new google.maps.Map(document.getElementById('registerKostMap'), {
                center: initialLatLng,
                zoom: 14,
                mapTypeControl: true,
                streetViewControl: false
            });

            regMarker = new google.maps.Marker({
                position: initialLatLng,
                map: regMap,
                draggable: true
            });

            // Perbarui input koordinat saat marker digeser (drag)
            google.maps.event.addListener(regMarker, 'dragend', function (event) {
                latInput.value = event.latLng.lat().toFixed(8);
                lngInput.value = event.latLng.lng().toFixed(8);
            });

            // Perbarui koordinat dan pin saat peta diklik
            google.maps.event.addListener(regMap, 'click', function (event) {
                const clickedLatLng = event.latLng;
                regMarker.setPosition(clickedLatLng);
                latInput.value = clickedLatLng.lat().toFixed(8);
                lngInput.value = clickedLatLng.lng().toFixed(8);
            });
        }
    </script>
</body>
</html>
