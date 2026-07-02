<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ProfilMahasiswa;
use App\Models\ProfilPengelola;
use App\Models\Kampus;
use App\Models\Kriteria;
use App\Models\OpsiKriteria;
use App\Models\Kost;
use App\Models\FotoKost;
use App\Models\AtributKost;
use App\Models\Kamar;
use App\Models\AtributKamar;
use App\Models\KamarFavorit;
use App\Models\LogKontak;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        
        $kampus = Kampus::create([
            'name' => 'Universitas Suryakancana (UNSUR)',
            'latitude' => -6.81245000,
            'longitude' => 107.14090000,
        ]);

        
        $admin = User::create([
            'name' => 'Admin Utama',
            'username' => 'admin',
            'email' => 'admin@kost.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        
        $pengelolaUser = User::create([
            'name' => 'Budi Santoso',
            'username' => 'pengelola',
            'email' => 'pengelola@kost.com',
            'password' => Hash::make('pengelola123'),
            'role' => 'pengelola',
            'status' => 'active',
        ]);
        ProfilPengelola::create([
            'user_id' => $pengelolaUser->id,
            'ktp_number' => '3507123456780001',
            'phone' => '081234567890',
            'address' => 'Jl. Pasir Gede, Cianjur',
        ]);

        
        $mahasiswaUser = User::create([
            'name' => 'Ahmad Hidayat',
            'username' => 'mahasiswa',
            'email' => 'mahasiswa@kost.com',
            'password' => Hash::make('mahasiswa123'),
            'role' => 'mahasiswa',
            'status' => 'active',
        ]);
        ProfilMahasiswa::create([
            'user_id' => $mahasiswaUser->id,
            'nim' => '20051214001',
            'university' => 'Universitas Suryakancana',
            'major' => 'Teknik Informatika',
            'gender' => 'Laki-laki',
            'phone' => '089876543210',
            'address' => 'Jl. KH. Abdullah Bin Nuh, Cianjur',
        ]);

        
        $kriterias = [
            
            [
                'name' => 'Jenis Kost',
                'type' => 'select',
                'category' => 'umum',
                'options' => ['Putra', 'Putri', 'Campur']
            ],
            [
                'name' => 'Harga Sewa',
                'type' => 'select',
                'category' => 'umum',
                'options' => ['< 500 Ribu', '500 Ribu - 1 Juta', '1 Juta - 1.5 Juta', '> 1.5 Juta']
            ],
            [
                'name' => 'Sistem Listrik',
                'type' => 'select',
                'category' => 'umum',
                'options' => ['Listrik Token (Prepaid)', 'Listrik Bulanan (Include)']
            ],
            [
                'name' => 'Sumber Air',
                'type' => 'select',
                'category' => 'umum',
                'options' => ['Air PDAM', 'Air Sumur']
            ],
            [
                'name' => 'Akses Jalan',
                'type' => 'select',
                'category' => 'umum',
                'options' => ['Bisa Masuk Mobil', 'Hanya Masuk Motor']
            ],
            [
                'name' => 'Tempat Parkir',
                'type' => 'select',
                'category' => 'umum',
                'options' => ['Parkir Mobil & Motor', 'Parkir Motor Saja', 'Tidak Ada Parkir']
            ],

            
            [
                'name' => 'Kamar Mandi Dalam',
                'type' => 'select',
                'category' => 'pribadi',
                'options' => ['Ada Kamar Mandi Dalam', 'Kamar Mandi Luar (Tidak Ada)']
            ],
            [
                'name' => 'Jenis Kloset',
                'type' => 'select',
                'category' => 'pribadi',
                'options' => ['Kloset Duduk', 'Kloset Jongkok']
            ],
            [
                'name' => 'Kasur',
                'type' => 'select',
                'category' => 'pribadi',
                'options' => ['Ada Kasur', 'Tidak Ada Kasur']
            ],
            [
                'name' => 'Lemari Pakaian',
                'type' => 'select',
                'category' => 'pribadi',
                'options' => ['Ada Lemari Pakaian', 'Tidak Ada Lemari Pakaian']
            ],
            [
                'name' => 'Meja & Kursi Belajar',
                'type' => 'select',
                'category' => 'pribadi',
                'options' => ['Ada Meja & Kursi Belajar', 'Tidak Ada Meja & Kursi Belajar']
            ],
            [
                'name' => 'AC (Pendingin Ruangan)',
                'type' => 'select',
                'category' => 'pribadi',
                'options' => ['Ada AC', 'Tidak Ada AC']
            ],

            
            [
                'name' => 'Fasilitas Wi-Fi',
                'type' => 'select',
                'category' => 'bersama',
                'options' => ['Ada Wi-Fi', 'Tidak Ada Wi-Fi']
            ],
            [
                'name' => 'Tempat Jemuran',
                'type' => 'select',
                'category' => 'bersama',
                'options' => ['Ada Tempat Jemuran', 'Tidak Ada Tempat Jemuran']
            ],
            [
                'name' => 'Dapur Bersama',
                'type' => 'select',
                'category' => 'bersama',
                'options' => ['Ada Dapur Bersama', 'Tidak Ada Dapur Bersama']
            ],
            [
                'name' => 'Kulkas Bersama',
                'type' => 'select',
                'category' => 'bersama',
                'options' => ['Ada Kulkas Bersama', 'Tidak Ada Kulkas Bersama']
            ],
        ];

        
        $savedKriterias = [];
        foreach ($kriterias as $kData) {
            $k = Kriteria::create([
                'name' => $kData['name'],
                'type' => $kData['type'],
                'category' => $kData['category'],
            ]);

            $savedKriterias[$kData['name']] = [
                'model' => $k,
                'options' => []
            ];

            foreach ($kData['options'] as $oVal) {
                $o = OpsiKriteria::create([
                    'kriteria_id' => $k->id,
                    'value' => $oVal
                ]);
                $savedKriterias[$kData['name']]['options'][$oVal] = $o->id;
            }
        }

        
        $kostsData = [
            [
                'name' => 'Kost Putra Mandiri',
                'address' => 'Jl. Pasir Gede No. 5, Cianjur (Dekat Kampus UNSUR)',
                'latitude' => -6.81120000,
                'longitude' => 107.14350000,
                'description' => 'Kost khusus putra bersih, aman, nyaman, dan sangat dekat dengan gedung kuliah utama Universitas Suryakancana (UNSUR). Harga bersahabat.',
                'image' => 'https://images.unsplash.com/photo-1555854817-5b2260d50c47?w=800',
                'shared_attributes' => [
                    'Jenis Kost' => 'Putra',
                    'Sistem Listrik' => 'Listrik Token (Prepaid)',
                    'Sumber Air' => 'Air Sumur',
                    'Akses Jalan' => 'Hanya Masuk Motor',
                    'Tempat Parkir' => 'Parkir Motor Saja',
                    'Fasilitas Wi-Fi' => 'Ada Wi-Fi',
                    'Tempat Jemuran' => 'Ada Tempat Jemuran',
                    'Dapur Bersama' => 'Ada Dapur Bersama',
                    'Kulkas Bersama' => 'Tidak Ada Kulkas Bersama',
                ],
                'rooms' => [
                    [
                        'name' => 'Kamar Standar A1',
                        'price' => 600000,
                        'description' => 'Kamar standar berukuran 3x3m, ventilasi cukup, nyaman untuk belajar.',
                        'status' => 'tersedia',
                        'attributes' => [
                            'Kamar Mandi Dalam' => 'Kamar Mandi Luar (Tidak Ada)',
                            'Jenis Kloset' => 'Kloset Jongkok',
                            'Kasur' => 'Ada Kasur',
                            'Lemari Pakaian' => 'Ada Lemari Pakaian',
                            'Meja & Kursi Belajar' => 'Tidak Ada Meja & Kursi Belajar',
                            'AC (Pendingin Ruangan)' => 'Tidak Ada AC',
                        ]
                    ],
                    [
                        'name' => 'Kamar Ekonomi A2',
                        'price' => 500000,
                        'description' => 'Kamar hemat tanpa kasur, silakan bawa perlengkapan tidur sendiri.',
                        'status' => 'tersedia',
                        'attributes' => [
                            'Kamar Mandi Dalam' => 'Kamar Mandi Luar (Tidak Ada)',
                            'Jenis Kloset' => 'Kloset Jongkok',
                            'Kasur' => 'Tidak Ada Kasur',
                            'Lemari Pakaian' => 'Ada Lemari Pakaian',
                            'Meja & Kursi Belajar' => 'Tidak Ada Meja & Kursi Belajar',
                            'AC (Pendingin Ruangan)' => 'Tidak Ada AC',
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Kost Putri Sakinah',
                'address' => 'Jl. KH. Abdullah Bin Nuh No. 10, Cianjur',
                'latitude' => -6.81450000,
                'longitude' => 107.13800000,
                'description' => 'Kost putri eksklusif dengan fasilitas lengkap di dekat gerbang luar UNSUR. Lingkungan tenang, asri, dijaga CCTV 24 jam, sangat cocok untuk mahasiswi.',
                'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=800',
                'shared_attributes' => [
                    'Jenis Kost' => 'Putri',
                    'Sistem Listrik' => 'Listrik Bulanan (Include)',
                    'Sumber Air' => 'Air PDAM',
                    'Akses Jalan' => 'Bisa Masuk Mobil',
                    'Tempat Parkir' => 'Parkir Mobil & Motor',
                    'Fasilitas Wi-Fi' => 'Ada Wi-Fi',
                    'Tempat Jemuran' => 'Ada Tempat Jemuran',
                    'Dapur Bersama' => 'Ada Dapur Bersama',
                    'Kulkas Bersama' => 'Ada Kulkas Bersama',
                ],
                'rooms' => [
                    [
                        'name' => 'Kamar Deluxe B1',
                        'price' => 1200000,
                        'description' => 'Kamar eksklusif ber-AC dengan kamar mandi dalam kloset duduk. Sangat nyaman.',
                        'status' => 'tersedia',
                        'attributes' => [
                            'Kamar Mandi Dalam' => 'Ada Kamar Mandi Dalam',
                            'Jenis Kloset' => 'Kloset Duduk',
                            'Kasur' => 'Ada Kasur',
                            'Lemari Pakaian' => 'Ada Lemari Pakaian',
                            'Meja & Kursi Belajar' => 'Ada Meja & Kursi Belajar',
                            'AC (Pendingin Ruangan)' => 'Ada AC',
                        ]
                    ],
                    [
                        'name' => 'Kamar Superior B2',
                        'price' => 950000,
                        'description' => 'Kamar mandi dalam kloset jongkok, tidak ber-AC namun udara sejuk.',
                        'status' => 'tersedia',
                        'attributes' => [
                            'Kamar Mandi Dalam' => 'Ada Kamar Mandi Dalam',
                            'Jenis Kloset' => 'Kloset Jongkok',
                            'Kasur' => 'Ada Kasur',
                            'Lemari Pakaian' => 'Ada Lemari Pakaian',
                            'Meja & Kursi Belajar' => 'Ada Meja & Kursi Belajar',
                            'AC (Pendingin Ruangan)' => 'Tidak Ada AC',
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Kost Campur Lestari',
                'address' => 'Jl. Sindanglaya No. 23, Cianjur',
                'latitude' => -6.80900000,
                'longitude' => 107.13500000,
                'description' => 'Kost campur murah meriah sekitar UNSUR. Suasana asri dan kekeluargaan. Cocok untuk mahasiswa/mahasiswi dengan anggaran hemat yang menginginkan hunian sederhana.',
                'image' => 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?w=800',
                'shared_attributes' => [
                    'Jenis Kost' => 'Campur',
                    'Sistem Listrik' => 'Listrik Token (Prepaid)',
                    'Sumber Air' => 'Air Sumur',
                    'Akses Jalan' => 'Hanya Masuk Motor',
                    'Tempat Parkir' => 'Parkir Motor Saja',
                    'Fasilitas Wi-Fi' => 'Tidak Ada Wi-Fi',
                    'Tempat Jemuran' => 'Ada Tempat Jemuran',
                    'Dapur Bersama' => 'Ada Dapur Bersama',
                    'Kulkas Bersama' => 'Tidak Ada Kulkas Bersama',
                ],
                'rooms' => [
                    [
                        'name' => 'Kamar Sederhana C1',
                        'price' => 450000,
                        'description' => 'Kamar kosan sederhana berukuran 2.5x3m, cocok untuk mahasiswa hemat.',
                        'status' => 'tersedia',
                        'attributes' => [
                            'Kamar Mandi Dalam' => 'Kamar Mandi Luar (Tidak Ada)',
                            'Jenis Kloset' => 'Kloset Jongkok',
                            'Kasur' => 'Tidak Ada Kasur',
                            'Lemari Pakaian' => 'Tidak Ada Lemari Pakaian',
                            'Meja & Kursi Belajar' => 'Tidak Ada Meja & Kursi Belajar',
                            'AC (Pendingin Ruangan)' => 'Tidak Ada AC',
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Kost Putri Melati AC',
                'address' => 'Jl. Pasir Gede Gg. Melati No. 8, Cianjur',
                'latitude' => -6.81300000,
                'longitude' => 107.14400000,
                'description' => 'Kost eksklusif putri dekat Fakultas Teknik UNSUR. Dilengkapi pendingin ruangan (AC) dan furniture modern lengkap. Kamar mandi dalam dengan air hangat, wifi cepat.',
                'image' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=800',
                'shared_attributes' => [
                    'Jenis Kost' => 'Putri',
                    'Sistem Listrik' => 'Listrik Token (Prepaid)',
                    'Sumber Air' => 'Air PDAM',
                    'Akses Jalan' => 'Bisa Masuk Mobil',
                    'Tempat Parkir' => 'Parkir Motor Saja',
                    'Fasilitas Wi-Fi' => 'Ada Wi-Fi',
                    'Tempat Jemuran' => 'Ada Tempat Jemuran',
                    'Dapur Bersama' => 'Ada Dapur Bersama',
                    'Kulkas Bersama' => 'Ada Kulkas Bersama',
                ],
                'rooms' => [
                    [
                        'name' => 'Kamar VIP D1',
                        'price' => 1600000,
                        'description' => 'Kamar eksklusif putri dengan AC, kloset duduk, perabotan modern.',
                        'status' => 'tersedia',
                        'attributes' => [
                            'Kamar Mandi Dalam' => 'Ada Kamar Mandi Dalam',
                            'Jenis Kloset' => 'Kloset Duduk',
                            'Kasur' => 'Ada Kasur',
                            'Lemari Pakaian' => 'Ada Lemari Pakaian',
                            'Meja & Kursi Belajar' => 'Ada Meja & Kursi Belajar',
                            'AC (Pendingin Ruangan)' => 'Ada AC',
                        ]
                    ],
                    [
                        'name' => 'Kamar Standar D2',
                        'price' => 1300000,
                        'description' => 'Kamar mandi dalam tanpa AC, sirkulasi udara baik dengan exhaust fan.',
                        'status' => 'tersedia',
                        'attributes' => [
                            'Kamar Mandi Dalam' => 'Ada Kamar Mandi Dalam',
                            'Jenis Kloset' => 'Kloset Duduk',
                            'Kasur' => 'Ada Kasur',
                            'Lemari Pakaian' => 'Ada Lemari Pakaian',
                            'Meja & Kursi Belajar' => 'Ada Meja & Kursi Belajar',
                            'AC (Pendingin Ruangan)' => 'Tidak Ada AC',
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Kost Putra Executive',
                'address' => 'Jl. Bojong No. 15, Cianjur',
                'latitude' => -6.80750000,
                'longitude' => 107.14150000,
                'description' => 'Hunian nyaman berkelas eksekutif khusus putra di lokasi strategis dekat Fakultas Hukum UNSUR. Sangat dekat dengan pusat kuliner, minimarket, dan kampus.',
                'image' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=800',
                'shared_attributes' => [
                    'Jenis Kost' => 'Putra',
                    'Sistem Listrik' => 'Listrik Token (Prepaid)',
                    'Sumber Air' => 'Air PDAM',
                    'Akses Jalan' => 'Bisa Masuk Mobil',
                    'Tempat Parkir' => 'Parkir Mobil & Motor',
                    'Fasilitas Wi-Fi' => 'Ada Wi-Fi',
                    'Tempat Jemuran' => 'Ada Tempat Jemuran',
                    'Dapur Bersama' => 'Ada Dapur Bersama',
                    'Kulkas Bersama' => 'Ada Kulkas Bersama',
                ],
                'rooms' => [
                    [
                        'name' => 'Kamar Executive E1',
                        'price' => 1100000,
                        'description' => 'Kamar mandi dalam kloset duduk dengan perabotan kayu jati berkualitas.',
                        'status' => 'tersedia',
                        'attributes' => [
                            'Kamar Mandi Dalam' => 'Ada Kamar Mandi Dalam',
                            'Jenis Kloset' => 'Kloset Duduk',
                            'Kasur' => 'Ada Kasur',
                            'Lemari Pakaian' => 'Ada Lemari Pakaian',
                            'Meja & Kursi Belajar' => 'Ada Meja & Kursi Belajar',
                            'AC (Pendingin Ruangan)' => 'Tidak Ada AC',
                        ]
                    ],
                    [
                        'name' => 'Kamar Deluxe E2',
                        'price' => 900000,
                        'description' => 'Kamar mandi luar, kloset jongkok, bersih dan rapi.',
                        'status' => 'tersedia',
                        'attributes' => [
                            'Kamar Mandi Dalam' => 'Kamar Mandi Luar (Tidak Ada)',
                            'Jenis Kloset' => 'Kloset Jongkok',
                            'Kasur' => 'Ada Kasur',
                            'Lemari Pakaian' => 'Ada Lemari Pakaian',
                            'Meja & Kursi Belajar' => 'Ada Meja & Kursi Belajar',
                            'AC (Pendingin Ruangan)' => 'Tidak Ada AC',
                        ]
                    ]
                ]
            ],
        ];

        foreach ($kostsData as $kData) {
            
            $prices = array_column($kData['rooms'], 'price');
            $avgPrice = count($prices) > 0 ? (array_sum($prices) / count($prices)) : 0;

            $kost = Kost::create([
                'user_id' => $pengelolaUser->id,
                'kampus_id' => $kampus->id,
                'name' => $kData['name'],
                'price' => $avgPrice,
                'address' => $kData['address'],
                'latitude' => $kData['latitude'],
                'longitude' => $kData['longitude'],
                'description' => $kData['description']
            ]);

            
            FotoKost::create([
                'kost_id' => $kost->id,
                'image_path' => $kData['image'],
                'is_primary' => true
            ]);

            
            foreach ($kData['shared_attributes'] as $kName => $oVal) {
                if (isset($savedKriterias[$kName])) {
                    $kId = $savedKriterias[$kName]['model']->id;
                    if (isset($savedKriterias[$kName]['options'][$oVal])) {
                        $oId = $savedKriterias[$kName]['options'][$oVal];

                        AtributKost::create([
                            'kost_id' => $kost->id,
                            'kriteria_id' => $kId,
                            'opsi_kriteria_id' => $oId
                        ]);
                    }
                }
            }

            
            foreach ($kData['rooms'] as $rData) {
                $kamar = Kamar::create([
                    'kost_id' => $kost->id,
                    'name' => $rData['name'],
                    'price' => $rData['price'],
                    'status' => $rData['status'],
                    'description' => $rData['description'],
                    'image_path' => null
                ]);

                
                foreach ($rData['attributes'] as $kName => $oVal) {
                    if (isset($savedKriterias[$kName])) {
                        $kId = $savedKriterias[$kName]['model']->id;
                        if (isset($savedKriterias[$kName]['options'][$oVal])) {
                            $oId = $savedKriterias[$kName]['options'][$oVal];

                            AtributKamar::create([
                                'kamar_id' => $kamar->id,
                                'kriteria_id' => $kId,
                                'opsi_kriteria_id' => $oId
                            ]);
                        }
                    }
                }
            }
        }

        
        $anyKamar = Kamar::first();
        if ($anyKamar) {
            KamarFavorit::create([
                'user_id' => $mahasiswaUser->id,
                'kamar_id' => $anyKamar->id
            ]);

            
            LogKontak::create([
                'user_id' => $mahasiswaUser->id,
                'kamar_id' => $anyKamar->id,
                'contact_type' => 'whatsapp'
            ]);
        }
    }
}
