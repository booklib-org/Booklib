
@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Manage</li>
            <li class="breadcrumb-item active">Samba & NFS Mounts</li>
            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><i class="fa fa-align-justify"></i> Manage Samba / NFS Mounts
                    <div class="card-header-actions">
                        <form method="GET" action="/manage/mounts">
                        <a class="card-header-action btn-setting" href="/manage/mounts/create" title="Add Samba / NFS Mount">
                            <svg class="c-icon">
                                <use xlink:href="/icons/sprites/free.svg#cil-storage"></use>
                            </svg>
                        </a>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if (session("success") && session("success") == true)
                        <div class="alert alert-success alert-dismissible fade show" role="alert">{{session("message")}}</div>
                    @endif

                    <table class="table table-responsive-sm table-bordered table-striped table-sm">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Mount Point</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($mounts as $mount)
                            <tr>
                                <td>{{$mount->name}}</td>
                                <td>{{$mount->type}}</td>
                                <td>{{$mount->mount_from}}</td>
                                <td>{{$mount->mount_to}}</td>
                                <td><form action="/manage/mounts/{{$mount->id}}" method="POST">

                                        @method('DELETE')
                                        @csrf
                                        <button class="badge badge-danger" type="submit" onclick="return confirm('Are you sure? Any entries and thumbnails associated with this mount will also be removed from the system');">Delete</button>
                                    </form></td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                    <nav>

                        <ul class="pagination">
                            {{$mounts->onEachSide(5)->links()}}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>

@endsection
