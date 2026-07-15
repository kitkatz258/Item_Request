<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Item Request' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/dashboard">Item Request</a>
            <div class="ms-auto d-flex align-items-center">
                @if(auth()->user()->role === 'user')
                    <a href="{{ route('requester.items') }}" class="text-white me-3">Request Items</a>
                    <a href="{{ route('requester.my-requests') }}" class="text-white me-3">My Requests</a>
                @endif
                <span class="text-white me-3">{{ auth()->user()->name }}</span>
                <form method="POST" action="/logout" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        {{ $slot }}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>