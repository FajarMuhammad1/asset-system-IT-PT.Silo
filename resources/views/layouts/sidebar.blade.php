  <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

           <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
    <div class="sidebar-brand-icon rotate-n-0">
        
        <i class="fas fa-cogs"></i>

    </div>
    <div class="sidebar-brand-text mx-3"> Asset System </div>
</a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ $menuDashboard ?? '' }}">
                <a class="nav-link" href="{{ route ('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Administration
            </div>


            <!-- Nav Item - Charts -->
            <li class="nav-item">
               <a class="nav-link collapsed" href="{{ route('ppi') }}">
                    <i class="fas fa-fw fa-clipboard"></i>
                    <span>PPI Request</span>
                </a>
              
                    
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{route('surat-jalan.index')}}">
                    <i class="fas fa-envelope "></i>
                    <span>Surat Jalan</span></a>
            </li>


                <li class="nav-item{{ $menuPengguna ?? '' }}">
                <a class="nav-link" href="{{ route('pengguna.index') }}">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Pengguna/User</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('team') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Team IT</span></a>
            </li>
        
            <!-- Divider -->
             <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Task/Heldesk
            </div>

            <!-- Nav Item - Tables -->
            <li class="nav-item {{ $menuTeam ?? '' }}">
                <a class="nav-link" href="tables.html">
                    <i class="fas fa-fw fa-desktop"></i>
                    <span>Helpdesk Monitoring</span></a>
            </li>
            <!-- Nav Item - Tables -->
            <li class="nav-item {{ $menuTeam ?? '' }}">
                <a class="nav-link" href="tables.html">
                    <i class="fas fa-fw fa-tasks"></i>
                    <span>Task Info</span></a>
            </li>
           

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->