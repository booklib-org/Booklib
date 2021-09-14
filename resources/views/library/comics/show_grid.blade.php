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

                    <div class="row text-center">
                        @foreach($directories as $directory)
                            <div class="col-6 col-sm-4 col-md-2">
                                <a href="/library/{{$library->id}}/{{$directory->id}}">
                                    @if(isset($directory->thumbnail))
                                        <img src="{{$directory->thumbnail->storage_path . $directory->thumbnail->filename }}" width="{{$thumbnailSize}}px"
                                             onerror="this.onerror=null;this.src='/icons/svg/free/cil-image.svg';">
                                    @else
                                        <img src="/icons/svg/free/cil-image.svg" width="{{$thumbnailSize}}px"
                                             onerror="this.onerror=null;this.src='/icons/svg/free/cil-image.svg';">
                                    @endif

                                    <div>{{$directory->directory_name}}</div>
                                </a>
                            </div>
                        @endforeach
                    </div>





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



                            <div class="row text-center">
                                @foreach($files as $file)

                                     <div class="col-6 col-sm-4 col-md-2">
                                        <a href="/library/{{$library->id}}/{{$file->directory_id}}/{{$file->id}}">
                                            @if(isset($file->thumbnail))

                                            <img src="{{$file->thumbnail->storage_path . $file->thumbnail->filename }}" width="{{$thumbnailSize}}px"
                                                 onerror="this.onerror=null;this.src='/icons/svg/free/cil-image.svg';">
                                            @else
                                                <img src="/icons/svg/free/cil-image.svg" width="{{$thumbnailSize}}px"
                                                     onerror="this.onerror=null;this.src='/icons/svg/free/cil-image.svg';">

                                            @endif
                                            <div>{{$file->title()  ?? $file->getFilenameWithoutExtension()}}</div>
                                        </a>
                                    </div>

                                @endforeach
                            </div>




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
