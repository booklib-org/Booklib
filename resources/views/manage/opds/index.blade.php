
@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Manage</li>
            <li class="breadcrumb-item active">Import OPDS Feed</li>
            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><i class="fa fa-align-justify"></i> Manage OPDS Feeds
                    <div class="card-header-actions">
                        <a class="card-header-action btn-setting" href="/manage/importopds/create" title="Add OPDS Feed">
                            <svg class="c-icon">
                                <use xlink:href="/icons/sprites/free.svg#cil-library-add"></use>
                            </svg>
                        </a>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if (session("success") && session("success") == true)
                        <div class="alert alert-success alert-dismissible fade show" role="alert">{{session("message")}}</div>
                    @endif

                    <table class="table table-responsive-sm table-bordered table-striped table-sm">
                        <thead>
                        <tr>
                            <th>URL</th>
                            <th>Username</th>
                            <th>Import To</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($opds as $opd)
                            <tr>
                                <td>{{$opd->url}}</td>
                                <td>{{$opd->username}}</td>
                                <td>{{\App\Models\LibraryFolder::findOrFail($opd->library_folder_id)->path}}</td>
                                <td><form action="/manage/importopds/{{$opd->id}}" method="POST">

                                        @method('DELETE')
                                        @csrf
                                        <button class="badge badge-danger" type="submit" onclick="return confirm('Are you sure?');">Delete</button>
                                    </form></td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                    <nav>

                        <ul class="pagination">
                            {{$opds->onEachSide(5)->links()}}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>

@endsection
