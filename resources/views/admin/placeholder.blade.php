@extends('layouts.app')

@section('title', $title ?? 'Page')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">{{ $title ?? 'Page' }}</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="mdi mdi-tools" style="font-size: 64px; color: #6c757d;"></i>
                    <h3 class="mt-3">{{ $title ?? 'Page' }}</h3>
                    <p class="text-muted">This page is under development and will be available soon.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

