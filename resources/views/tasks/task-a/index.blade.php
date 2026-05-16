@extends('layouts.app')

@section('title', 'Task A — User Modeling')

@section('content')
    <div class="space-y-8">

        {{-- Header --}}
        <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700">Task A</span>
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">User Modeling Agent</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                        Select a Yelp persona and enter a product or business. The agent generates a review and star rating that matches the user's voice, tone, and behavioural pattern.
                    </p>
                </div>
            </div>
        </section>

        {{-- Error alert --}}
        @if ($errors->any())
            <div class="rounded-3xl border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">

            {{-- Input panel --}}
            <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Input</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">Build a review prompt</h2>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">Yelp persona</span>
                </div>

                <form action="{{ route('task-a.generate') }}" method="POST" class="space-y-5" id="task-a-form">
                    @csrf

                    <div class="space-y-2">
                        <label for="persona_id" class="block text-sm font-medium text-slate-700">Select User Persona</label>
                        <select id="persona_id" name="persona_id"
                            class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                            @foreach ($personas as $p)
                                <option value="{{ $p['id'] }}" {{ old('persona_id', $persona['id'] ?? '') === $p['id'] ? 'selected' : '' }}>
                                    {{ $p['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="product" class="block text-sm font-medium text-slate-700">Product / Business to Review</label>
                        <input id="product" name="product" type="text"
                            value="{{ old('product', $product ?? '') }}"
                            placeholder="e.g. Mama Titi's Kitchen, Lagos"
                            class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    </div>

                    <button type="submit" id="generate-btn"
                        class="w-full rounded-3xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-300 disabled:opacity-60">
                        <span id="btn-label">Generate Review</span>
                        <span id="btn-loading" class="hidden">Generating...</span>
                    </button>
                </form>
            </section>

            {{-- Output panel --}}
            <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Generated Output</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">Review result</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">Mistral AI</span>
                </div>

                @if ($result ?? null)
                    <div class="mt-6 space-y-5">
                        <div class="flex flex-wrap items-center gap-1 text-2xl">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= intval($result['rating'] ?? 0) ? 'text-amber-500' : 'text-slate-300' }}">
                                    {{ $i <= intval($result['rating'] ?? 0) ? '★' : '☆' }}
                                </span>
                            @endfor
                            <span class="ml-2 text-sm font-semibold text-slate-500">{{ $result['rating'] ?? 0 }} / 5</span>
                        </div>

                        <div class="rounded-3xl border-l-4 border-emerald-400 bg-slate-50 p-6">
                            <p class="italic text-slate-700 leading-7">{{ $result['review'] ?? 'No review generated.' }}</p>
                        </div>

                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Generated by Mistral AI · Behavioural simulation</p>
                    </div>
                @else
                    <div class="mt-6 flex min-h-[260px] items-center justify-center rounded-3xl border-2 border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">
                        Your generated review will appear here
                    </div>
                    <p class="text-xs text-slate-400 mt-2">Select a persona and enter a product name to generate</p>
                @endif
            </section>
        </div>

        {{-- Persona Analysis panel --}}
        @if ($persona ?? null)
            <section class="space-y-6 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Selected Persona</p>
                        <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ $persona['name'] }}</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">Review profile</span>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-3xl border border-slate-200 bg-emerald-50 p-5">
                        <p class="text-xs uppercase tracking-widest text-slate-500">Average Rating</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $persona['avg_rating'] }}★</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-emerald-50 p-5">
                        <p class="text-xs uppercase tracking-widest text-slate-500">Total Reviews</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $persona['review_count'] }}</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-emerald-50 p-5">
                        <p class="text-xs uppercase tracking-widest text-slate-500">Writing Style</p>
                        <p class="mt-3 text-base font-semibold text-slate-900">{{ Str::limit($persona['style'], 50) }}</p>
                    </div>
                </div>

                @if (!empty($persona['reviews']))
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-widest text-slate-500 mb-4">Sample Reviews from this Persona</h4>
                        <div class="grid gap-4 md:grid-cols-3">
                            @foreach (array_slice($persona['reviews'], 0, 3) as $sample)
                                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                                    <div class="flex items-center gap-2 mb-3">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="text-sm {{ $i <= $sample['rating'] ? 'text-amber-500' : 'text-slate-300' }}">
                                                {{ $i <= $sample['rating'] ? '★' : '☆' }}
                                            </span>
                                        @endfor
                                    </div>
                                    <p class="text-slate-600 text-sm leading-6">{{ Str::limit($sample['text'], 160) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        @endif
    </div>

    <script>
        document.getElementById('task-a-form').addEventListener('submit', function () {
            var btn = document.getElementById('generate-btn');
            var label = document.getElementById('btn-label');
            var loading = document.getElementById('btn-loading');
            btn.disabled = true;
            label.classList.add('hidden');
            loading.classList.remove('hidden');
        });
    </script>
@endsection
