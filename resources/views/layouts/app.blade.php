<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NaijaReview AI — DSN x BCT Hackathon</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-6xl mx-auto flex flex-col gap-4 px-6 py-6 md:flex-row md:items-center md:justify-between">
            <div>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 text-slate-900">
                    <span class="text-2xl font-semibold">NaijaReview AI</span>
                </a>
                <p class="mt-2 max-w-2xl text-sm text-slate-500">
                    A clean, professional interface for Task A and Task B workflows in the DSN x BCT Hackathon.
                </p>
            </div>

            <nav class="flex flex-wrap items-center gap-3">
                <a href="{{ route('task-a') }}" class="rounded-full px-4 py-2 text-sm font-semibold transition shadow-sm {{ request()->routeIs('task-a') ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Task A
                </a>
                <a href="{{ route('task-b') }}" class="rounded-full px-4 py-2 text-sm font-semibold transition shadow-sm {{ request()->routeIs('task-b') ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Task B
                </a>
            </nav>
        </div>
    </header>

    <main class="max-w-5xl mx-auto py-10 px-4">
        @yield('content')
    </main>
</body>
</html>