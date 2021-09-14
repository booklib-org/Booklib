<ul class="c-sidebar-nav">
    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="/">
            <svg class="c-sidebar-nav-icon">
                <use xlink:href="/icons/sprites/free.svg#cil-speedometer"></use>
            </svg> Dashboard</a></li>
    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="/search">
            <svg class="c-sidebar-nav-icon">
                <use xlink:href="/icons/sprites/free.svg#cil-search"></use>
            </svg> Search</a></li>
    <li class="c-sidebar-nav-title">Libraries</li>

    @foreach(\App\Models\Library::orderBy("name")->get() as $lib)
        <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="/library/{{$lib->id}}">
            <svg class="c-sidebar-nav-icon">
                <use xlink:href="/icons/sprites/free.svg#cil-library"></use>
            </svg>
            {{$lib->name}}</a>
        </li>
    @endforeach

</ul>
<button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent" data-class="c-sidebar-minimized"></button>
