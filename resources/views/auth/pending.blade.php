<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Sedang Diverifikasi - Sistem Rekomendasi Kost</title>
    
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    
    <link rel="stylesheet" href="{{ asset('css/dashboard-style.css') }}">
</head>
<body class="auth-wrapper">
    <div class="auth-box" style="width: 480px; text-align: center; padding: 35px 30px;">
        <div class="auth-logo" style="margin-bottom: 20px;">
            <a href="{{ route('home') }}"><b>KOST</b>-CBF</a>
        </div>

        <div style="margin-bottom: 25px;">
            <div style="width: 80px; height: 80px; background-color: #fef3c7; color: #d97706; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 36px; margin-bottom: 15px; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2);">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <h3 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Pendaftaran Sedang Diverifikasi</h3>
            <p style="font-size: 13px; color: #64748b; line-height: 1.5;">Terima kasih telah melakukan registrasi sebagai pengelola kost. Akun Anda saat ini masuk dalam antrean peninjauan oleh tim Administrator.</p>
        </div>

        @if(session('success_pending'))
            <div style="background-color: #dcfce7; color: #16a34a; padding: 15px; border-radius: 8px; font-size: 12.5px; margin-bottom: 20px; border: 1px solid #bbf7d0; text-align: left; line-height: 1.5;">
                <i class="fa fa-check-circle" style="margin-right: 5px; font-size: 14px;"></i> {{ session('success_pending') }}
            </div>
        @endif

        @if(session('warning_pending'))
            <div style="background-color: #fffbeb; color: #d97706; padding: 15px; border-radius: 8px; font-size: 12.5px; margin-bottom: 20px; border: 1px solid #fde68a; text-align: left; line-height: 1.5;">
                <i class="fa-solid fa-circle-exclamation" style="margin-right: 5px; font-size: 14px;"></i> {{ session('warning_pending') }}
            </div>
        @endif

        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; text-align: left; font-size: 12px; color: #475569; margin-bottom: 25px; line-height: 1.6;">
            <strong style="color: #0f172a; display: block; margin-bottom: 5px;"><i class="fa-solid fa-shield-halved"></i> Apa yang kami periksa?</strong>
            <ul style="margin: 0; padding-left: 20px;">
                <li>Kesesuaian nomor KTP dengan dokumen/berkas yang diunggah.</li>
                <li>Validitas data kost dasar (nama, alamat, kampus terdekat).</li>
                <li>Status keaktifan pengelola untuk menghindari duplikasi data.</li>
            </ul>
        </div>

        <a href="{{ route('home') }}" class="btn-custom btn-primary-custom" style="width: 100%; height: 42px; font-size: 13.5px; font-weight: bold;">
            <i class="fa-solid fa-house"></i> Kembali ke Beranda
        </a>
    </div>
</body>
</html>
