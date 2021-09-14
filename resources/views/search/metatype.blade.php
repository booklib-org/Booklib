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

        @if($results->count() > 0)
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header"><i class="fa fa-align-justify"></i>Search Results
                        <div class="card-header-actions">

                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-responsive-sm table-bordered table-striped table-sm">
                            <thead>
                            <tr>
                                <th>{{ucfirst(\Illuminate\Support\Facades\Session::get("type"))}}</th>
                                <th>Name</th>
                                <th>Author</th>
                                <th>Type</th>
                                <th>Filesize (MB)</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($results as $file)
                                <tr>

                                    <td>{{$file->value}}</td>
                                    <td><a href="/file/{{$file->file_id}}">{{$file->getTitleOrFilename()}}</a></td>
                                    <td>{{$file->Author()}}</td>
                                    <td><span class="badge badge-secondary">{{strtoupper(pathinfo($file->filename, PATHINFO_EXTENSION))}}</span></td>
                                    <td>{{round($file->filesize / 1024 / 1024)}} MB</td>
                                    <td><a href="/file/{{$file->file_id}}/download"><button class="btn btn-sm btn-success" type="button">Download</button></a></td>
                            @endforeach
                            </tbody>
                        </table>
                        <nav>

                            <ul class="pagination">
                                {{$results->onEachSide(5)->links()}}
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        @endif

    </div>

@endsection
