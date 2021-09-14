@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Change Password</li>
            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Change Password</div>
                <form class="form-horizontal" method="post">
                    <div class="card-body">
                        @if (session("success") && session("success") == true)
                            <div class="alert alert-success alert-dismissible fade show" role="alert">{{session("message")}}</div>
                        @endif
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

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="name">New Password</label>
                            <div class="col-md-9">
                                <input class="form-control" id="password" type="password" name="password" placeholder="Password" required>
                            </div>
                        </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="name">Confirm New Password</label>
                                <div class="col-md-9">
                                    <input class="form-control" id="confirmPassword" type="password" name="confirmPassword" placeholder="Confirm Password" required>
                                </div>
                            </div>


                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-primary" type="submit"> Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
