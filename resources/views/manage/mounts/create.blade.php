@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Manage</li>
            <li class="breadcrumb-item"><a href="/manage/mounts">Samba & NFS Mounts</a></li>
            <li class="breadcrumb-item active">Add</li>
            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Add Samba / NFS Mount</div>
                <form class="form-horizontal" action="/manage/mounts" method="post">
                    <div class="card-body">
                        @if (session("error") && session("error") == true)
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">{{session("message")}}</div>
                        @endif
                            @csrf
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="name">Mount Name</label>
                                <div class="col-md-9">
                                    <input class="form-control" id="name" type="text" name="name" placeholder="Mount Name" required value="{{old("name")}}" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="name">Mount Type</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="type" name="type">
                                        <option @if(old("type") == "NFS") selected @endif>NFS</option>
                                        <option @if(old("type") == "Samba") selected @endif>Samba</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="name">Mount Source</label>
                                <div class="col-md-9">
                                    <input class="form-control" id="source" type="text" name="source" placeholder="Mount Source" required value="{{old("source")}}"><span class="help-block">Please enter the IP address or Hostname and directory of the share. </span>
                                    <br><span class="help-block">For NFS use the following format: IPAddress:Directory. For example: 10.10.10.10:/share1</span>
                                    <br><span class="help-block">For Samba use the following format: //IPAddress/Directory. For example: //10.10.10.10/share1</span>
                                </div>
                            </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="name">Mount To</label>
                            <div class="col-md-9">
                                <input class="form-control" id="destination" type="text" name="destination" placeholder="Mount To" required  value="{{old("destination")}}"><span class="help-block">Make sure you enter an empty directory. If the directory does not exist, we will create it for you.</span>
                                <br><span class="help-block">The directory will be created inside of <b>{{base_path("mounts/")}}</b></span>
                            </div>
                        </div>
,
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="name">Username (Required for Samba)</label>
                            <div class="col-md-9">
                                <input class="form-control" id="username" type="text" name="username" placeholder="Username" value="{{old("username")}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="name">Password (Required for Samba)</label>
                            <div class="col-md-9">
                                <input class="form-control" id="password" type="password" name="password" placeholder="Password" value="{{old("password")}}">
                            </div>
                        </div>


                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-primary" type="submit"> Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
