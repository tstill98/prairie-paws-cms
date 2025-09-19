@extends('layouts.app')

@section('title', 'Add New Animal')

@section('content')
    <div class="card card-narrow">
        <h2 class="page-title">Add New Animal</h2>
        <form action="{{ route('animals.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Name:</label>
                <input type="text" name="name" id="name" required class="form-input">
                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="species" class="form-label">Species:</label>
                <input type="text" name="species" id="species" required class="form-input">
                @error('species')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="breed" class="form-label">Breed (Optional):</label>
                <input type="text" name="breed" id="breed" class="form-input">
                @error('breed')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description:</label>
                <textarea name="description" id="description" rows="5" required class="form-textarea"></textarea>
                @error('description')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group-lg">
                <label for="photo" class="form-label">Photo (Optional):</label>
                <input type="file" name="photo" id="photo" accept="image/*" class="form-file">
                @error('photo')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex-between">
                <button type="submit" class="btn btn-primary">
                    Create Animal
                </button>
                <a href="{{ route('animals.index') }}" class="btn-link">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection