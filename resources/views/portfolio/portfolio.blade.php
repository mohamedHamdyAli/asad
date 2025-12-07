@extends('layouts.app')

@section('title', 'About')

@section('content')
    <main class="main-area fix">
        @include('portfolio.parts.slider')
        @include('portfolio.parts.projects')
    </main>
@endsection
