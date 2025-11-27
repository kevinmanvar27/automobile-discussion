@extends('layouts.app')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Discussion Forum</h1>
        <!-- Trigger the modal with a button -->
        <button type="button" class="btn btn-primary" onclick="openThreadModal()">
            New Thread
        </button>
    </div>
    
    @if($threads->count() > 0)
        <ul class="thread-list">
            @foreach($threads as $thread)
                <li class="thread-item">
                    <a href="{{ route('threads.show', $thread) }}" style="text-decoration: none; color: inherit;">
                        <div class="thread-title">{{ $thread->subject }}</div>
                        <div class="thread-meta">
                            By {{ $thread->user->name }} | 
                            {{ $thread->created_at->format('M d, Y H:i') }}
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p>No threads yet. <button type="button" class="btn btn-link" onclick="openThreadModal()">Create the first thread</button>.</p>
    @endif
</div>

<script>
    // Ensure the modal functions are available
    if (typeof openThreadModal !== 'function') {
        // Fallback in case the global functions aren't available
        function openThreadModal() {
            document.getElementById('threadModal').style.display = 'block';
        }
    }
</script>
@endsection