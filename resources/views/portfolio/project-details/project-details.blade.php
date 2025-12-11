@extends('layouts.app')

@section('title', 'About')

@section('content')
    @php
        $unit = \App\Models\Unit::with(['user','homeUnitGallery'])->find($id);
    @endphp
    <main class="main-area fix">
        @include('portfolio.project-details.parts.slider')
        @include('portfolio.project-details.parts.details')
        {{-- @include('portfolio.project-details.parts.tabs') --}}
    </main>
@endsection
