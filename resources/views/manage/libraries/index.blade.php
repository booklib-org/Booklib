
@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Manage</li>
            <li class="breadcrumb-item active">Libraries</li>
            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><i class="fa fa-align-justify"></i> Manage Libraries
                    <div class="card-header-actions">
                        <form method="GET" action="/manage/libraries">Search: <input type="text" id="search" name="search" size="50" placeholder="Search...">
                        <a class="card-header-action btn-setting" href="/manage/libraries/create" title="Add Library">
                            <svg class="c-icon">
                                <use xlink:href="/icons/sprites/free.svg#cil-library-add"></use>
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
                            <th># Files</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($libraries as $library)
                            <tr>
                                <td><a href="/manage/libraries/{{$library->id}}">{{$library->name}}</a></td>
                                <td>{{$library->total_files}}</td>
                                <td>{{$library->type}}</td>
                                <td><form action="/manage/libraries/{{$library->id}}" method="POST"><a href="/manage/libraries/{{$library->id}}/edit"><button class="badge badge-warning" type="button">Edit</button></a>

                                        @method('DELETE')
                                        @csrf
                                        <button class="badge badge-danger" type="submit" onclick="return confirm('Are you sure?');">Delete</button>
                                    </form></td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                    <nav>

                        <ul class="pagination">
                            {{$libraries->onEachSide(5)->links()}}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>

@endsection
