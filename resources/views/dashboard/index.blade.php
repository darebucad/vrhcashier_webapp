@extends ('layouts.master')

@section ('content')

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h4">Dashboard</h1>
    @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">
        <button class="btn btn-sm btn-outline-secondary">Share</button>
        <button class="btn btn-sm btn-outline-secondary">Export</button>
      </div>
      <button class="btn btn-sm btn-outline-secondary dropdown-toggle">
        <span data-feather="calendar"></span>
        This week
      </button>
    </div>
  </div>

  <div class="form-group row">

    <div class="col-md-4">
      <img src="..." alt="..." class="img-thumbnail">
    </div>

    <div class="col-md-4">
      <img src="..." alt="..." class="img-thumbnail">
    </div>

    <div class="col-md-4">
      <img src="..." alt="..." class="img-thumbnail">
    </div>

  </div>


<hr>
  <div class="form-group row">
    <h4>Total Sales: </h4>
  </div>

</main>

@endsection
