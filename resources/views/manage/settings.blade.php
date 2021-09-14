@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Manage</li>
            <li class="breadcrumb-item active">Settings</li>
            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">General Settings</div>
                <form class="form-horizontal" method="post">
                    <div class="card-body">
                        @if (session("success") && session("success") == true)
                            <div class="alert alert-success alert-dismissible fade show" role="alert">{{session("message")}}</div>
                        @endif
                        @csrf
                        @method("put")
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="name">Thumbnail Quality</label>
                            <div class="col-md-9">
                                <select class="form-control" id="type" name="thumbnail_quality">
                                    <option @if($thumbnail_quality->value == "High") selected @endif>High</option>
                                    <option @if($thumbnail_quality->value == "Medium") selected @endif>Medium</option>
                                    <option value="Low" @if($thumbnail_quality->value == "Low") selected @endif>Low (Default)</option>
                                </select>
                                <span class="help-block">Please set the thumbnail quality. Please note: Setting higher quality may exhaust your disk space with large libraries</span>

                            </div>
                        </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="name">Indexing Frequency</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="type" name="scanning_frequency">
                                        <option @if($scanning_frequency->value == "Every 15 Minutes") selected @endif>Every 15 Minutes</option>
                                        <option @if($scanning_frequency->value == "Every 30 Minutes") selected @endif>Every 30 Minutes</option>
                                        <option @if($scanning_frequency->value == "Every Hour") selected @endif>Every Hour</option>
                                        <option @if($scanning_frequency->value == "Every 3 Hours") selected @endif>Every 3 Hours</option>
                                        <option @if($scanning_frequency->value == "Every 6 Hours") selected @endif>Every 6 Hours</option>
                                        <option @if($scanning_frequency->value == "Every 12 Hours") selected @endif>Every 12 Hours</option>
                                        <option @if($scanning_frequency->value == "Every 24 Hours") selected @endif>Every 24 Hours</option>


                                    </select>


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
