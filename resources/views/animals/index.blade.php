@extends('layouts.app')

@section('title', 'Adoptable Animals')

@section('content')
    <div class="gallery-section mb-6">
        <h2 class="section-title">Adoptable Animals</h2>
        <div class="gallery-grid">
            @forelse($latestAnimals as $animal)
                <div class="gallery-item">
                    <a href="{{ route('animals.show', $animal) }}">
                        @if($animal->photo_path)
                            <img src="{{ asset(Storage::url($animal->photo_path)) }}" alt="{{ $animal->name }}" class="animal-thumb">
                        @else
                            <div class="no-photo">No Photo</div>
                        @endif
                        <div class="gallery-caption">{{ $animal->name }}</div>
                    </a>
                </div>
            @empty
                <p class="text-muted">No recent animals found.</p>
            @endforelse
        </div>
    </div>

    <div class="card">
        <h2 class="page-title">Adoptable Animals</h2>

        @if($animals->isEmpty())
            <div class="text-center p-8">
                <p class="text-muted mb-4">No animals found. Check back later or add one!</p>
                @auth
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('animals.create') }}" class="btn btn-primary">
                            Add New Animal
                        </a>
                    @endif
                @endauth
            </div>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th class="sortable-header">
                                <a href="{{ route('animals.index', ['sort' => 'name', 'direction' => Request::get('sort') == 'name' && Request::get('direction') == 'asc' ? 'desc' : 'asc']) }}" class="sort-link">
                                    Name
                                    @if(Request::get('sort') == 'name')
                                        @if(Request::get('direction') == 'asc')
                                            <span class="sort-arrow">↑</span>
                                        @else
                                            <span class="sort-arrow">↓</span>
                                        @endif
                                    @endif
                                </a>
                            </th>
                            <th class="sortable-header">
                                <a href="{{ route('animals.index', ['sort' => 'species', 'direction' => Request::get('sort') == 'species' && Request::get('direction') == 'asc' ? 'desc' : 'asc']) }}" class="sort-link">
                                    Species
                                    @if(Request::get('sort') == 'species')
                                        @if(Request::get('direction') == 'asc')
                                            <span class="sort-arrow">↑</span>
                                        @else
                                            <span class="sort-arrow">↓</span>
                                        @endif
                                    @endif
                                </a>
                            </th>
                            <th class="sortable-header">
                                <a href="{{ route('animals.index', ['sort' => 'created_at', 'direction' => Request::get('sort') == 'created_at' && Request::get('direction') == 'asc' ? 'desc' : 'asc']) }}" class="sort-link">
                                    Date Posted
                                    @if(Request::get('sort') == 'created_at')
                                        @if(Request::get('direction') == 'asc')
                                            <span class="sort-arrow">↑</span>
                                        @else
                                            <span class="sort-arrow">↓</span>
                                        @endif
                                    @endif
                                </a>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($animals as $animal)
                            <tr>
                                <td>
                                    @if($animal->photo_path)
                                        <img src="{{ asset(Storage::url($animal->photo_path)) }}" alt="{{ $animal->name }}" class="animal-thumb">
                                    @else
                                        <div class="no-photo">No Photo</div>
                                    @endif
                                </td>
                                <td class="font-semibold">{{ $animal->name }}</td>
                                <td>{{ $animal->species }}</td>
                                <td class="text-muted">{{ $animal->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('animals.show', $animal) }}" class="btn-link">View</a>
                                        @auth
                                            @if(Auth::user()->role === 'admin')
                                                <a href="{{ route('animals.edit', $animal) }}" class="btn-link">Edit</a>
                                            @endif
                                        @endauth
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @auth
                @if(Auth::user()->role === 'admin')
                    <div class="text-right mt-6">
                        <a href="{{ route('animals.create') }}" class="btn btn-primary">
                            Add New Animal
                        </a>
                    </div>
                @endif
            @endauth
        @endif
    </div>
@endsection

