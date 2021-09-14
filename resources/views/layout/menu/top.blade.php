<header class="c-header c-header-light c-header-fixed c-header-with-subheader">
    <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar" data-class="c-sidebar-show">
        <svg class="c-icon c-icon-lg">
            <use xlink:href="/icons/sprites/free.svg#cil-menu"></use>
        </svg>
    </button><a class="c-header-brand d-lg-none" href="/">
        BookLib</a>

    <button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar" data-class="c-sidebar-lg-show" responsive="true">
        <svg class="c-icon c-icon-lg">
            <use xlink:href="/icons/sprites/free.svg#cil-menu"></use>
        </svg>
    </button>
    <ul class="c-header-nav ml-auto mr-4">
        <li class="c-header-nav-item dropdown"><a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <div class="c-avatar"><svg class="c-sidebar-nav-icon">
                        <use xlink:href="/icons/sprites/free.svg#cil-settings"></use>
                    </svg></div>
            </a>
            <div class="dropdown-menu dropdown-menu-right pt-0">
                @if(auth()->user()->isAdmin())
                <div class="dropdown-header bg-light py-2"><strong>Settings</strong></div>
                <a class="dropdown-item" href="/manage/settings">
                    <svg class="c-icon mr-2">
                        <use xlink:href="/icons/sprites/free.svg#cil-settings"></use>
                    </svg> General Settings</a>
                    <a class="dropdown-item" href="/manage/importopds">
                        <svg class="c-icon mr-2">
                            <use xlink:href="/icons/sprites/free.svg#cil-library-add"></use>
                        </svg> Import OPDS feed</a>
                   <!-- <a class="dropdown-item" href="/manage/mounts">
                        <svg class="c-icon mr-2">
                            <use xlink:href="/icons/sprites/free.svg#cil-storage"></use>
                        </svg> Manage Samba / NFS Mounts</a>-->
                <a class="dropdown-item" href="/manage/libraries">
                    <svg class="c-icon mr-2">
                        <use xlink:href="/icons/sprites/free.svg#cil-library"></use>
                    </svg> Manage Libraries</a>
                <a class="dropdown-item" href="/manage/users">
                    <svg class="c-icon mr-2">
                        <use xlink:href="/icons/sprites/free.svg#cil-user"></use>
                    </svg> Manage Users</a>
                @endif
                <div class="dropdown-header bg-light py-2"><strong>Account</strong></div>
                    <a class="dropdown-item" href="/settings">
                        <svg class="c-icon mr-2">
                            <use xlink:href="/icons/sprites/free.svg#cil-settings"></use>
                        </svg> User Settings</a>
                <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/changePassword">
                        <svg class="c-icon mr-2">
                            <use xlink:href="/icons/sprites/free.svg#cil-keyboard"></use>
                        </svg> Change Password</a>
            <a class="dropdown-item" href="/logout">
                    <svg class="c-icon mr-2">
                        <use xlink:href="/icons/sprites/free.svg#cil-account-logout"></use>
                    </svg> Logout</a>
            </div>
        </li>
    </ul>
    @yield("breadcrumbs")
</header>
