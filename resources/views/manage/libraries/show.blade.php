@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Manage</li>
            <li class="breadcrumb-item"><a href="/manage/libraries">Libraries</a></li>
            <li class="breadcrumb-item active"> {{$library->name}}</li>
            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Library:  {{$library->name}}</div>
                <form class="form-horizontal" action="/manage/libraries" method="post">
                    <div class="card-body">
                            @csrf
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="name">Library Name</label>
                                <div class="col-md-9">
                                    {{$library->name}}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="name">Library Type</label>
                                <div class="col-md-9">
                                    {{$library->type}}
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="name">Library Folders</label>
                                <div class="col-md-9">
                                    <ul class="list-group folderList">
                                        @foreach($library->folders as $folder)
                                        <li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center">{{$folder->path}}</li>
                                        @endforeach
                                    </ul>

                                </div>
                            </div>


                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addFolderModal" tabindex="-1" role="dialog" aria-labelledby="addFolderModal" aria-hidden="true">
        <div class="modal-dialog modal-primary" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Folder</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <p>Please browse to the folder to add, and click the Add button</p>
                    <div class="col-md-9">
                        <input type="hidden" name="browsefolder" id="browsefolder" value="/">
                        <ul class="list-group" id="dirlist">

                        </ul>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="AddFolder" type="button">Add</button>
                </div>
            </div>
            <!-- /.modal-content-->
        </div>
        <!-- /.modal-dialog-->
    </div>

@endsection

@section("js")

    <script>

        $(document).ready(function () {


        });


    </script>
@endsection


