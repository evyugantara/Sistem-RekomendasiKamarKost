@extends('layouts.dashboard')

@section('title', 'Dashboard Monitoring')
@section('header-title', 'Dashboard Monitoring Sistem')
@section('breadcrumb-active', 'Dashboard')

@section('content')

<div class="metrics-row">
    
    <div class="metric-card blue">
        <div class="inner">
            <h3>{{ $totalMahasiswa }}</h3>
            <p>JML PENGHUNI AKTIF</p>
        </div>
        <div class="icon">
            <i class="fa fa-users"></i>
        </div>
    </div>
    
    
    <div class="metric-card green">
        <div class="inner">
            <h3>{{ $totalPengelola }}</h3>
            <p>JML PENGELOLA AKTIF</p>
        </div>
        <div class="icon">
            <i class="fa fa-user-tie"></i>
        </div>
    </div>
    
    
    <div class="metric-card yellow">
        <div class="inner">
            <h3>{{ $totalKost }}</h3>
            <p>JML KOST TERDAFTAR</p>
        </div>
        <div class="icon">
            <i class="fa fa-house-chimney-window"></i>
        </div>
    </div>

    
    <div class="metric-card red">
        <div class="inner">
            <h3>{{ $totalSearches }}</h3>
            <p>JML PENCARIAN REKOMENDASI</p>
        </div>
        <div class="icon">
            <i class="fa fa-magnifying-glass-chart"></i>
        </div>
    </div>
</div>


<div class="metrics-row" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 25px;">
    
    <div style="background-color: #fff; border-radius: 3px; border: 1px solid #ddd; padding: 15px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 1px rgba(0,0,0,0.05);">
        <div>
            <span style="font-size: 11px; font-weight: bold; color: #777; display: block; text-transform: uppercase;">Penghuni Status Aktif</span>
            <span style="font-size: 22px; font-weight: bold; color: #333;">{{ $activeMahasiswa }}</span>
        </div>
        <i class="fa-solid fa-user-check" style="font-size: 28px; color: #00c0ef; opacity: 0.7;"></i>
    </div>
    
    
    <div style="background-color: #fff; border-radius: 3px; border: 1px solid #ddd; padding: 15px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 1px rgba(0,0,0,0.05);">
        <div>
            <span style="font-size: 11px; font-weight: bold; color: #777; display: block; text-transform: uppercase;">Hubungi Pengelola (Klik)</span>
            <span style="font-size: 22px; font-weight: bold; color: #333;">{{ $totalContacts }}</span>
        </div>
        <i class="fa-solid fa-comments" style="font-size: 28px; color: #00a65a; opacity: 0.7;"></i>
    </div>
</div>


<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><i class="fa-solid fa-chart-line"></i> Grafik Trend Pendaftaran Pengguna Baru</h3>
    </div>
    <div class="box-body" style="padding: 20px;">
        <h4 style="text-align: center; font-weight: 600; color: #444; margin-bottom: 20px; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
            Grafik Trend Jumlah Pendaftaran Pengguna Baru (6 Bulan Terakhir)
        </h4>
        
        
        <div style="position: relative; height: 320px; width: 100%;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('trendChart').getContext('2d');
        
        
        var months = @json($months);
        var studentData = @json($studentRegistrations);
        var ownerData = @json($ownerRegistrations);
        
        var trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Pendaftaran Penghuni',
                        data: studentData,
                        borderColor: '#00c0ef',
                        backgroundColor: 'rgba(0, 192, 239, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#00c0ef',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.3, 
                        fill: true
                    },
                    {
                        label: 'Pendaftaran Pengelola',
                        data: ownerData,
                        borderColor: '#00a65a',
                        backgroundColor: 'rgba(0, 166, 90, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#00a65a',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                family: "'Source Sans Pro', sans-serif",
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: "'Source Sans Pro', sans-serif"
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f4f4f4'
                        },
                        ticks: {
                            stepSize: 5,
                            font: {
                                family: "'Source Sans Pro', sans-serif"
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
