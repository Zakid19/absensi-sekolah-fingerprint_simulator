<aside class="main-sidebar sidebar-dark-primary elevation-4">

    {{-- Brand Logo --}}
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('images/mandiri-bersemi.png') }}"
             alt="Logo"
             class="brand-image img-circle elevation-2"
             style="opacity: .9">
        <span class="brand-text font-weight-semibold">
            Absensi Sekolah
        </span>
    </a>

    {{-- Sidebar --}}
    <div class="sidebar">

        {{-- User Panel --}}
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            {{-- <div class="image">
                <img src="{{ asset('images/avatar-default.png') }}"
                     class="img-circle elevation-2"
                     alt="User Image">
            </div> --}}
            <div class="info">
                <span class="d-block text-white font-weight-bold">
                    {{ Auth::user()->name }}
                </span>
                {{-- <small class="text-muted text-capitalize">
                    {{ Auth::user()->role }}
                </small> --}}
            </div>
        </div>

        {{-- Sidebar Menu --}}
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false">

            {{-- ================= ADMIN ================= --}}
            @if(auth()->user()->role === 'admin')

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">MASTER DATA</li>

                {{-- Guru --}}
                <li class="nav-item">
                    <a href="{{ route('teacher.manage') }}"
                    class="nav-link {{ request()->is('teacher*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>Guru</p>
                    </a>
                </li>

                {{-- Siswa --}}
                <li class="nav-item">
                    <a href="{{ route('class.index') }}"
                    class="nav-link {{ request()->is('class*','student*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Siswa</p>
                    </a>
                </li>

                <li class="nav-header">FINGERPRINT</li>

                {{-- ================= FINGERPRINT SIMULATOR ================= --}}
                @php
                    $fingerprintSimulatorActive = request()->is('simulator/fingerprint*');
                @endphp

                <li class="nav-item has-treeview {{ $fingerprintSimulatorActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $fingerprintSimulatorActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-laptop-code"></i>
                        <p>
                            Fingerprint Simulator
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('simulator.fingerprint.index') }}"
                            class="nav-link {{ request()->is('simulator/fingerprint') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-id-card"></i>
                                <p>Registrasi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('simulator.fingerprint.scan') }}"
                            class="nav-link {{ request()->is('simulator/fingerprint/scan') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-hand-point-up"></i>
                                <p>Scan</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- ================= FINGERPRINT DEVICE ================= --}}
                @php
                    $fingerprintDeviceActive = request()->is('device/fingerprints*');
                @endphp

                <li class="nav-item has-treeview {{ $fingerprintDeviceActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $fingerprintDeviceActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-microchip"></i>
                        <p>
                            Fingerprint Device
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('device.fingerprints.pending') }}"
                            class="nav-link {{ request()->is('device/fingerprints/pending') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-plus"></i>
                                <p>Registrasi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('device.fingerprints.attendance') }}"
                            class="nav-link {{ request()->is('device/fingerprints/attendance') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-fingerprint"></i>
                                <p>Absen Finger</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('device.fingerprints.student_reset') }}"
                            class="nav-link {{ request()->is('device/fingerprints/class*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-undo-alt"></i>
                                <p>Reset Fingerprint</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-header">ABSENSI</li>

                {{-- Jam Absensi --}}
                <li class="nav-item">
                    <a href="{{ route('attendance.settings.edit') }}"
                    class="nav-link {{ request()->is('attendance/settings*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clock"></i>
                        <p>Jam Absensi</p>
                    </a>
                </li>

                {{-- Riwayat Absensi --}}
                <li class="nav-item">
                    <a href="{{ route('attendance.manage') }}"
                    class="nav-link {{ request()->is('attendance/manage') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>Riwayat Absensi</p>
                    </a>
                </li>

                {{-- Log Absensi --}}
                <li class="nav-item">
                    <a href="{{ route('absensi.log') }}"
                    class="nav-link {{ request()->is('absensi/log') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>Log Absensi</p>
                    </a>
                </li>

                <li class="nav-header">SYSTEM</li>

                {{-- Laporan --}}
                <li class="nav-item">
                    <a href="{{ route('reports.manage') }}"
                    class="nav-link {{ request()->is('reports/manage') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Laporan</p>
                    </a>
                </li>

                {{-- Backup --}}
                <li class="nav-item">
                    <a href="{{ route('backup.index') }}"
                    class="nav-link {{ request()->is('system*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-database"></i>
                        <p>Backup</p>
                    </a>
                </li>

            @endif

            {{-- ================= TEACHER ================= --}}
            @if(auth()->user()->role === 'teacher')

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">ABSENSI</li>

                <li class="nav-item">
                    <a href="{{ route('attendance.manage') }}"
                    class="nav-link {{ request()->is('attendance/manage') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>Riwayat Absensi</p>
                    </a>
                </li>

            @endif

            </ul>
        </nav>

        {{-- /.sidebar-menu --}}
    </div>
    {{-- /.sidebar --}}
</aside>
