@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <main class="main-area fix">
        {{-- Slider --}}
        @include('home.parts.slider')

        {{-- About --}}
        @include('home.parts.about')

        {{-- Work Process --}}
        @include('home.parts.work-process')

        {{-- Video --}}
        @include('home.parts.video')

        {{-- Company --}}
        @include('home.parts.company')

        {{-- Blog --}}
        @include('home.parts.blog')

        {{-- Brands --}}
        @include('home.parts.brands')
    </main>

@endsection
