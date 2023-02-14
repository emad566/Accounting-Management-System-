<li class="nav-item dropdown u-pro">
    <a class="nav-link dropdown-toggle waves-effect waves-dark profile-pic" href="" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"><img
            src="{{ getSrc(Auth::user(), 'image') }}" alt="{{ Auth::user()->name }}" class=""> <span
            class="hidden-md-down">{{ Auth::user()->name }} &nbsp;<i class="fa fa-angle-down"></i></span> </a>
    <div class="dropdown-menu dropdown-menu-right animated flipInY text-left" dir="rtl">
        <!-- text-->
        <a href="{{ route('mainUser.profile') }}" class="dropdown-item"><i class="ti-user"></i> {{ trans('main.My Profile') }}</a>
        {{-- <!-- text-->
        <a href="javascript:void(0)" class="dropdown-item"><i class="ti-wallet"></i> My Balance</a>
        <!-- text-->
        <a href="javascript:void(0)" class="dropdown-item"><i class="ti-email"></i> Inbox</a>
        <!-- text-->
        <div class="dropdown-divider"></div>
        <!-- text-->
        <a href="javascript:void(0)" class="dropdown-item"><i class="ti-settings"></i> Account Setting</a> --}}
        <!-- text-->
        <div class="dropdown-divider"></div>
        <!-- text-->
        <a href="javascript:void(0)" onclick="event.preventDefault();
            localStorage.clear();
            $('#logOutForm').submit();"
            class="dropdown-item"
            ><i class="fa fa-power-off"></i> {{ trans('main.Logout') }}</a>
        <form id="logOutForm" method="POST" action="{{ (Auth::guard('admin')->check()) ? route('admin.logout') : route('logout') }}">
            @csrf
        </form>
        <!-- text-->
    </div>
</li>
