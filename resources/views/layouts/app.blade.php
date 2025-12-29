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

              

                {{-- NAV RIGHT --}}
                <ul class="navbar-nav ml-auto">

                    {{-- Search Mobile --}}
                    <li class="nav-item dropdown no-arrow d-sm-none">
                        <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-search fa-fw"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                             aria-labelledby="searchDropdown">
                            <form class="form-inline mr-auto w-100 navbar-search">
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                           aria-label="Search" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button">
                                            <i class="fas fa-search fa-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </li>

                    {{-- ======================================================= --}}
                    {{--               AREA NOTIFIKASI & PESAN ADMIN             --}}
                    {{-- ======================================================= --}}

                    @php
                        // Definisikan role user
                        $userRole = strtolower(auth()->user()->role);

                        // Role yang diizinkan melihat Notif Admin
                        $allowedRoles = ['admin', 'super admin', 'superadmin'];
                    @endphp

                    {{-- ===================== NOTIFIKASI ADMIN ===================== --}}
                    @if(in_array($userRole, $allowedRoles))

                        {{-- Bell Notification Admin --}}
                        <li class="nav-item dropdown no-arrow mx-1">

                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                <i class="fas fa-bell fa-fw"></i>

                                @if(isset($notifCount) && $notifCount > 0)
                                    <span class="badge badge-danger badge-counter">
                                        {{ $notifCount > 5 ? '5+' : $notifCount }}
                                    </span>
                                @endif
                            </a>

                            {{-- Dropdown Notifikasi --}}
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                 aria-labelledby="alertsDropdown">

                                <h6 class="dropdown-header">Pusat Notifikasi</h6>

                                @forelse($notifications as $notif)

                                    @php
                                        $iconBg = match($notif['type']) {
                                            'PPI'       => 'bg-primary',
                                            'HELPDESK'  => 'bg-warning',
                                            default     => 'bg-secondary'
                                        };

                                        $icon = match($notif['type']) {
                                            'PPI'       => 'fa-file-alt',
                                            'HELPDESK'  => 'fa-headset',
                                            default     => 'fa-bell'
                                        };
                                    @endphp

                                    <a class="dropdown-item d-flex align-items-center" href="{{ $notif['url'] }}">
                                        <div class="mr-3">
                                            <div class="icon-circle {{ $iconBg }}">
                                                <i class="fas {{ $icon }} text-white"></i>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="small text-gray-500">
                                                {{ $notif['time']->diffForHumans() }}
                                            </div>
                                            <span class="font-weight-bold">
                                                {{ $notif['title'] }}
                                            </span>
                                            <br>
                                            <span class="small">
                                                {{ \Illuminate\Support\Str::limit($notif['detail'], 40) }}
                                            </span>
                                        </div>
                                    </a>

                                @empty

                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <div class="mr-3">
                                            <div class="icon-circle bg-success">
                                                <i class="fas fa-check text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="font-weight-bold">Tidak ada notifikasi baru.</span>
                                        </div>
                                    </a>

                                @endforelse
                            </div>
                        </li>

                        {{-- Messages Admin --}}
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>

                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                 aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">Message Center</h6>

                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="{{ asset('sbadmin2/img/undraw_profile_1.svg') }}" alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div class="font-weight-bold">
                                        <div class="text-truncate">
                                            Hi there! I am wondering if you can help me with a problem I've been having.
                                        </div>
                                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                                    </div>
                                </a>

                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                            </div>
                        </li>

                    @endif
                    {{-- ===================== END NOTIFIKASI ADMIN ===================== --}}


                    {{-- ======================================================= --}}
                    {{--              NOTIFIKASI USER / STAFF / PELAPOR          --}}
                    {{-- ======================================================= --}}
                    @if(!in_array($userRole, $allowedRoles))

                        <li class="nav-item dropdown no-arrow mx-1">

                            <a class="nav-link dropdown-toggle" href="#" id="userNotifDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                <i class="fas fa-bell fa-fw"></i>

                                @if(isset($notifCount) && $notifCount > 0)
                                    <span class="badge badge-danger badge-counter">
                                        {{ $notifCount > 5 ? '5+' : $notifCount }}
                                    </span>
                                @endif
                            </a>

                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userNotifDropdown">

                                <h6 class="dropdown-header">Notifikasi</h6>

                                @forelse($notifications as $notif)
                                    <a class="dropdown-item d-flex align-items-center" href="{{ $notif['url'] }}">
                                        <div class="mr-3">
                                            <div class="icon-circle {{ $notif['color'] }}">
                                                <i class="fas fa-bell text-white"></i>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="small text-gray-500">{{ $notif['time']->diffForHumans() }}</div>
                                            <span class="font-weight-bold">{{ $notif['title'] }}</span>
                                            <br>
                                            <span class="small">{{ Str::limit($notif['detail'], 40) }}</span>
                                        </div>
                                    </a>
                                @empty
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <div class="mr-3">
                                            <div class="icon-circle bg-secondary">
                                                <i class="fas fa-bell text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="font-weight-bold">Tidak ada notifikasi.</span>
                                        </div>
                                    </a>
                                @endforelse
                            </div>
                        </li>

                    @endif

                    {{-- ===================== END NOTIFIKASI USER ===================== --}}


                    {{-- User Dropdown --}}
                    <div class="topbar-divider d-none d-sm-block"></div>

                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                {{ auth()->user()->name ?? auth()->user()->nama }}
                            </span>
                            <img class="img-profile rounded-circle"
                                 src="{{ asset('sbadmin2/img/undraw_profile.svg') }}">
                        </a>

                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="userDropdown">

                            <a class="dropdown-item" href="#">
                                <div class="badge badge-success justify-content-center d-flex">
                                    {{ auth()->user()->role }}
                                </div>
                            </a>

                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-cog fa-sm fa-fw mr-2 text-gray-400"></i>
                                Settings
                            </a>

                            @if(in_array($userRole, $allowedRoles))
                                <a class="dropdown-item" href="{{ route('activity.log') }}">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                            @endif

                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
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
