<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <img src="/admin/dist/img/AdminLTELogo.png" alt="AdminLogo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                {{-- <img src="/profil/profil.png" class="img-circle elevation-2" alt="User Image"> --}}
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    {{ Auth::user()->name }}
                    {{-- Name --}}
               </a>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

                @if(auth()->user()->role === 'admin')

                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                    </li>

                 <li class="nav-header">Fitur</li>

                    <li class="nav-item">
                        <a href="{{ route('teacher.manage') }}" class="nav-link {{ request()->is('teacher*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Guru
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('class.index') }}" class="nav-link {{ request()->is('class*') || request()->is('student*') ? 'active' : '' }}"
>
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Siswa
                                <span class="badge badge-info right"></span>
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link {{ request()->is('device*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Mesin Fingerprint
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('device.fingerprint.index') }}" class="nav-link {{ request()->is('device/fingerprint') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Register Fingerprint</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('device.fingerprint.scan') }}" class="nav-link {{ request()->is('device/fingerprint/scan') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Scan Fingerprint</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('attendance.manage') }}" class="nav-link {{ request()->is('attendance/manage') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Riwayat Absensi
                                <span class="badge badge-info right"></span>
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('reports.manage') }}" class="nav-link {{ request()->is('reports/manage') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Laporan
                                <span class="badge badge-info right"></span>
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('backup.index') }}" class="nav-link {{ request()->is('system*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Backup
                                <span class="badge badge-info right"></span>
                            </p>
                        </a>
                    </li>

                @endif

                @if(auth()->user()->role === 'teacher')
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                    </li>

                    <li class="nav-header">Fitur</li>

                    <li class="nav-item">
                        <a href="{{ route('attendance.manage') }}" class="nav-link }">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Riwayat Absensi
                                <span class="badge badge-info right"></span>
                            </p>
                        </a>
                    </li>

                @endif


            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
