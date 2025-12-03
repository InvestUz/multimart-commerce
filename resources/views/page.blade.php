@extends('layouts.app')

@section('title', $page->meta_title ?? $page->title . ' - ' . config('app.name'))

@section('meta_description', $page->meta_description ?? '')
@section('meta_keywords', $page->meta_keywords ?? '')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $page->title }}</h1>
        
        @if($page->featured_image)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $page->featured_image) }}" alt="{{ $page->title }}" class="w-full h-auto rounded-lg">
            </div>
        @endif
        
        <div class="prose max-w-none">
            {!! $page->content !!}
        </div>
    </div>
</div>
@endsection