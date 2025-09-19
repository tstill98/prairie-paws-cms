@extends('layouts.app')

@section('title', 'Edit Animal: ' . $animal->name)

@section('content')
    <div class="card card-narrow">
        <h2 class="page-title">Edit Animal: {{ $animal->name }}</h2>
        <form action="{{ route('animals.update', $animal) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name" class="form-label">Name:</label>
                <input type="text" name="name" id="name" value="{{ old('name', $animal->name) }}" required class="form-input">
                @error('name')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="species" class="form-label">Species:</label>
                <input type="text" name="species" id="species" value="{{ old('species', $animal->species) }}" required class="form-input">
                @error('species')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="breed" class="form-label">Breed (Optional):</label>
                <input type="text" name="breed" id="breed" value="{{ old('breed', $animal->breed) }}" class="form-input">
                @error('breed')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description:</label>
                <textarea name="description" id="description" rows="5" required class="form-textarea">{{ old('description', $animal->description) }}</textarea>
                @error('description')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group-lg">
                <label for="photo" class="form-label">Current Photo:</label>
                @if($animal->photo_path)
                    <img src="{{ asset(Storage::url($animal->photo_path)) }}" alt="{{ $animal->name }}" class="animal-photo">
                    <div class="space-bottom">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remove_photo" value="1" class="form-checkbox">
                            <span class="checkbox-text">Remove current photo</span>
                        </label>
                    </div>
                @else
                    <p class="text-gray text-small space-bottom">No photo uploaded.</p>
                @endif
                <label for="new_photo" class="form-label">New Photo (Optional):</label>
                <input type="file" name="photo" id="new_photo" accept="image/*" class="form-file">
                @error('photo')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex-between">
                <button type="submit" class="btn btn-primary">
                    Update Animal
                </button>
                <a href="{{ route('animals.index') }}" class="btn-link">
                    Cancel
                </a>
            </div>
        </form>

        <div class="delete-section">
            <form action="{{ route('animals.destroy', $animal) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to delete {{ $animal->name }}? This action cannot be undone.')">
                    Delete Animal
                </button>
            </form>
        </div>
    </div>
@endsection