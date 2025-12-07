@extends('layouts.app')

@section('title', 'About')

@section('content')
    <main class="main-area fix">
        @include('about.parts.slider')
        @include('about.parts.about-one')
        @include('about.parts.about-two')
        @include('about.parts.cta')
        @include('about.parts.video')
    </main>
@endsection
