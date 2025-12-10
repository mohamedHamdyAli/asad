@extends('layouts.app')

@section('title', __('QHSE Policy'))

@section('content')
<main class="main-area fix">
        @include('qhse_policy.parts.slider')
            @include('qhse_policy.parts.body')
    </main>
@endsection
