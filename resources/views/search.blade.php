@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Search</li>
            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Search</div>
                <form class="form-horizontal" action="/doSearch" method="post">
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
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="name">Search Field</label>
                            <div class="col-md-9">
                                <select class="form-control" id="field" name="field">
                                    <option>Filename</option>
                                    <option>Title</option>
                                    <option>Author</option>
                                    <option>Directory</option>
                                    <option disabled>---------</option>
                                    @foreach($fields as $field)
                                        <option>{{$field->type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="name">Search String</label>
                            <div class="col-md-9">
                                <input class="form-control" id="search" type="text" name="search" placeholder="Search String" required>
                            </div>
                        </div>


                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-primary" type="submit"> Search</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
