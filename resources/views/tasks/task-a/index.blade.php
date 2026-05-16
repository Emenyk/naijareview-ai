@extends('layouts.app')

@section('title', 'Task A — User Modeling')

@section('content')
    <div class="space-y-8">
        <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700">Task A</span>
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">User Modeling Agent</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                        Select a Yelp persona and enter a product or business. The agent generates a review and star rating that matches the user's voice, tone, and behavior.
                    </p>
                </div>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
           <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
                <form method="POST" action="{{ route('task-a.generate') }}">
                @csrf
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Input</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">Build a review prompt</h2>
                    </div>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">Yelp persona</span>
                    <div class="space-y-2">
                        <label for="persona" class="block text-sm font-medium text-slate-700">Select User Persona</label>
                        <select name="user_id" class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                            @foreach($sampleUsers as $user)
                                <option 
                                    value="{{ $user['user_id'] }}"
                                    {{ isset($selectedUser) && $selectedUser === $user['user_id'] ? 'selected' : '' }}
                                >
                                    {{ $user['personality'] ?? 'User' }} · 
                                    Avg {{ $user['avg_rating'] }} stars · 
                                    {{ $user['review_count'] }} reviews
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="product" class="block text-sm font-medium text-slate-700">Product / Business to Review</label>
                        <input id="product" name="product" type="text" placeholder="e.g. Mama Titi's Kitchen, Lagos" class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    </div>

                    <button type="submit" class="w-full rounded-3xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                        Generate Review
                    </button>
                </div>
                </form>
            </section>

            <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Generated Output</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">Review result</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">Mistral AI</span>
                </div>

                @if($result ?? null)
                    <div class="mt-6 space-y-5">
                        <div class="flex flex-wrap items-center gap-3 text-sm font-semibold">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="text-2xl {{ $i <= intval($result['rating'] ?? 0) ? 'text-amber-500' : 'text-slate-300' }}">{{ $i <= intval($result['rating'] ?? 0) ? '★' : '☆' }}</span>
                            @endfor
                            <span class="text-slate-500 ml-2">{{ $result['rating'] ?? '0' }} out of 5</span>
                        </div>

                        <div class="rounded-3xl border-l-4 border-slate-300 bg-slate-50 p-6">
                            <p class="italic text-slate-700 leading-7">{{ $result['review'] ?? 'No review generated yet.' }}</p>
                        </div>

                        <p class="text-xs uppercase tracking-[0.22em] text-slate-500">Generated by Mistral AI · Behavioural simulation</p>
                    </div>
                @else
                    <div class="mt-6 flex min-h-[260px] items-center justify-center rounded-3xl border-2 border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">
                        Your generated review will appear here
                    </div>
                    <p class="text-xs text-slate-400 mt-2">Enter a product name and select a persona to generate a review</p>
                @endif
            </section>
        </div>

        @if($persona ?? null)
            <section class="space-y-6 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">User Persona Analyzed</p>
                        <h2 class="mt-2 text-2xl font-semibold text-slate-900">User Persona Analyzed</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">Review profile</span>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-3xl border border-slate-200 bg-emerald-50 p-5">
                        <p class="text-sm text-slate-500">Average Rating</p>
                        <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $persona['avg_rating'] }}</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-emerald-50 p-5">
                        <p class="text-sm text-slate-500">Total Reviews</p>
                        <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $persona['review_count'] }}</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-emerald-50 p-5">
                        <p class="text-sm text-slate-500">Writing Style</p>
                        <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $persona['style'] }}</p>
                    </div>
                </div>

                @if(!empty($persona['samples']))
                    <h4 class="text-md font-semibold text-slate-800 mb-3">Sample Reviews from this User</h4>
                    <div class="grid gap-4 md:grid-cols-3">
                        @foreach(array_slice($persona['samples'], 0, 3) as $sample)
                            <div class="rounded-3xl border border-slate-200 bg-emerald-50 p-4 flex flex-col gap-2">
                                <div class="flex items-center justify-between">
                                    <h5 class="font-semibold text-slate-800">
                                        Review {{ $loop->iteration }}
                                    </h5>
                                    <span class="text-amber-500 text-sm font-medium">
                                        {{ $sample['stars'] }} ★
                                    </span>
                                </div>
                                <div class="h-28 overflow-y-auto pr-1 scrollbar-thin">
                                    <p class="text-slate-600 text-sm leading-6">
                                        {{ $sample['text'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        @endif
    </div>

@endsection