<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    {{-- LEFT --}}
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link"
               data-widget="pushmenu"
               href="#"
               role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    {{-- RIGHT --}}
    <ul class="navbar-nav ml-auto">

        {{-- USER MENU --}}
        <li class="nav-item dropdown">
            <a class="nav-link d-flex align-items-center"
               data-toggle="dropdown"
               href="#">

                <i class="far fa-user-circle fa-lg mr-1"></i>

                <span class="d-none d-md-inline">
                    {{ Auth::user()->name }}
                </span>
            </a>

            <div class="dropdown-menu dropdown-menu-right shadow-sm">

                {{-- USER INFO --}}
                <div class="dropdown-item-text">
                    <strong>{{ Auth::user()->name }}</strong><br>
                    <small class="text-muted">
                        Role: {{ ucfirst(Auth::user()->role) }}
                    </small>
                </div>

                <div class="dropdown-divider"></div>

                {{-- PROFILE --}}
                <a href="{{ route('profile.edit') ?? '#' }}"
                   class="dropdown-item">
                    <i class="fas fa-user mr-2"></i>
                    Profile
                </a>

                <div class="dropdown-divider"></div>

                {{-- LOGOUT --}}
                <a href="#"
                   class="dropdown-item text-danger"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </a>

                <form id="logout-form"
                      action="{{ route('logout') }}"
                      method="POST"
                      class="d-none">
                    @csrf
                </form>

            </div>
        </li>

    </ul>
</nav>
