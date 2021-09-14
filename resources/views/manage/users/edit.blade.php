@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Manage</li>
            <li class="breadcrumb-item"><a href="/manage/users">Users</a></li>
            <li class="breadcrumb-item">{{$user->username}}</li>
            <li class="breadcrumb-item active">Edit</li>
            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Edit User: {{$user->username}}</div>
                <form class="form-horizontal" action="/manage/users/{{$user->id}}" method="post">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible" id="sectionAlert">

                                @foreach($errors->all() as $error)
                                    <li>{{$error}}</li>
                                @endforeach
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                            @csrf
                        @method('PUT')
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="name">Username</label>
                                <div class="col-md-9">
                                    <input class="form-control" id="username" type="text" name="username" placeholder="Username" required value="{{$user->username}}">
                                </div>
                            </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="name">Email</label>
                            <div class="col-md-9">
                                <input class="form-control" id="email" type="text" name="email" placeholder="Email" required value="{{$user->email}}">
                            </div>
                        </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="role">User Role</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="role" name="role">
                                        <option @if($user->role == "Administrator") selected @endif>Administrator</option>
                                        <option @if($user->role == "Reader") selected @endif>Reader</option>
                                    </select>
                                    <span class="help-block">Please select the type of user</span>
                                </div>
                            </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="name">Reset Password</label>
                            <div class="col-md-9 col-form-label">
                                <div class="form-check checkbox">
                                    <div class="col-md-9">
                                        <input class="form-check-input" id="resetpassword" type="checkbox" name="resetpassword"  value="true">
                                    </div>

                                </div>

                            </div>


                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-primary" type="submit"> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



@endsection
