<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Mining IT Asset') }}</title>

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-blue: #4e73df; 
            --primary-dark: #224abe;
            --bg-dark: #1a1a2e;
            --accent-cyan: #36b9cc;
        }

        body {
            font-family: 'Nunito', sans-serif;
            overflow-x: hidden;
            background-color: var(--bg-dark); 
            color: #fff;
        }

        /* === BACKGROUND === */
        .bg-container {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
        }
        .bg-mining-image {
            position: absolute; width: 100%; height: 100%;
            /* Pastikan gambar ada di folder public/image/ */
            background: url("{{ asset('image/background.jpg') }}") no-repeat center center/cover;
            opacity: 0.4; 
        }
        .bg-overlay-blue {
            position: absolute; width: 100%; height: 100%;
            background: linear-gradient(180deg, rgba(26, 26, 46, 0.85) 0%, rgba(34, 74, 190, 0.85) 100%);
        }

        /* === NAVBAR === */
        .navbar {
            padding: 15px 0;
            background: rgba(26, 26, 46, 0.95);
            backdrop-filter: blur(10px);
            transition: all 0.3s;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .navbar-brand {
            font-weight: 800;
            color: white !important;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 1.1rem;
            display: flex;
            align-items: center; 
        }
        
        .navbar-brand img {
            height: 35px;
            width: auto;
            margin-right: 10px;
        }

        /* === BURGER MENU CUSTOM (FIXED) === */
        .navbar-toggler {
            border: none; /* Hilangkan kotak border */
            padding: 0;
            outline: none;
            box-shadow: none !important; /* Hilangkan glow biru */
        }
        
        .navbar-toggler-icon {
            width: 1.5em;
            height: 1.5em;
            /* Icon garis tiga warna putih tebal */
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='3' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* === HERO SECTION === */
        .hero-section {
            padding-top: 130px;
            padding-bottom: 80px;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .hero-title {
            font-size: 2.5rem; /* Ukuran Mobile */
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            text-shadow: 0 2px 15px rgba(0,0,0,0.5);
        }

        @media (min-width: 992px) {
            .hero-title {
                font-size: 3.8rem; /* Ukuran Desktop */
            }
        }

        .hero-subtitle {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 30px;
            border-left: 4px solid var(--accent-cyan);
            padding-left: 15px;
        }

        /* === GLASS CARDS === */
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 20px;
            transition: transform 0.3s ease;
            height: 100%;
        }
        .glass-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--accent-cyan);
        }
        .icon-box {
            font-size: 1.8rem;
            margin-bottom: 12px;
            color: var(--accent-cyan);
        }
        .card-title-custom {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        /* === BUTTONS === */
        .btn-custom-primary {
            background-color: var(--primary-blue);
            color: white;
            font-weight: 700;
            border-radius: 50px;
            padding: 10px 30px;
            border: none;
            transition: all 0.3s;
        }
        .btn-custom-primary:hover {
            background-color: var(--primary-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
        }

        .btn-custom-outline {
            background: transparent;
            border: 2px solid rgba(255,255,255,0.6);
            color: white;
            font-weight: 600;
            border-radius: 50px;
            padding: 8px 25px;
            transition: all 0.3s;
        }
        .btn-custom-outline:hover {
            background: white;
            color: var(--bg-dark);
            border-color: white;
        }

        /* === CONTENT SECTION === */
        .content-section {
            background-color: var(--bg-dark);
            padding: 80px 0;
            position: relative;
            z-index: 10;
            box-shadow: 0 -20px 50px rgba(0,0,0,0.5);
        }

        .section-heading {
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }
        .section-heading::after {
            content: '';
            display: block;
            width: 50%;
            height: 4px;
            background: var(--primary-blue);
            margin-top: 5px;
            border-radius: 2px;
        }

        /* Dept Cards */
        .dept-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            height: 100%;
            transition: 0.3s;
        }
        .dept-card:hover {
            background: rgba(78, 115, 223, 0.15);
            border-color: var(--primary-blue);
        }
        .dept-icon {
            font-size: 1.8rem;
            color: var(--primary-blue);
            margin-bottom: 10px;
        }
        .dept-name {
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        /* Footer */
        footer {
            background: #0f0f1a;
            padding: 30px 0;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
            position: relative;
            z-index: 20;
        }
    </style>
</head>
<body>

    {{-- Background Parallax --}}
    <div class="bg-container">
        <div class="bg-mining-image"></div>
        <div class="bg-overlay-blue"></div>
    </div>

    {{-- Navbar Responsive Fixed --}}
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        {{-- Gunakan d-flex justify-content-between agar logo kiri & burger kanan mentok --}}
        <div class="container d-flex justify-content-between align-items-center">
            
            {{-- LOGO (KIRI) --}}
            <a class="navbar-brand" href="#">
                <img src="{{ asset('image/images.png') }}" alt="Logo">
                <span>Resource Asset System</span>
            </a>
            
            {{-- TOMBOL BURGER (KANAN) --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- MENU ITEM --}}
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center text-center pt-4 pt-lg-0">
                    <li class="nav-item mb-3 mb-lg-0">
                        <a class="nav-link text-white me-lg-4" href="#about-section">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-custom-primary w-100 w-lg-auto px-4">
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-custom-outline w-100 w-lg-auto px-4">
                                    <i class="fas fa-sign-in-alt me-2"></i> Login Staff
                                </a>
                            @endauth
                        @endif
                    </li>
                </ul>
            </div>
            
        </div>
    </nav>

    {{-- Hero Section --}}
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                
                {{-- Text Content --}}
                <div class="col-lg-6 mb-5 mb-lg-0 text-center text-lg-start">
                    <h1 class="hero-title">
                        Resource Asset &<br>Monitoring Helpdesk
                    </h1>
                    <p class="hero-subtitle mx-auto mx-lg-0" style="max-width: 500px;">
                        Sistem terintegrasi untuk manajemen aset tambang, pemeliharaan perangkat, dan pelaporan kendala (Helpdesk) di lingkungan <strong>PT. SILO</strong>.
                    </p>
                    
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start mt-4">
                        @auth
                            <a href="{{ url('/Pengguna/ppi/create') }}" class="btn btn-custom-primary btn-lg">
                                <i class="fas fa-plus-circle me-2"></i> Buat Tiket
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-custom-primary btn-lg">
                                Akses Portal <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        @endauth
                        <a href="#departments" class="btn btn-custom-outline btn-lg">
                            Departemen
                        </a>
                    </div>
                </div>

                {{-- Hero Cards Grid --}}
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <div class="glass-card">
                                <div class="icon-box"><i class="fas fa-laptop-code"></i></div>
                                <div class="card-title-custom">Inventory IT</div>
                                <p class="small text-white-50 m-0">Tracking Laptop, PC, & Stok Sparepart.</p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="glass-card">
                                <div class="icon-box"><i class="fas fa-broadcast-tower"></i></div>
                                <div class="card-title-custom">Radio Comm</div>
                                <p class="small text-white-50 m-0">Manajemen HT, Rig, & Repeater.</p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="glass-card">
                                <div class="icon-box"><i class="fas fa-tools"></i></div>
                                <div class="card-title-custom">Maintenance</div>
                                <p class="small text-white-50 m-0">Jadwal service berkala & histori.</p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="glass-card">
                                <div class="icon-box"><i class="fas fa-file-signature"></i></div>
                                <div class="card-title-custom">E-Report</div>
                                <p class="small text-white-50 m-0">Digital BAST & Laporan Audit.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Content Section (About & Dept) --}}
    <section class="content-section" id="about-section">
        <div class="container">
            
            {{-- About --}}
            <div class="row align-items-center mb-5">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <img src="{{ asset('image/photo.jpg') }}" 
                         alt="Mining Activity" 
                         class="img-fluid rounded-4 shadow-lg border border-secondary w-100">
                </div>
                <div class="col-lg-7 ps-lg-5">
                    <h2 class="section-heading">TENTANG PT. SILO</h2>
                    <p class="text-white-50" style="line-height: 1.8; text-align: justify;">
                        <strong>PT. Sebuku Iron Lateritic Ores (SILO)</strong> berkomitmen pada efisiensi operasional tambang melalui digitalisasi. Sistem ini dirancang untuk memastikan ketersediaan alat kerja, respon cepat terhadap kendala teknis, dan transparansi data aset antar departemen.
                    </p>
                </div>
            </div>

            <hr class="border-secondary opacity-25 my-5">

            {{-- Departments Grid --}}
            <div id="departments">
                <div class="text-center mb-5">
                    <h2 class="section-heading fs-3">DEPARTEMEN TERKAIT</h2>
                    <p class="text-white-50">Sinergi operasional untuk produktivitas maksimal.</p>
                </div>

                {{-- Mobile: 2 Kolom (col-6), Desktop: 6 Kolom (col-lg-2) --}}
                <div class="row g-3">
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="dept-card">
                            <div class="dept-icon"><i class="fas fa-network-wired"></i></div>
                            <div class="dept-name">IT & Network</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="dept-card">
                            <div class="dept-icon"><i class="fas fa-drafting-compass"></i></div>
                            <div class="dept-name">Engineering</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="dept-card">
                            <div class="dept-icon"><i class="fas fa-truck-moving"></i></div>
                            <div class="dept-name">Production</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="dept-card">
                            <div class="dept-icon"><i class="fas fa-cogs"></i></div>
                            <div class="dept-name">Maintenance</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="dept-card">
                            <div class="dept-icon"><i class="fas fa-hard-hat"></i></div>
                            <div class="dept-name">HSE / Safety</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="dept-card">
                            <div class="dept-icon"><i class="fas fa-users"></i></div>
                            <div class="dept-name">HRGA</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <footer>
        <div class="container text-center">
            <p class="mb-0">
                &copy; {{ date('Y') }} <strong>PT. SILO IT DEPT</strong>. Resource Management System.<br>
                <span class="small opacity-50">Developed by Muhammad Fajar Hermawan</span>
            </p>
        </div>
    </footer>

    {{-- Bootstrap Bundle with Popper --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Navbar change background on scroll
        window.addEventListener('scroll', function() {
            var navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow');
                navbar.style.background = 'rgba(26, 26, 46, 1)';
            } else {
                navbar.classList.remove('shadow');
                navbar.style.background = 'rgba(26, 26, 46, 0.95)';
            }
        });
    </script>
</body>
</html>