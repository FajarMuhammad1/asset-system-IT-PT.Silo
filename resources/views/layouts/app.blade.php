@include('layouts.header')

<body id="page-top">

<div id="wrapper">

    @include('layouts.sidebar')

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            {{-- TOPBAR --}}
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                {{-- Toggle Sidebar Mobile --}}
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                @php
                    // LOGIC PEMISAHAN ROLE
                    $userRoleLabel = auth()->user()->role;
                    $userRole = strtolower($userRoleLabel);

                    // 1. ADMIN (Teknis & Maintenance)
                    $isAdmin = ($userRole == 'admin');

                    // 2. SUPER ADMIN (Hanya Approval / Manajemen)
                    $isSuperAdmin = in_array($userRole, ['superadmin', 'super admin']);

                    // 3. STAFF & USER
                    $isStaff = ($userRole == 'staff');
                    $isUser  = ($userRole == 'pengguna');

                    // Gabungan hak akses untuk melihat Notifikasi System
                    $canSeeSystemNotif = $isAdmin || $isSuperAdmin;
                @endphp

                {{-- SEARCH BAR: HANYA UNTUK ADMIN & STAFF (Super Admin tidak perlu cari-cari data teknis) --}}
                @if(!$isSuperAdmin)
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Cari data..."
                                   aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                @endif

                {{-- NAV RIGHT --}}
                <ul class="navbar-nav ml-auto">

                    {{-- Search Mobile (Hanya Non-SuperAdmin) --}}
                    @if(!$isSuperAdmin)
                    <li class="nav-item dropdown no-arrow d-sm-none">
                        <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-search fa-fw"></i>
                        </a>
                    </li>
                    @endif

                    {{-- ======================================================= --}}
                    {{--               AREA NOTIFIKASI SYSTEM                    --}}
                    {{-- ======================================================= --}}

                    @if($canSeeSystemNotif)

                        {{-- Bell Notification --}}
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                {{-- Counter Notif --}}
                                @if(isset($notifCount) && $notifCount > 0)
                                    <span class="badge badge-danger badge-counter">
                                        {{ $notifCount > 5 ? '5+' : $notifCount }}
                                    </span>
                                @endif
                            </a>

                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                 aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header {{ $isSuperAdmin ? 'bg-dark' : 'bg-primary' }}">
                                    @if($isSuperAdmin)
                                        Notifikasi Approval
                                    @else
                                        Notifikasi Sistem
                                    @endif
                                </h6>

                                @if(isset($notifications))
                                    @forelse($notifications as $notif)
                                        <a class="dropdown-item d-flex align-items-center" href="{{ $notif['url'] ?? '#' }}">
                                            <div class="mr-3">
                                                <div class="icon-circle {{ $notif['color'] ?? 'bg-primary' }}">
                                                    <i class="fas {{ $notif['icon'] ?? 'fa-bell' }} text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="small text-gray-500">{{ isset($notif['time']) ? $notif['time']->diffForHumans() : '' }}</div>
                                                <span class="font-weight-bold">{{ $notif['title'] ?? '' }}</span>
                                                <div class="small text-gray-600">{{ Str::limit($notif['detail'] ?? '', 40) }}</div>
                                            </div>
                                        </a>
                                    @empty
                                        <a class="dropdown-item d-flex align-items-center" href="#">
                                            <div class="mr-3">
                                                <div class="icon-circle bg-secondary">
                                                    <i class="fas fa-check text-white"></i>
                                                </div>
                                            </div>
                                            <div><span class="font-weight-bold text-gray-500">Tidak ada notifikasi baru.</span></div>
                                        </a>
                                    @endforelse
                                @endif
                            </div>
                        </li>

                        {{-- Messages (HANYA UNTUK ADMIN TEKNIS) --}}
                        {{-- Super Admin tidak perlu lihat pesan chat/helpdesk di header --}}
                        @if($isAdmin)
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                            </a>
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                 aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">Pesan Masuk (Admin)</h6>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Tidak ada pesan baru</a>
                            </div>
                        </li>
                        @endif

                    @endif

                    {{-- ======================================================= --}}
                    {{--               NOTIFIKASI USER BIASA                     --}}
                    {{-- ======================================================= --}}
                    
                    @if(!$canSeeSystemNotif)
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="userNotifDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                @if(isset($notifCount) && $notifCount > 0)
                                    <span class="badge badge-danger badge-counter">{{ $notifCount }}</span>
                                @endif
                            </a>
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userNotifDropdown">
                                <h6 class="dropdown-header">Notifikasi Anda</h6>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Kosong</a>
                            </div>
                        </li>
                    @endif


                    <div class="topbar-divider d-none d-sm-block"></div>

                    {{-- USER PROFILE --}}
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <strong>{{ auth()->user()->name ?? auth()->user()->nama }}</strong>
                                <br>
                                {{-- Jabatan --}}
                                <small>{{ auth()->user()->jabatan ?? auth()->user()->departemen ?? 'User' }}</small>
                            </span>
                            <img class="img-profile rounded-circle"
                                 src="{{ asset('sbadmin2/img/undraw_profile.svg') }}">
                        </a>

                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="userDropdown">

                            {{-- BADGE ROLE --}}
                            <a class="dropdown-item" href="#">
                                <div class="d-flex justify-content-center">
                                    @if($isSuperAdmin)
                                        <span class="badge badge-dark px-3 py-2">
                                            <i class="fas fa-user-shield mr-1"></i> Approver (Head IT)
                                        </span>
                                    @elseif($isAdmin)
                                        <span class="badge badge-primary px-3 py-2">Administrator</span>
                                    @elseif($isStaff)
                                        <span class="badge badge-info px-3 py-2">Staff IT</span>
                                    @else
                                        <span class="badge badge-secondary px-3 py-2">Pengguna</span>
                                    @endif
                                </div>
                            </a>

                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Profil Saya
                            </a>

                            {{-- ACTIVITY LOG (HANYA ADMIN TEKNIS) --}}
                            {{-- Super Admin tidak perlu lihat log sistem --}}
                            @if($isAdmin)
                                <a class="dropdown-item" href="{{ route('activity.log') }}">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                            @endif

                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Keluar (Logout)
                            </a>

                        </div>
                    </li>

                </ul>

            </nav>

            <div class="container-fluid">
                @yield('content')
            </div>

        </div>
    </div>

</div>

@include('layouts.footer')