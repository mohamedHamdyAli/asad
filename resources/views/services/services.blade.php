@extends('layouts.app')

@section('title', __('Our Services'))

@section('content')
<main class="main-area fix">
        @include('services.parts.slider')
    </main>
@endsection
