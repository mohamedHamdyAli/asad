@extends('layouts.app')

@section('title', __('Home'))

@section('content')
    <main class="main-area fix">
        {{-- Slider --}}
        @include('home.parts.slider')

        {{-- About --}}
        @include('home.parts.about')

        {{-- Work Process --}}
        @include('home.parts.work-process')

        {{-- Services --}}
        @include('home.parts.services')

        {{-- Video --}}
        {{-- @include('home.parts.video') --}}

        {{-- Company --}}
        @include('home.parts.company')


        {{-- Cirtifaction --}}
        @include('home.parts.cirtifaction')

        {{-- Brands --}}
        @include('home.parts.brands')
    </main>

@endsection
