@extends('layouts.app')

@section('title', 'Task B — Recommendation')

@section('content')
    @php $selectedScenario = old('scenario', $scenario ?? 'normal'); @endphp

    <div class="space-y-8">

        {{-- Header + Scenario selector --}}
        <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700">Task B</span>
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Recommendation Agent</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                        Describe a user and receive a ranked list of personalised recommendations. Refine the list in follow-up turns to simulate an agentic recommendation workflow.
                    </p>
                </div>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-3" id="scenario-cards">
                @foreach ([
                    'normal'       => ['title' => 'Normal User',   'description' => 'Has review history in the requested domain',     'icon' => '👤'],
                    'cold_start'   => ['title' => 'Cold Start',    'description' => 'Brand new user, no prior history',               'icon' => '❄️'],
                    'cross_domain' => ['title' => 'Cross Domain',  'description' => 'History in a different category — infer tastes', 'icon' => '🔁'],
                ] as $key => $card)
                    <button type="button"
                        onclick="selectScenario('{{ $key }}')"
                        id="card-{{ $key }}"
                        class="group flex flex-col gap-3 rounded-3xl border p-5 text-left transition focus:outline-none focus:ring-2 focus:ring-emerald-300 {{ $selectedScenario === $key ? 'border-emerald-500 bg-emerald-50 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-lg">{{ $card['icon'] }}</span>
                        <div>
                            <div class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                <span>{{ $card['title'] }}</span>
                                <span id="check-{{ $key }}" class="{{ $selectedScenario === $key ? '' : 'hidden' }} text-emerald-600">✓</span>
                            </div>
                            <p class="mt-2 text-sm text-slate-500">{{ $card['description'] }}</p>
                        </div>
                    </button>
                @endforeach
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

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">

            {{-- Input form --}}
            <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Input</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">Describe the user</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">Persona prompt</span>
                </div>

                <form action="{{ route('task-b.recommend') }}" method="POST" class="space-y-6" id="rec-form">
                    @csrf
                    <input type="hidden" name="scenario" id="scenario-input" value="{{ $selectedScenario }}">

                    <div class="space-y-2">
                        <label for="persona_description" class="block text-sm font-medium text-slate-700">Describe the User Persona</label>
                        <textarea id="persona_description" name="persona_description" rows="5"
                            placeholder="e.g. Chidi, 28, Lagos. Loves spicy local food, always complains about overpricing. Recently reviewed 3 restaurants. Wants something new to try tonight."
                            class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-4 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100">{{ old('persona_description') }}</textarea>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-2">
                        <div class="space-y-2">
                            <label for="domain" class="block text-sm font-medium text-slate-700">Domain / Category</label>
                            <input id="domain" name="domain" type="text"
                                value="{{ old('domain') }}"
                                placeholder="e.g. Restaurants, Books, Movies"
                                class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                        </div>
                        <div class="space-y-2">
                            <label for="location" class="block text-sm font-medium text-slate-700">Location Context</label>
                            <input id="location" name="location" type="text"
                                value="{{ old('location') }}"
                                placeholder="e.g. Lagos, Abuja, Port Harcourt"
                                class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                        </div>
                    </div>

                    <button type="submit" id="rec-btn"
                        class="w-full rounded-3xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-60">
                        <span id="rec-label">Get Recommendations</span>
                        <span id="rec-loading" class="hidden">Thinking...</span>
                    </button>
                </form>
            </section>

            {{-- Results panel --}}
            <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
                @if ($recommendations ?? null)
                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-4 mb-2">
                            <div>
                                <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Results</p>
                                <h2 class="mt-2 text-xl font-semibold text-slate-900">Top 10 Recommendations</h2>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">Ranked list</span>
                        </div>

                        @foreach ($recommendations as $index => $item)
                            @php
                                $rank = $index + 1;
                                $styles = match (true) {
                                    $rank === 1 => ['border' => 'border-amber-400',  'bg' => 'bg-amber-50',   'circle' => 'bg-amber-500 text-white'],
                                    $rank === 2 => ['border' => 'border-slate-400',  'bg' => 'bg-slate-100',  'circle' => 'bg-slate-500 text-white'],
                                    $rank === 3 => ['border' => 'border-orange-400', 'bg' => 'bg-orange-50',  'circle' => 'bg-orange-600 text-white'],
                                    default     => ['border' => 'border-slate-200',  'bg' => 'bg-white',      'circle' => 'bg-slate-200 text-slate-700'],
                                };
                            @endphp
                            <div class="flex gap-3 rounded-2xl border-l-4 px-4 py-3 {{ $styles['border'] }} {{ $styles['bg'] }}">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $styles['circle'] }}">
                                    {{ $rank }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $item['name'] ?? 'Item ' . $rank }}</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-600">{{ $item['reason'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex min-h-[300px] items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">
                        Your personalised recommendations will appear here
                    </div>
                @endif
            </section>
        </div>

        {{-- Multi-turn refinement --}}
        @if ($recommendations ?? null)
            <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Refinement</p>
                        <h2 class="mt-2 text-2xl font-semibold text-slate-900">Refine These Recommendations</h2>
                        <p class="mt-1 text-sm text-slate-500">The agent remembers the full conversation — ask it to adjust, narrow, or expand the list.</p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">Multi-turn</span>
                </div>

                {{-- Conversation history --}}
                @if (!empty($history))
                    <div class="mb-6 space-y-3 max-h-64 overflow-y-auto rounded-3xl border border-slate-100 bg-slate-50 p-4">
                        @foreach ($history as $message)
                            @if (($message['role'] ?? '') === 'user')
                                <div class="flex justify-end">
                                    <div class="max-w-lg rounded-2xl bg-emerald-700 px-4 py-3 text-sm text-white shadow-sm">
                                        {{ $message['message'] ?? '' }}
                                    </div>
                                </div>
                            @else
                                <div class="flex justify-start">
                                    <div class="max-w-lg rounded-2xl bg-white px-4 py-3 text-sm text-slate-700 shadow-sm border border-slate-200">
                                        {{ $message['message'] ?? '' }}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('task-b.refine') }}" method="POST" class="flex gap-3" id="refine-form">
                    @csrf
                    <input type="hidden" name="scenario" value="{{ $selectedScenario }}">
                    <input id="refinement" name="refinement" type="text"
                        placeholder="e.g. Remove expensive options · Nigerian content only · Something calmer"
                        class="flex-1 rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                    <button type="submit" id="refine-btn"
                        class="rounded-3xl bg-emerald-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800 disabled:opacity-60">
                        <span id="refine-label">Refine</span>
                        <span id="refine-loading" class="hidden">...</span>
                    </button>
                </form>
            </section>
        @endif

    </div>

    <script>
        function selectScenario(key) {
            var keys = ['normal', 'cold_start', 'cross_domain'];
            document.getElementById('scenario-input').value = key;
            keys.forEach(function (k) {
                var card  = document.getElementById('card-' + k);
                var check = document.getElementById('check-' + k);
                if (k === key) {
                    card.classList.add('border-emerald-500', 'bg-emerald-50', 'shadow-sm');
                    card.classList.remove('border-slate-200', 'bg-white');
                    check.classList.remove('hidden');
                } else {
                    card.classList.remove('border-emerald-500', 'bg-emerald-50', 'shadow-sm');
                    card.classList.add('border-slate-200', 'bg-white');
                    check.classList.add('hidden');
                }
            });
        }

        var recForm = document.getElementById('rec-form');
        if (recForm) {
            recForm.addEventListener('submit', function () {
                var btn     = document.getElementById('rec-btn');
                var label   = document.getElementById('rec-label');
                var loading = document.getElementById('rec-loading');
                btn.disabled = true;
                label.classList.add('hidden');
                loading.classList.remove('hidden');
            });
        }

        var refineForm = document.getElementById('refine-form');
        if (refineForm) {
            refineForm.addEventListener('submit', function () {
                var btn     = document.getElementById('refine-btn');
                var label   = document.getElementById('refine-label');
                var loading = document.getElementById('refine-loading');
                btn.disabled = true;
                label.classList.add('hidden');
                loading.classList.remove('hidden');
            });
        }
    </script>
@endsection
