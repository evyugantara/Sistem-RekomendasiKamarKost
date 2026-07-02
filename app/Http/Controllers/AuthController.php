<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfilMahasiswa;
use App\Models\ProfilPengelola;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            
            if ($user->status === 'inactive') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'username' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
                ]);
            }

            
            if ($user->status === 'pending') {
                Auth::logout();
                return redirect()->route('pending')->with('warning_pending', 'Akun Anda sedang dalam proses verifikasi. Silakan tunggu persetujuan dari Administrator.');
            }

            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user)->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        throw ValidationException::withMessages([
            'username' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
        ]);
    }

    public function registerForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        $campuses = \App\Models\Kampus::all();
        return view('auth.register', compact('campuses'));
    }

    public function registerMahasiswa(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'phone' => 'required|string',
            'address' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',
            'status' => 'active',
        ]);

        ProfilMahasiswa::create([
            'user_id' => $user->id,
            'nim' => null,
            'university' => null,
            'major' => null,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        Auth::login($user);

        return redirect()->route('mahasiswa.dashboard')->with('success', 'Registrasi berhasil! Selamat datang di dashboard Anda.');
    }

    public function registerPengelola(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'ktp_number' => 'required|string|min:16',
            'phone' => 'required|string',
            'address' => 'required|string',
            'ktp_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'kost_name' => 'required|string|max:255',
            'kost_image' => 'required|file|image|mimes:jpeg,png,jpg|max:2048',
            'kost_address' => 'required|string',
            'kost_latitude' => 'required|numeric',
            'kost_longitude' => 'required|numeric',
        ]);

        
        $ktpFileName = null;
        if ($request->hasFile('ktp_file')) {
            $file = $request->file('ktp_file');
            $ktpFileName = 'ktp_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/ktp'), $ktpFileName);
        }

        
        $kostImageName = null;
        if ($request->hasFile('kost_image')) {
            $file = $request->file('kost_image');
            $kostImageName = 'kost_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kost'), $kostImageName);
        }

        
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pengelola',
            'status' => 'pending', 
        ]);

        
        ProfilPengelola::create([
            'user_id' => $user->id,
            'ktp_number' => $request->ktp_number,
            'phone' => $request->phone,
            'address' => $request->address,
            'ktp_file' => 'uploads/ktp/' . $ktpFileName,
        ]);

        
        $defaultCampus = \App\Models\Kampus::first();
        $kampusId = $defaultCampus ? $defaultCampus->id : 1;

        
        $kost = \App\Models\Kost::create([
            'user_id' => $user->id,
            'kampus_id' => $kampusId,
            'name' => $request->kost_name,
            'price' => 0.00,
            'address' => $request->kost_address,
            'latitude' => $request->kost_latitude,
            'longitude' => $request->kost_longitude,
            'description' => 'Draft kost diajukan saat registrasi.',
        ]);

        
        if ($kostImageName) {
            \App\Models\FotoKost::create([
                'kost_id' => $kost->id,
                'image_path' => 'uploads/kost/' . $kostImageName,
                'is_primary' => true,
            ]);
        }

        return redirect()->route('pending')->with('success_pending', 'Pengajuan registrasi Anda berhasil dikirim! Akun Anda sedang dalam proses verifikasi oleh Administrator.');
    }

    public function pending()
    {
        return view('auth.pending');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar.');
    }

    protected function redirectBasedOnRole($user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'pengelola') {
            return redirect()->route('pengelola.dashboard');
        } else {
            return redirect()->route('mahasiswa.dashboard');
        }
    }
}
