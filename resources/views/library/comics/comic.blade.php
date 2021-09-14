@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Libraries</li>

                <li class="breadcrumb-item"><a href="/library/{{$library->id}}" >{{$library->name}}</a></li>

            <li class="breadcrumb-item">{{$comic->filename}}</li>
            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">{{$comic->filename}}</div>
                    <div class="card-body">


                        <label class="col-md-3 col-form-label" for="name"><a href="/library/{{$library->id}}/{{$comic->directory_id}}/{{$comic->id}}/download">Download File</a></label>
                        <table class="table table-responsive-sm table-striped">
                            <tbody>
                            <tr>
                                <td>Filename</td>
                                <td>{{$comic->filename}}</td>
                            </tr>
                            <tr>
                                <td>Title</td>
                                <td>{{$comic->title() ?? "-"}}</td>
                            </tr>
                            <tr>
                                <td>Author</td>
                                <td>{{$comic->author() ?? "-"}}</td>
                            </tr>
                            @foreach($comic->otherMeta() as $meta)
                                @if($meta->type->type != "creator")
                                    @if($meta->type->type != "title")
                                        <tr>
                                            <td>{{ucfirst($meta->type->type)}}</td>
                                            <td>{{$meta->value}}</td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach
                            </tbody>
                        </table>

                        </div>
                    </div>

            </div>
        </div>
    </div>

@endsection
