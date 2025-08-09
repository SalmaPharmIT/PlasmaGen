@props(['header'])

@extends('include.dashboardLayout')

@section('content')
    @isset($header)
    <div class="pagetitle">
        {{ $header }}
    </div>
    @endisset

    <section class="section">
        {{ $slot }}
    </section>
@endsection 