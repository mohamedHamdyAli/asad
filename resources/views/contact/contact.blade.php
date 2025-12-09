@extends('layouts.app')

@section('title', __('Contact'))

@section('content')
    <main class="main-area fix">
        @include('contact.parts.slider')

        <section class="contact__area section-py-120">
            <div class="container">
                <div class="row gutter-24">
                    @include('contact.parts.contact-info')
                    @include('contact.parts.contact-map')
                    @include('contact.parts.contact-form')
                </div>
            </div>
        </section>
    </main>
@endsection
