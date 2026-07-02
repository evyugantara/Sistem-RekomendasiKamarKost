@extends('layouts.dashboard')

@section('title', 'Tambah Kost Baru')
@section('header-title', 'Tambah Kost Baru')
@section('breadcrumb-active', 'Tambah Kost')

@section('content')
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><i class="fa-solid fa-house-medical"></i> Formulir Data Kost Baru</h3>
    </div>
    
    <div class="box-body">
        <form action="{{ route('pengelola.kost.simpan') }}" method="post">
            @csrf
            
            <div class="grid-2">
                
                <div>
                    <h4 style="border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 15px; font-weight: 600; color: #555;">
                        <i class="fa-solid fa-file-invoice"></i> Detail Informasi Kost
                    </h4>
                    
                    <div class="form-group">
                        <label>Nama Kost</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Kost Sakinah AC Putra" value="{{ old('name') }}" required>
                    </div>

                    <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="form-group">
                            <label>Harga Sewa Bulanan (Rupiah)</label>
                            <input type="number" name="price" class="form-control" placeholder="Contoh: 850000" value="{{ old('price') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Acuan Kampus Terdekat</label>
                            <select name="kampus_id" id="kampusSelect" class="form-control" required>
                                @if(count($campuses) > 1)
                                    <option value="">Pilih Kampus...</option>
                                @endif
                                @foreach($campuses as $camp)
                                    <option value="{{ $camp->id }}" data-lat="{{ $camp->latitude }}" data-lng="{{ $camp->longitude }}" {{ (old('kampus_id') == $camp->id || count($campuses) == 1) ? 'selected' : '' }}>
                                        {{ $camp->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Alamat Lengkap Kost</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Nama Jalan, Nomor, RT/RW, Kelurahan, Kecamatan, Kota..." required>{{ old('address') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Tambahan</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Jelaskan peraturan kost, lingkungan, dll...">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid-2" style="grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div class="form-group">
                            <label>Latitude Lokasi</label>
                            <input type="text" name="latitude" id="latInput" class="form-control" value="{{ old('latitude', '-6.81245000') }}" required readonly style="background-color: #f9f9f9;">
                        </div>
                        <div class="form-group">
                            <label>Longitude Lokasi</label>
                            <input type="text" name="longitude" id="lngInput" class="form-control" value="{{ old('longitude', '107.14090000') }}" required readonly style="background-color: #f9f9f9;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 5px; margin-bottom: 0;">
                        <button type="button" class="btn-custom btn-success-custom" id="btnGetCurrentLocation" style="width: 100%; font-weight: 600;">
                            <i class="fa-solid fa-location-crosshairs"></i> Gunakan Lokasi Saat Ini
                        </button>
                    </div>
                </div>
                
                
                <div>
                    <h4 style="border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 15px; font-weight: 600; color: #555;">
                        <i class="fa-solid fa-map-location-dot"></i> Tentukan Titik Koordinat Kost Pada Peta
                    </h4>
                    <p style="color: #666; font-size: 12.5px; margin-bottom: 10px;">Silakan geser pin biru (Kost) ke posisi gedung kost Anda berada atau klik langsung di area peta. Pin merah merupakan kampus acuan yang Anda pilih.</p>
                    
                    
                    <div id="coordinateMap" class="map-wrapper" style="height: 380px;"></div>
                    
                    <div style="font-size: 11.5px; color: #777; margin-top: 5px; display: flex; align-items: center; gap: 5px;">
                        <i class="fa-solid fa-circle-info"></i> Titik koordinat Latitude dan Longitude di samping akan terisi otomatis berdasarkan penandaan Anda pada peta di atas.
                    </div>
                </div>
            </div>
            
            <div class="box-footer" style="padding-left: 0; padding-right: 0; background: none; border-top: 1px solid #eee; margin-top: 20px; padding-top: 15px;">
                <button type="submit" class="btn-custom btn-primary-custom" style="padding: 10px 25px; font-weight: bold; font-size: 14px;">
                    <i class="fa-solid fa-save"></i> Daftarkan Kost Baru
                </button>
                <a href="{{ route('pengelola.kost') }}" class="btn-custom" style="background-color: #ddd; color: #333; padding: 10px 20px;">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        var defaultLat = -6.81245000;
        var defaultLng = 107.14090000;

        
        var kampusSelect = document.getElementById('kampusSelect');
        if (kampusSelect && kampusSelect.selectedIndex !== -1) {
            var selectedOption = kampusSelect.options[kampusSelect.selectedIndex];
            if (selectedOption && selectedOption.value !== "") {
                defaultLat = parseFloat(selectedOption.getAttribute('data-lat'));
                defaultLng = parseFloat(selectedOption.getAttribute('data-lng'));
            }
        }

        
        var latInputVal = document.getElementById('latInput').value;
        var lngInputVal = document.getElementById('lngInput').value;
        var initialKostLat = parseFloat(latInputVal) || (defaultLat - 0.001);
        var initialKostLng = parseFloat(lngInputVal) || (defaultLng + 0.001);

        
        var map = new google.maps.Map(document.getElementById('coordinateMap'), {
            center: { lat: initialKostLat, lng: initialKostLng },
            zoom: 14,
            mapTypeControl: true,
            streetViewControl: false
        });

        
        var campusMarker = null;
        var kostMarker = new google.maps.Marker({
            position: { lat: initialKostLat, lng: initialKostLng },
            map: map,
            draggable: true,
            icon: {
                url: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                scaledSize: new google.maps.Size(25, 41),
                anchor: new google.maps.Point(12, 41)
            }
        });

        
        function updateCoordinates(lat, lng) {
            document.getElementById('latInput').value = lat.toFixed(8);
            document.getElementById('lngInput').value = lng.toFixed(8);
        }

        
        function showCampusMarker() {
            if (kampusSelect && kampusSelect.selectedIndex !== -1) {
                var selectedOption = kampusSelect.options[kampusSelect.selectedIndex];
                if (selectedOption && selectedOption.value !== "") {
                    var cLat = parseFloat(selectedOption.getAttribute('data-lat'));
                    var cLng = parseFloat(selectedOption.getAttribute('data-lng'));

                    if (campusMarker) {
                        campusMarker.setMap(null);
                    }

                    campusMarker = new google.maps.Marker({
                        position: { lat: cLat, lng: cLng },
                        map: map,
                        title: "Kampus: " + selectedOption.text,
                        icon: {
                            url: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                            scaledSize: new google.maps.Size(25, 41),
                            anchor: new google.maps.Point(12, 41)
                        }
                    });

                    
                    var bounds = new google.maps.LatLngBounds();
                    bounds.extend(new google.maps.LatLng(cLat, cLng));
                    bounds.extend(kostMarker.getPosition());
                    map.fitBounds(bounds);
                }
            }
        }
        showCampusMarker();

        
        google.maps.event.addListener(kostMarker, 'dragend', function(event) {
            updateCoordinates(event.latLng.lat(), event.latLng.lng());
        });

        
        google.maps.event.addListener(map, 'click', function(event) {
            var clickedLatLng = event.latLng;
            kostMarker.setPosition(clickedLatLng);
            updateCoordinates(clickedLatLng.lat(), clickedLatLng.lng());
        });

        
        if (kampusSelect) {
            kampusSelect.addEventListener('change', function() {
                var selectedOption = kampusSelect.options[kampusSelect.selectedIndex];
                if (selectedOption.value !== "") {
                    var cLat = parseFloat(selectedOption.getAttribute('data-lat'));
                    var cLng = parseFloat(selectedOption.getAttribute('data-lng'));

                    
                    if (campusMarker) {
                        campusMarker.setMap(null);
                    }

                    
                    campusMarker = new google.maps.Marker({
                        position: { lat: cLat, lng: cLng },
                        map: map,
                        title: "Kampus: " + selectedOption.text,
                        icon: {
                            url: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                            scaledSize: new google.maps.Size(25, 41),
                            anchor: new google.maps.Point(12, 41)
                        }
                    });

                    
                    var newKostLatLng = new google.maps.LatLng(cLat - 0.001, cLng + 0.001);
                    kostMarker.setPosition(newKostLatLng);
                    updateCoordinates(cLat - 0.001, cLng + 0.001);

                    
                    var bounds = new google.maps.LatLngBounds();
                    bounds.extend(new google.maps.LatLng(cLat, cLng));
                    bounds.extend(newKostLatLng);
                    map.fitBounds(bounds);
                }
            });
        }

        
        var btnGetLoc = document.getElementById('btnGetCurrentLocation');
        if (btnGetLoc) {
            btnGetLoc.addEventListener('click', function() {
                if (navigator.geolocation) {
                    btnGetLoc.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mendapatkan Lokasi...';
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var lat = position.coords.latitude;
                        var lng = position.coords.longitude;
                        
                        var newLatLng = new google.maps.LatLng(lat, lng);
                        kostMarker.setPosition(newLatLng);
                        map.setCenter(newLatLng);
                        map.setZoom(16);
                        
                        updateCoordinates(lat, lng);
                        
                        btnGetLoc.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Gunakan Lokasi Saat Ini';
                    }, function(error) {
                        btnGetLoc.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Gunakan Lokasi Saat Ini';
                        var errorMsg = 'Gagal mendapatkan lokasi: ';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMsg += 'Izin akses lokasi ditolak oleh browser.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMsg += 'Informasi lokasi tidak tersedia.';
                                break;
                            case error.TIMEOUT:
                                errorMsg += 'Waktu permintaan habis.';
                                break;
                            default:
                                errorMsg += 'Kesalahan tidak dikenal.';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Geolokasi Gagal',
                            text: errorMsg,
                            confirmButtonColor: '#3c8dbc'
                        });
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Didukung',
                        text: 'Browser Anda tidak mendukung deteksi lokasi (Geolocation).',
                        confirmButtonColor: '#3c8dbc'
                    });
                }
            });
        }
    });
</script>
@endsection
