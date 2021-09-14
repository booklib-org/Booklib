@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Manage</li>
            <li class="breadcrumb-item"><a href="/manage/libraries">Libraries</a></li>
            <li class="breadcrumb-item active">Add</li>
            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Add Library</div>
                <form class="form-horizontal" action="/manage/libraries" method="post">
                    <div class="card-body">
                            @csrf
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="name">Library Name</label>
                                <div class="col-md-9">
                                    <input class="form-control" id="name" type="text" name="name" placeholder="Library Name" required><span class="help-block">Please enter the library name you'd like to add</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="name">Library Type</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="type" name="type">
                                        <option>Books</option>
                                        <option>Comics</option>
                                    </select>
                                    <span class="help-block">Please select the type of library</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label" for="name">Folders To Scan</label>
                                <div class="col-md-9">
                                    <ul class="list-group folderList">
                                        <a href="" data-toggle="modal" data-target="#addFolderModal"><li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center active">Add Folder</li></a>
                                    </ul>

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

            var showData = $('#dirlist');
            $.getJSON('/api/manage/libraries/folders/browse/Lw==', function (data) {
                $('.dirlistItem').remove();
                $.each(data, function(index, value) {
                    $("#dirlist").append('<li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center dirlistItem" >' + value + '</li>');
                });
            });

            $("#dirlist").on("click", "li", function() {

                if($(this).text() === "Parent Directory"){
                    $("#browsefolder").val($("#browsefolder").val().replace($("#browsefolder").val().split("/").pop(), ""));
                    $("#browsefolder").val($("#browsefolder").val().substring(0, $("#browsefolder").val().length -1));
                    if($("#browsefolder").val().length === 0){
                        $("#browsefolder").val("/");
                    }
                }else{
                    $("#browsefolder").val($(this).text());
                }

                $.getJSON('/api/manage/libraries/folders/browse/' + window.btoa($("#browsefolder").val()), function (data) {
                    $('.dirlistItem').remove();
                    $("#dirlist").append('<li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center dirlistItem" >Parent Directory</li>');
                    $.each(data, function(index, value) {
                        $("#dirlist").append('<li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center dirlistItem" >' + value + '</li>');
                    });
                });
            });

            $("#AddFolder").on("click", function(){
                $.getJSON('/api/manage/libraries/folders/browse/Lw==', function (data) {
                    $('.dirlistItem').remove();
                    $.each(data, function(index, value) {
                        $("#dirlist").append('<li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center dirlistItem" >' + value + '</li>');
                    });
                });

                $.get('/api/manage/libraries/folders/exists/' + window.btoa($("#browsefolder").val()), function(data){
                    if(data === "1"){
                        folderToAdd = $("#browsefolder").val();
                        $(".folderList").append('<li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center" id="RemoveFolder" value="' + folderToAdd + '" >' + folderToAdd + '<span class="list-group-item list-group-item-action list-group-item-warning col-5" >Please Note: This folder is already added to a different library</span><button class="btn btn-danger" id="RemoveFolderButton"  type="button" value="' + folderToAdd + '">Remove</button></li>');
                        $(".folderList").append('<input type="hidden" name="folder[]" id="folder" value="' + folderToAdd + '">');
                        $("#browsefolder").val("/");
                        $("#addFolderModal").modal('toggle');
                    }else{
                        folderToAdd = $("#browsefolder").val();
                        $(".folderList").append('<li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center" id="RemoveFolder" value="' + folderToAdd + '" >' + folderToAdd + '<button class="btn btn-danger" id="RemoveFolderButton"  type="button" value="' + folderToAdd + '">Remove</button></li>');
                        $(".folderList").append('<input type="hidden" name="folder[]" id="folder" value="' + folderToAdd + '">');
                        $("#browsefolder").val("/");
                        $("#addFolderModal").modal('toggle');
                    }
                });




            });

            $(".form-horizontal").on("click", "#RemoveFolderButton", function(){
                FolderToRemove = $(this).val();
                $('input[value="' + FolderToRemove + '"').remove();
                $('li[value="' + FolderToRemove + '"').remove();
            });


        });


    </script>
@endsection


