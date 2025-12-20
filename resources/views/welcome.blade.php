<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Mining IT Asset') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-blue: #4e73df; 
            --primary-dark: #224abe;
        }

        body {
            font-family: 'Nunito', sans-serif;
            height: 100vh;
            overflow-x: hidden;
            background-color: #1a1a2e; 
        }

        /* === BACKGROUND === */
        .bg-container {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
        }
        .bg-mining-image {
            position: absolute; width: 100%; height: 100%;
            background: url("{{ asset('image/background.jpg') }}") no-repeat center center/cover;
            opacity: 50%; 
        }
        .bg-overlay-blue {
            position: absolute; width: 100%; height: 100%;
            background: linear-gradient(180deg, rgba(78, 115, 223, 0.7) 10%, rgba(34, 74, 190, 0.9) 100%);
        }
        /* ================== */

        /* Navbar & Logo */
        .navbar {
            padding-top: 25px;
        }
        .navbar-brand {
            font-weight: 800;
            color: white !important;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 1.5rem;
            display: flex;
            align-items: center; /* Biar logo dan teks sejajar tengah */
        }
        
        /* Style untuk Logo Gambar */
        .navbar-brand img {
            height: 80px; /* Atur tinggi logo biar pas */
            width: 80px;  /* Atur lebar logo biar pas */
            margin-right: 15px; /* Jarak antara logo dan teks */
        }
        
        /* Hero Section */
        .hero-container {
            min-height: 90vh;
            display: flex;
            align-items: center;
            color: white;
            padding-top: 50px;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.1rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 40px;
            border-left: 5px solid white;
            padding-left: 20px;
        }

        /* Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 25px;
            transition: transform 0.3s ease;
            color: white;
            height: 100%;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.2);
            cursor: pointer;
        }

        .icon-box {
            font-size: 2rem;
            margin-bottom: 15px;
            color: white;
        }

        .card-title {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 1rem;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        /* Buttons */
        .btn-light-custom {
            background-color: white;
            color: var(--primary-blue);
            font-weight: 700;
            border-radius: 50px;
            padding: 12px 30px;
            border: none;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .btn-light-custom:hover {
            background-color: #f8f9fa;
            transform: scale(1.05);
            color: var(--primary-dark);
        }

        .btn-outline-custom {
            border: 2px solid rgba(255,255,255,0.7);
            color: white;
            font-weight: 600;
            border-radius: 50px;
            padding: 10px 25px;
        }
        
        .btn-outline-custom:hover {
            background: white;
            color: var(--primary-blue);
            border-color: white;
        }

        /* Footer */
        .footer-strip {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: rgba(0,0,0,0.3);
            color: rgba(255,255,255,0.7);
            padding: 15px 0;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

    <div class="bg-container">
        <div class="bg-mining-image"></div>
        <div class="bg-overlay-blue"></div>
    </div>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('image/images.png') }}" alt="Logo System">
                RESOURCE SYSTEM
            </a>
            <div class="ms-auto">
                @if (Route::has('login'))
                    <div class="">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline-custom btn-sm">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-light-custom btn-sm">
                                <i class="fas fa-sign-in-alt me-2"></i> Login 
                            </a>
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <div class="container hero-container">
        <div class="row align-items-center w-100">
            
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h1 class="hero-title">
                    IT ASSET &<br>
                    RESOURCE CONTROL
                </h1>
                <p class="hero-subtitle">
                    Sistem Manajemen Inventaris & Pemeliharaan Perangkat Tambang.<br>
                    Terintegrasi, Realtime, dan Akurat.
                </p>
                
                <div class="d-flex gap-3">
                    @auth
                        <a href="{{ url('/Pengguna/ppi/create') }}" class="btn btn-light-custom">
                            <i class="fas fa-plus-circle me-2"></i> Buat Tiket
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light-custom">
                            <i class="fas fa-rocket me-2"></i> Akses Portal
                        </a>
                        <a href="#features" class="btn btn-outline-custom">
                            <i class="fas fa-search me-2"></i> Cek Status
                        </a>
                    @endauth
                </div>
            </div>

            <div class="col-lg-6">
                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <div class="glass-card">
                            <div class="icon-box">
                                <i class="fas fa-laptop-medical"></i>
                            </div>
                            <div class="card-title">Inventory & Stock</div>
                            <p class="small text-white-50">Monitoring stok Laptop, PC, dan Consumable (Sparepart) IT.</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="glass-card">
                            <div class="icon-box">
                                <i class="fas fa-satellite-dish"></i>
                            </div>
                            <div class="card-title">Radio Comm</div>
                            <p class="small text-white-50">Manajemen aset komunikasi (HT/Rig) dan infrastruktur jaringan.</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="glass-card">
                            <div class="icon-box">
                                <i class="fas fa-tools"></i>
                            </div>
                            <div class="card-title">Maintenance</div>
                            <p class="small text-white-50">Jadwal service berkala dan riwayat perbaikan perangkat.</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="glass-card">
                            <div class="icon-box">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="card-title">Audit & BAST</div>
                            <p class="small text-white-50">Pencatatan serah terima aset (BAST) dan laporan audit.</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <div class="footer-strip text-center">
        <div class="container">
            <span>&copy; {{ date('Y') }} <strong>IT DEPARTMENT</strong> - Resource Asset Management System</span>
        </div>
    </div>

</body>
</html>