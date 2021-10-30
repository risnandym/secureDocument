@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 mb-3">
            <link-form @if(isset($link)):link="{{ json_encode(\App\Http\Resources\LinkResource::make($link)) }}" @endif
                       @if(isset($query))query-url="{{ $query }}" @endif dusk="link-form">
            </link-form>
        </div>

        @if(empty($query))
        <div class="col-12 col-md-4">
            <sharer url="{{ route('link.create') }}" dusk="sharer"></sharer>
        </div>
        @endif
    </div>
</div>
@endsection
