<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NaijaReview AI') — DSN x BCT LLM Agent Challenge</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="min-h-screen flex flex-col">
        <header id="app-header" class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur-md transition-all duration-300">
            <div class="max-w-6xl mx-auto flex flex-col gap-3 px-6 py-5 transition-all duration-300 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-3xl font-semibold tracking-tight text-slate-900">NaijaReview AI</p>
                    <p id="app-header-subtitle" class="mt-1 text-sm text-slate-500 transition-opacity duration-300">DSN x BCT LLM Agent Challenge 3.0</p>
                </div>

                <nav class="flex items-center gap-6" aria-label="Primary navigation">
                    <a href="{{ route('task-a') }}" class="text-sm font-semibold text-slate-700 transition hover:text-slate-900 pb-1 {{ request()->routeIs('task-a') ? 'border-b-2 border-emerald-600 text-slate-900' : '' }}">
                        User Modeling
                    </a>
                    <a href="{{ route('task-b') }}" class="text-sm font-semibold text-slate-700 transition hover:text-slate-900 pb-1 {{ request()->routeIs('task-b') ? 'border-b-2 border-emerald-600 text-slate-900' : '' }}">
                        Recommendation
                    </a>
                </nav>
            </div>
        </header>

        <main class="flex-1 bg-slate-50 px-4 py-10">
            <div class="mx-auto w-full max-w-5xl rounded-3xl bg-white px-6 py-8 shadow-sm ring-1 ring-slate-200/80">
                @yield('content')
            </div>
        </main>

        <footer class="border-t border-slate-200 bg-white py-5">
            <div class="max-w-6xl mx-auto px-6 text-sm text-slate-500">
                Built for DSN x BCT Hackathon 3.0 · Powered by Laravel 13 + Mistral AI
            </div>
        </footer>

        <script>
            (function() {
                var header = document.getElementById('app-header');
                var subtitle = document.getElementById('app-header-subtitle');
                var content = header.querySelector('div');

                function updateHeader() {
                    if (window.scrollY > 24) {
                        header.classList.add('shadow-sm');
                        content.classList.add('py-3');
                        content.classList.remove('py-5');
                        if (subtitle) {
                            subtitle.classList.add('opacity-0');
                        }
                    } else {
                        header.classList.remove('shadow-sm');
                        content.classList.remove('py-3');
                        content.classList.add('py-5');
                        if (subtitle) {
                            subtitle.classList.remove('opacity-0');
                        }
                    }
                }

                window.addEventListener('scroll', updateHeader);
                updateHeader();
            })();
        </script>
    </div>
</body>
</html>
