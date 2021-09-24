
@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Libraries</li>

                <li class="breadcrumb-item active"><a href="/library/{{$library->id}}" >{{$library->name}}</a></li>


            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">

        @if($directories->count() > 0)
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><i class="fa fa-align-justify"></i>Directories
                    <div class="card-header-actions">
                        <form method="GET">
                            Search: <input type="text" id="searchDir" name="searchDir" size="50" placeholder="Search..." class="form-control" >

                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-responsive-sm table-bordered table-striped table-sm">
                        <thead>
                        <tr>
                            <th>Name</th>
                            @if($showCounters)
                                <th># Files</th>
                                <th># Subdirectories</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($directories as $directory)
                            <tr>
                                <td><a href="/library/{{$library->id}}/{{$directory->id}}">{{$directory->directory_name}}</a></td>
                                @if($showCounters)
                                    <td>{{count($directory->files)}}</td>
                                    <td>{{count($directory->directories)}}</td>
                                @endif
                        @endforeach
                        </tbody>
                    </table>
                    <nav>

                        <ul class="pagination">
                            {{$directories->onEachSide(5)->links()}}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        @endif

            @if($files->count() > 0)
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header"><i class="fa fa-align-justify"></i>Files
                            <div class="card-header-actions">
                                <form method="GET">
                                    Search: <input type="text" id="searchFile" name="searchFile" size="50" placeholder="Search..." class="form-control" >

                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-sm table-bordered table-striped table-sm">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Author</th>
                                    <th>Type</th>
                                    <th>Filesize (MB)</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($files as $file)
                                    <tr>
                                        <td><a href="/library/{{$library->id}}/{{$file->directory_id}}/{{$file->id}}">{{$file->title() ?? $file->getFilenameWithoutExtension()}}</a></td>
                                        <td>{{$file->author() ?? ""}} </td>
                                        <td><span class="badge badge-secondary">{{strtoupper(pathinfo($file->filename, PATHINFO_EXTENSION))}}</span></td>
                                        <td>{{round($file->filesize / 1024 / 1024)}} MB</td>
                                        <td><a href="/library/{{$library->id}}/{{$file->directory_id}}/{{$file->id}}/download"><button class="btn btn-sm btn-success" type="button">Download</button></a></td>
                                @endforeach
                                </tbody>
                            </table>
                            <nav>

                                <ul class="pagination">
                                    {{$files->onEachSide(5)->links()}}
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            @endif



    </div>

@endsection
