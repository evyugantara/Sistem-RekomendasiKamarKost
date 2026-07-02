<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Rekomendasi Kost</title>
    
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    
    <link rel="stylesheet" href="{{ asset('css/dashboard-style.css') }}?v={{ time() }}">
</head>
<body class="auth-wrapper">
    <div class="auth-box">
        <div class="auth-logo">
            <a href="{{ route('home') }}"><b>KOST MAHASISWA</b> CBF</a>
        </div>
        
        <p class="msg">Silakan masuk untuk mengakses sistem rekomendasi</p>
        
        @if($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 3px; font-size: 13px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 3px; font-size: 13px; margin-bottom: 15px; border: 1px solid #c3e6cb;">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="post">
            @csrf
            <div class="form-group" style="position: relative;">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username') }}" required>
                <i class="fa fa-user" style="position: absolute; right: 10px; bottom: 10px; color: #ccc;"></i>
            </div>
            
            <div class="form-group" style="position: relative; margin-top: 15px;">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                <i class="fa fa-lock" style="position: absolute; right: 10px; bottom: 10px; color: #ccc;"></i>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 5px; font-size: 13px; cursor: pointer;">
                    <input type="checkbox" name="remember"> Ingat Saya
                </label>
            </div>
            
            <button type="submit" class="btn-custom btn-primary-custom" style="width: 100%; height: 40px; font-size: 15px; font-weight: bold; border-radius: 4px;">
                <i class="fa-solid fa-right-to-bracket"></i> MASUK
            </button>
        </form>
        
        <div style="margin-top: 20px; text-align: center; font-size: 13px;">
            Belum punya akun? <a href="{{ route('register') }}" style="font-weight: bold;">Daftar Sekarang</a>
        </div>
    </div>
</body>
</html>
