<!-- @extends('layouts.admin') -->

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Dashboard') }}</h1>

    @if (session('success'))
    <div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success border-left-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <nav aria-label="Page navigation example">
      <ul class="pagination justify-content-end">
          <a class="page-link" href="{{ url('/file-upload') }}">+ Ajukan dokumen baru</a>
        </li>
      </ul>
    </nav>
    <!-- <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(100)->generate($rd_string=Str::random(100))) !!} ">
   {{ QrCode::generate($rd_string=Str::random(150)); }} -->
        
    <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Document</th>
            <th scope="col">View</th>
            <!-- <th scope="col">Handle</th> -->
          </tr>
        </thead>
        <tbody>
          @foreach($files as $f)
            <tr>
            <td>{{$f->file_path}}</td> 
            <td>{{$f->name}}</td>

             <td><a href="{{Storage::url($f->name)}}">Klik</a>
               </td>
               <!-- <td><form action="/file-delete" method="post">
               {!! method_field('delete') !!}
               <button type="submit">Delete</button>
               </form></td> -->
            </tr>
          @endforeach  
        </tbody>
    </table>
    
@endsection
