@extends('layouts.app')

@section('content')
<div class="card">
    <h1>Create New Thread</h1>
    
    <form method="POST" action="{{ route('threads.store') }}">
        @csrf
        
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject') }}" required>
            @error('subject')
                <div class="alert alert-error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
            @error('description')
                <div class="alert alert-error">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-primary">Create Thread</button>
        <a href="{{ route('discussion.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection