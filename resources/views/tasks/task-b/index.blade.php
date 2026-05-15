@extends('layouts.app')

@section('title', 'Task B — Recommendation')

@section('content')
    @php
        $selectedScenario = old('scenario', $scenario ?? 'normal');
    @endphp

    <div class="space-y-8">
        <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700">Task B</span>
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Recommendation Agent</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600">
                        Describe a user and receive a ranked list of personalized recommendations. Refine the list in follow-up turns to simulate an AI assistant workflow.
                    </p>
                </div>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-3">
                @foreach([
                    'normal' => ['title' => 'Normal User', 'description' => 'Has review history', 'icon' => '👤'],
                    'cold_start' => ['title' => 'Cold Start', 'description' => 'Brand new user, no history', 'icon' => '❄️'],
                    'cross_domain' => ['title' => 'Cross Domain', 'description' => 'History in different category', 'icon' => '🔁'],
                ] as $key => $scenarioCard)
                    <button type="button" onclick="document.getElementById('scenario').value='{{ $key }}'" class="group flex flex-col gap-3 rounded-3xl border p-5 text-left transition focus:outline-none focus:ring-2 focus:ring-emerald-300 {{ $selectedScenario === $key ? 'border-emerald-500 bg-emerald-50 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-lg">{{ $scenarioCard['icon'] }}</span>
                        <div>
                            <div class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                <span>{{ $scenarioCard['title'] }}</span>
                                @if($selectedScenario === $key)
                                    <span class="text-emerald-600">✓</span>
                                @endif
                            </div>
                            <p class="mt-2 text-sm text-slate-500">{{ $scenarioCard['description'] }}</p>
                        </div>
                    </button>
                @endforeach
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Input</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">Describe the user</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">Persona prompt</span>
                </div>

                <form action="{{ route('task-b.recommend') }}" method="POST" class="mt-6 space-y-6">
                    @csrf
                    <input type="hidden" name="scenario" id="scenario" value="{{ $selectedScenario }}">

                    <div class="space-y-2">
                        <label for="persona_description" class="block text-sm font-medium text-slate-700">Describe the User Persona</label>
                        <textarea id="persona_description" name="persona_description" rows="5" placeholder="e.g. Chidi, 28, Lagos. Loves spicy local food, always complains about overpricing. Recently reviewed 3 restaurants. Wants something new to try tonight." class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-4 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"></textarea>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-2">
                        <div class="space-y-2">
                            <label for="domain" class="block text-sm font-medium text-slate-700">Domain / Category</label>
                            <input id="domain" name="domain" type="text" placeholder="e.g. Restaurants, Books, Movies" class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                        </div>
                        <div class="space-y-2">
                            <label for="location" class="block text-sm font-medium text-slate-700">Location Context</label>
                            <input id="location" name="location" type="text" placeholder="e.g. Lagos, Abuja, Port Harcourt" class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-3xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        Get Recommendations
                    </button>
                </form>
            </section>

            <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
                @if($recommendations ?? null)
                    <div class="space-y-6">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Results</p>
                                <h2 class="mt-3 text-2xl font-semibold text-slate-900">Top 10 Recommendations</h2>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">Ranked list</span>
                        </div>

                        <div class="space-y-4">
                            @foreach($recommendations as $index => $item)
                                @php
                                    $rank = $index + 1;
                                    if ($rank === 1) {
                                        $borderClass = 'border-amber-400 bg-amber-50';
                                        $dotClass = 'text-amber-700';
                                    } elseif ($rank === 2) {
                                        $borderClass = 'border-slate-400 bg-slate-100';
                                        $dotClass = 'text-slate-700';
                                    } elseif ($rank === 3) {
                                        $borderClass = 'border-orange-400 bg-orange-50';
                                        $dotClass = 'text-orange-700';
                                    } else {
                                        $borderClass = 'border-slate-200 bg-slate-50';
                                        $dotClass = 'text-slate-500';
                                    }
                                @endphp

                                <div class="flex gap-4 rounded-3xl border-l-4 px-5 py-4 {{ $borderClass }}">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-sm font-semibold shadow-sm {{ $dotClass }}">
                                        {{ $rank }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $item['name'] ?? 'Recommendation ' . $rank }}</p>
                                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['reason'] ?? 'No reason provided.' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="flex min-h-[280px] items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">
                        Your top recommendations will appear here after the first request.
                    </div>
                    <div class="mt-6 space-y-4">
                        @for($i = 0; $i < 3; $i++)
                            <div class="flex gap-4 rounded-3xl border px-5 py-4 bg-white shadow-sm animate-pulse">
                                <div class="h-10 w-10 rounded-full bg-slate-200"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="h-4 bg-slate-200 rounded"></div>
                                    <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                @endif
            </section>
        </div>

        @if(isset($recommendations))
            <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
                <h2 class="text-2xl font-semibold text-slate-900">Top 10 Recommendations for this User</h2>
                <div class="mt-6 space-y-4">
                    @foreach($recommendations as $index => $item)
                        @php
                            $rank = $index + 1;
                            if ($rank === 1) {
                                $circleBg = 'bg-amber-500';
                                $borderColor = 'border-l-amber-500';
                            } elseif ($rank === 2) {
                                $circleBg = 'bg-gray-400';
                                $borderColor = 'border-l-gray-400';
                            } elseif ($rank === 3) {
                                $circleBg = 'bg-amber-700';
                                $borderColor = 'border-l-amber-700';
                            } else {
                                $circleBg = 'bg-gray-700';
                                $borderColor = 'border-l-gray-300';
                            }
                        @endphp
                        <div class="flex gap-4 rounded-3xl border-l-4 {{ $borderColor }} bg-white px-5 py-4 shadow-sm">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $circleBg }} text-white font-bold text-sm">
                                {{ $rank }}
                            </div>
                            <div>
                                <p class="text-lg font-bold text-slate-900">{{ $item['name'] ?? 'Recommendation ' . $rank }}</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['reason'] ?? 'No reason provided.' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if($recommendations ?? null)
            <section class="bg-white border border-slate-200 rounded-3xl shadow-sm p-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-slate-500">Refinement</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">Refine These Recommendations</h2>
                        <p class="mt-2 text-sm text-slate-600">Not satisfied? Tell the agent to adjust. It remembers the full conversation.</p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">Multi-turn</span>
                </div>

                @if(!empty($history))
                    <div class="mt-6 space-y-4">
                        @foreach($history as $message)
                            @if(($message['role'] ?? '') === 'user')
                                <div class="flex justify-end">
                                    <div class="max-w-xl rounded-3xl bg-slate-100 px-4 py-3 text-sm text-slate-700 shadow-sm">
                                        {{ $message['message'] ?? '' }}
                                    </div>
                                </div>
                            @else
                                <div class="flex justify-start">
                                    <div class="max-w-xl rounded-3xl bg-emerald-50 px-4 py-3 text-sm text-slate-700 shadow-sm border border-emerald-100">
                                        {{ $message['message'] ?? '' }}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('task-b.refine') }}" method="POST" class="mt-6 space-y-4">
                    @csrf
                    <input type="hidden" name="scenario" value="{{ $selectedScenario }}">
                    <div class="space-y-2">
                        <label for="refinement" class="block text-sm font-medium text-slate-700">Refinement prompt</label>
                        <input id="refinement" name="refinement" type="text" placeholder="e.g. Remove expensive options, show me Nigerian content only, something calmer" class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm transition focus:border-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-100" />
                    </div>
                    <button type="submit" class="w-full rounded-3xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        Refine List
                    </button>
                </form>
            </section>
        @endif
    </div>
@endsection