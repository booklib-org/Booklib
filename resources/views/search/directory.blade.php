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
                                <th>Name</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach($results as $dir)
                                <tr>
                                    <td><a href="/directory/{{$dir->id}}">{{$dir->directory_name}}</a></td>

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
