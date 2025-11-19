@include('layouts.header')

<body id="page-top">

    <div id="wrapper">

      @include('layouts.sidebar')

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
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

                    <ul class="navbar-nav ml-auto">

                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
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
                        {{-- KAVLING NOTIFIKASI (HANYA UNTUK ADMIN)                 --}}
                        {{-- ======================================================= --}}
                        @php
                            // Ambil role, kecilin hurufnya
                            $userRole = strtolower(auth()->user()->role);
                        @endphp
                        
                        @if(in_array($userRole, ['admin', 'super admin', 'superadmin']))
                            
                            <li class="nav-item dropdown no-arrow mx-1">
                                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-bell fa-fw"></i>
                                    
                                    {{-- Tampilkan badge HANYA JIKA ada notif --}}
                                    @if(isset($pendingPpiCount) && $pendingPpiCount > 0)
                                        <span class="badge badge-danger badge-counter">
                                            {{ $pendingPpiCount > 5 ? '5+' : $pendingPpiCount }} 
                                        </span>
                                    @endif
                                </a>
                                
                                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="alertsDropdown">
                                    <h6 class="dropdown-header">
                                        Pusat Notifikasi (PPI Pending)
                                    </h6>

                                    @if(isset($recentPendingPpis))
                                        @forelse($recentPendingPpis as $ppi)
                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.ppi.show', $ppi->id) }}">
                                                <div class="mr-3">
                                                    <div class="icon-circle bg-primary">
                                                        <i class="fas fa-file-alt text-white"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="small text-gray-500">{{ $ppi->created_at->diffForHumans() }}</div>
                                                    <span class="font-weight-bold">
                                                        PPI baru dari: {{ $ppi->user->name ?? 'User' }}
                                                    </span>
                                                    <br>
                                                    <span class="small">{{ \Illuminate\Support\Str::limit($ppi->perangkat, 30) }}</span>
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
                                                    <span class="font-weight-bold">Tidak ada PPI baru.</span>
                                                </div>
                                            </a>
                                        @endforelse
                                    @endif
                                    
                                    <a class="dropdown-item text-center small text-gray-500" href="{{ route('admin.ppi.index') }}">
                                        Lihat Semua PPI
                                    </a>
                                </div>
                            </li>

                            <li class="nav-item dropdown no-arrow mx-1">
                                <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-envelope fa-fw"></i>
                                    <span class="badge badge-danger badge-counter">7</span>
                                </a>
                                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="messagesDropdown">
                                    <h6 class="dropdown-header">
                                        Message Center
                                    </h6>
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <div class="dropdown-list-image mr-3">
                                            <img class="rounded-circle" src="{{ asset('sbadmin2/img/undraw_profile_1.svg') }}" alt="...">
                                            <div class="status-indicator bg-success"></div>
                                        </div>
                                        <div class="font-weight-bold">
                                            <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                                problem I've been having.</div>
                                            <div class="small text-gray-500">Emily Fowler · 58m</div>
                                        </div>
                                    </a>
                                    <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                                </div>
                            </li>

                        @endif
                        {{-- AKHIR KAVLING ADMIN --}}


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
                                    <i class="fas fa-sm fa-fw mr-2 text-gray-400"></i>
                                   <div class="badge badge-success justify-content-center d-flex">
                                    {{auth()->user()->role}}
                                   </div> 
                                </a>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-cog fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                
                                {{-- =============================================== --}}
                                {{-- [UPDATE] DIBUNGKUS IF & LINK DIBENERIN          --}}
                                {{-- =============================================== --}}
                                @if(in_array($userRole, ['admin', 'super admin', 'superadmin']))
                                <a class="dropdown-item" href="{{ route('activity.log') }}">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                @endif
                                {{-- =============================================== --}}
                                {{-- [UPDATE] SELESAI                              --}}
                                {{-- =============================================== --}}
                                
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