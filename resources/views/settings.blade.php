@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">User Settings</li>
            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">User Settings</div>
                <form class="form-horizontal" method="post">
                    <div class="card-body">
                        @if (session("success") && session("success") == true)
                            <div class="alert alert-success alert-dismissible fade show" role="alert">{{session("message")}}</div>
                        @endif
                        @csrf
                        @method("put")
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="name">Items per page</label>
                            <div class="col-md-9">
                                <input class="form-control" id="items_per_page" type="text" name="items_per_page" required value="{{$settings["items_per_page"]->value ?? '20'}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="name">Thumbnails Size (Width in pixels)</label>
                            <div class="col-md-9">
                                <input class="form-control" id="thumbnail_size" type="text" name="thumbnail_size" required value="{{$settings["thumbnail_size"]->value ?? '100'}}" >
                                <span class="help-block">It is recommended to keep this to round 100 (px)</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="name">Default View</label>
                            <div class="col-md-9">
                                <select class="form-control" id="default_view" name="default_view">
                                    <option value="Table View" @if(isset($settings["default_view"]) && $settings["default_view"]->value == "Table View") selected @endif>Table View (no Thumbnails)</option>
                                    <option value="Grid View" @if(isset($settings["default_view"]) && $settings["default_view"]->value == "Grid View") selected @endif >Grid View (with Thumbnails)</option>
                                </select>
                            </div>
                        </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="name">Show Counter in table view</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="show_counters" name="show_counters">
                                        <option value="0" @if(isset($settings["show_counters"]) && $settings["show_counters"]->value == false) selected @endif>No</option>
                                        <option value="1" @if(isset($settings["show_counters"]) && $settings["show_counters"]->value == true) selected @endif >Yes (Slower page loads)</option>
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
