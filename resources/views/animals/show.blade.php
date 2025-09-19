@extends('layouts.app')

@section('title', $animal->name)

@section('content')
    <div class="card card-narrow">
        <h2 class="animal-title">{{ $animal->name }}</h2>
        <p class="animal-species">{{ $animal->species }} @if($animal->breed) ({{ $animal->breed }})@endif</p>

        @if($animal->photo_path)
            <div class="animal-photo-container">
                <img src="{{ asset(Storage::url($animal->photo_path)) }}"
                     alt="{{ $animal->name }}"
                     class="animal-photo">
            </div>
        @else
            <div class="no-photo-large">
                No image available.
            </div>
        @endif

        <div class="animal-description">
            {{ $animal->description }}
        </div>

        <div class="animal-meta">
            <p>Added: {{ $animal->created_at->format('M d, Y') }}</p>
            <p>Last Updated: {{ $animal->updated_at->format('M d, Y') }}</p>
            <p>Added by: {{ $animal->user->name }}</p>
        </div>

        <div class="flex-between mt-8">
            <a href="{{ route('animals.index') }}" class="btn btn-secondary">
                ← Back to List
            </a>
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('animals.edit', $animal) }}" class="btn btn-warning">
                        ✏️ Edit Animal
                    </a>
                @endif
            @endauth
        </div>
    </div>

    <div class="card card-narrow mt-8">
        <h3 class="section-title">Comments</h3>
        @if($animal->comments->count())
            <ul class="mb-6">
                @foreach($animal->comments as $comment)
                    <li class="mb-4">
                        <div class="text-small text-muted">
                            <strong>{{ $comment->user->name }}</strong> commented on {{ $comment->created_at->format('M d, Y H:i') }}:
                        </div>
                        <div class="comment-body">{{ $comment->body }}</div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted mb-6">No comments yet. Be the first to comment!</p>
        @endif

        @auth
            <form action="{{ route('comments.store', $animal) }}" method="POST" class="form-group">
                @csrf
                <textarea name="body" rows="3" class="form-textarea mb-2" placeholder="Add a comment..." required></textarea>
                @error('body')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </form>
        @else
            <p class="text-muted">You must be logged in to comment.</p>
        @endauth
    </div>
@endsection