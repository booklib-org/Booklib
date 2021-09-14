@extends("layout.app")

@section("breadcrumbs")
    <div class="c-subheader px-3">
        <!-- Breadcrumb-->
        <ol class="breadcrumb border-0 m-0">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Dashboard</li>
            <!-- Breadcrumb Menu-->
        </ol>
    </div>
@endsection

@section("page")

    <div class="row">
        @foreach($libraries as $library)

            <div class="col-6 col-lg-3">
                <div class="card overflow-hidden">
                    <a href="/library/{{$library->id}}">
                    <div class="card-body p-0 d-flex align-items-center">
                        <div class="bg-primary p-4 mfe-3">
                            <svg class="c-icon c-icon-xl">
                                <use xlink:href="/icons/sprites/free.svg#cil-library"></use>
                            </svg>
                        </div>
                        <div>
                            <div class="text-value text-primary">{{$library->name}}</div>

                        </div>
                    </div>
                    </a>
                </div>
            </div>

        @endforeach
    </div>

@endsection
