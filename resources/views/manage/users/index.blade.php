
@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Manage</li>
            <li class="breadcrumb-item active">Users</li>
            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><i class="fa fa-align-justify"></i> Manage Users
                    <div class="card-header-actions">
                        <form method="GET" action="/manage/users">Search: <input type="text" id="search" name="search" size="50" placeholder="Search...">
                        <a class="card-header-action btn-setting" href="/manage/users/create" title="Add User">
                            <svg class="c-icon">
                                <use xlink:href="/icons/sprites/free.svg#cil-user-plus"></use>
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
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{$user->username}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->role}}</td>
                                <td><form action="/manage/users/{{$user->id}}" method="POST"><a href="/manage/users/{{$user->id}}/edit"><button class="badge badge-warning" type="button">Edit</button></a>

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
                            {{$users->onEachSide(5)->links()}}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>

@endsection
