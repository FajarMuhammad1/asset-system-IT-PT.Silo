<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-icon rotate-n-0">
            <i class="fas fa-cogs"></i>
        </div>
        <div class="sidebar-brand-text mx-3"> Asset System </div>
    </a>

    <hr class="sidebar-divider my-0">

    {{-- =============================================== --}}
    {{-- SMART DASHBOARD LOGIC (SATU UNTUK SEMUA)        --}}
    {{-- =============================================== --}}
    @php
        // 1. Ambil role dan kecilin hurufnya biar aman
        $role = strtolower(auth()->user()->role);
        
        // 2. Set default variable
        $dashboardRoute = '#';
        $activeClass = '';

        // 3. Cek Role untuk nentuin arah Link & Status Active
        // Cek Admin (terima 'super admin', 'superadmin', 'admin')
        if (in_array($role, ['super admin', 'superadmin', 'admin'])) {
            $dashboardRoute = route('admin.dashboard');
            $activeClass = $menuDashboard ?? ''; // Variable dari Controller Admin
        } 
        // Cek Staff
        elseif ($role == 'staff') {
            $dashboardRoute = route('staff.dashboard');
            $activeClass = $menuDashboardStaff ?? ''; // Variable dari Controller Staff
        } 
        // Cek Pengguna
        elseif ($role == 'pengguna') {
            $dashboardRoute = route('pengguna.dashboard');
            $activeClass = $menuDashboardPengguna ?? ''; // Variable dari Controller Pengguna
        }
    @endphp

    <li class="nav-item {{ $activeClass }}">
        <a class="nav-link" href="{{ $dashboardRoute }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    
    {{-- =============================================== --}}
    {{-- KAVLING SUPER ADMIN & ADMIN                     --}}
    {{-- =============================================== --}}
    @if (in_array($role, ['super admin', 'superadmin', 'admin']))
        
        <hr class="sidebar-divider">
        
        <div class="sidebar-heading">
            Administration
        </div>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.ppi.index') }}">
                <i class="fas fa-fw fa-clipboard"></i>
                <span>PPI Monitoring</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="{{route('surat-jalan.index')}}">
                <i class="fas fa-envelope "></i>
                <span>Surat Jalan</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBarang" aria-expanded="true" aria-controls="collapseBarang">
                <i class="fas fa-boxes"></i>
                <span>Data Barang</span>
            </a>
            <div id="collapseBarang" class="collapse" aria-labelledby="headingBarang" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Data Barang:</h6> 
                    <a class="collapse-item" href="{{ route('master-barang.index') }}">Master Barang</a>
                    <a class="collapse-item" href="{{ route('barangmasuk.create') }}">Input Barang Masuk</a>
                    <a class="collapse-item" href="{{ route('barangmasuk.index') }}">Daftar Aset</a>
                    <a class="collapse-item" href="{{ route('barangkeluar.create') }}">Serah Terima (Keluar)</a>
                    <a class="collapse-item" href="{{ route('barangkeluar.index') }}">Riwayat Serah Terima</a>
                </div>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('pengguna.index') }}">
                <i class="fas fa-fw fa-user"></i>
                <span>Pengguna/User</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('team') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>Team IT</span>
            </a>
        </li>
    
        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            Task/Heldesk
        </div>

        <li class="nav-item">
            <a class="nav-link" href="#"> {{-- GANTI # NANTI --}}
                <i class="fas fa-fw fa-desktop"></i>
                <span>Helpdesk Monitoring</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="#"> {{-- GANTI # NANTI --}}
                <i class="fas fa-fw fa-tasks"></i>
                <span>Task Info</span>
            </a>
        </li>
    
    {{-- =============================================== --}}
    {{-- KAVLING STAFF                                   --}}
    {{-- =============================================== --}}
    @elseif ($role == 'staff')

        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            Tugas Saya
        </div>

        <li class="nav-item">
            <a class="nav-link" href="#"> {{-- GANTI # NANTI --}}
                <i class="fas fa-fw fa-tasks"></i>
                <span>Task Info</span>
            </a>
        </li>

    {{-- =============================================== --}}
    {{-- KAVLING PENGGUNA                                --}}
    {{-- =============================================== --}}
    @elseif ($role == 'pengguna')

        <hr class="sidebar-divider">

        {{-- NOTE: DASHBOARD DISINI UDAH DIHAPUS KARENA UDAH DIHANDLE DI ATAS --}}
        
        <div class="sidebar-heading">
            Formulir
        </div>

        {{-- Gw tambahin $menuPPI buat persiapan kalo nanti lo bikin controller PPI --}}
        <li class="nav-item {{ $menuPPI ?? '' }}">
            <a class="nav-link" href="{{ route('pengguna.ppi.create') }}"> {{-- Nanti ganti route('pengguna.permintaan.create') --}}
                <i class="fas fa-fw fa-clipboard"></i>
                <span>PPI Request</span>
            </a>
        </li>
    
    @endif
    {{-- =============================================== --}}
    {{-- AKHIR DARI SEMUA KAVLING ROLE                   --}}
    {{-- =============================================== --}}


    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>