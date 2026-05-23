@extends('layouts.app')

@section('title', 'Task B — Recommendation')@section('content')@php $selectedScenario = old('scenario', $scenario ?? 'normal'); @endphp<div style="display:flex;flex-direction:column;gap:1.5rem;" class="fade-up fade-up-1">

        {{-- ── Page Header + Scenario selector ── --}}
        <section class="card" style="background: linear-gradient(135deg, rgba(234,179,8,0.06) 0%, rgba(15,23,42,0.6) 100%); border-color: rgba(234,179,8,0.15); padding:2.25rem;">
            <div style="display:flex;flex-wrap:wrap;align-items:flex-start;gap:1.25rem;justify-content:space-between;margin-bottom:1.75rem;">
                <div style="flex:1;min-width:260px;">
                    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.875rem;">
                        <span class="badge badge-gold">
                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M5 1l1.2 2.4L9 4l-2 2 .5 2.8L5 7.5 2.5 8.8 3 6 1 4l2.8-.6L5 1z" fill="currentColor"/></svg>
                            Task B
                        </span>
                        <span class="badge badge-muted">Recommendation</span>
                    </div>
                    <h1 style="font-family:'Syne',sans-serif;font-size:1.875rem;font-weight:800;color:#fff;margin-bottom:0.75rem;line-height:1.2;">
                        Recommendation <span class="gradient-text">Agent</span>
                    </h1>
                    <p style="font-size:0.875rem;line-height:1.7;color:var(--text-muted);max-width:520px;">
                        Describe a user and receive a ranked list of personalised recommendations. Refine in follow-up turns to simulate an agentic recommendation workflow.
                    </p>
                </div>
                <div style="flex-shrink:0;display:flex;align-items:center;justify-content:center;width:72px;height:72px;border-radius:20px;background:rgba(234,179,8,0.08);border:1px solid rgba(234,179,8,0.18);">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                        <path d="M16 4l2.4 4.8L24 10l-4 4 1 5.6L16 17l-5 2.6L12 14l-4-4 5.6-1.2L16 4z" stroke="#FACC15" stroke-width="1.5" stroke-linejoin="round"/>
                        <circle cx="26" cy="24" r="4" fill="rgba(22,163,74,0.5)" stroke="#22C55E" stroke-width="1.5"/>
                        <path d="M24.5 24h3M26 22.5v3" stroke="#22C55E" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            <!-- Scenario Cards -->
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;" id="scenario-cards">
                @foreach ([
                        'normal' => ['title' => 'Normal User', 'description' => 'Has review history in the requested domain', 'icon' => '👤', 'color' => 'green'],
                        'cold_start' => ['title' => 'Cold Start', 'description' => 'Brand new user, no prior history', 'icon' => '❄️', 'color' => 'blue'],
                        'cross_domain' => ['title' => 'Cross Domain', 'description' => 'History in a different category — infer tastes', 'icon' => '🔁', 'color' => 'gold'],
                    ] as $key => $card)
                        <button type="button"
                            onclick="selectScenario('{{ $key }}')"
                            id="card-{{ $key }}"
                            class="scenario-card {{ $selectedScenario === $key ? 'selected' : '' }}">
                            <div class="scenario-icon">{{ $card['icon'] }}</div>
                            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.375rem;">
                                <span style="font-family:'Syne',sans-serif;font-size:0.9rem;font-weight:700;color:#fff;">{{ $card['title'] }}</span>
                                <svg id="check-{{ $key }}" width="14" height="14" viewBox="0 0 14 14" fill="none" style="{{ $selectedScenario === $key ? '' : 'display:none;' }}">
                                    <circle cx="7" cy="7" r="6" fill="rgba(22,163,74,0.3)"/>
                                    <path d="M4.5 7l2 2 3-3" stroke="#22C55E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <p style="font-size:0.78rem;color:var(--text-muted);line-height:1.5;">{{ $card['description'] }}</p>
                        </button>
                @endforeach
            </div>
        </section>

        {{-- ── Error alert ── --}}
        @if ($errors->any())
            <div class="alert-error fade-up fade-up-2">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- ── Input + Results grid ── --}}
        <div style="display:grid;grid-template-columns:1.25fr 0.75fr;gap:1.25rem;" class="fade-up fade-up-2" id="rec-grid">

            {{-- Input form --}}
            <section class="card">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:1.75rem;flex-wrap:wrap;">
                    <div>
                        <div class="section-eyebrow">Input</div>
                        <h2 style="font-family:'Syne',sans-serif;font-size:1.375rem;font-weight:700;color:#fff;">Describe the user</h2>
                    </div>
                    <span class="badge badge-muted">Persona prompt</span>
                </div>

                <form action="{{ route('task-b.recommend') }}" method="POST" id="rec-form" style="display:flex;flex-direction:column;gap:1.25rem;">
                    @csrf
                    <input type="hidden" name="scenario" id="scenario-input" value="{{ $selectedScenario }}">

                    <div>
                        <label for="persona_description" class="field-label">Describe the User Persona</label>
                        <textarea id="persona_description" name="persona_description" rows="5"
                            placeholder="e.g. Chidi, 28, Lagos. Loves spicy local food, always complains about overpricing. Recently reviewed 3 restaurants. Wants something new to try tonight."
                            class="textarea-field" style="resize:vertical;">{{ old('persona_description') }}</textarea>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;" id="input-pair">
                        <div>
                            <label for="domain" class="field-label">Domain / Category</label>
                            <input id="domain" name="domain" type="text"
                                value="{{ old('domain') }}"
                                placeholder="e.g. Restaurants, Movies"
                                class="input-field" />
                        </div>
                        <div>
                            <label for="location" class="field-label">Location Context</label>
                            <input id="location" name="location" type="text"
                                value="{{ old('location') }}"
                                placeholder="e.g. Lagos, Abuja"
                                class="input-field" />
                        </div>
                    </div>

                    <button type="submit" id="rec-btn" class="btn-primary" style="width:100%;">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" id="rec-icon"><path d="M7 1l1.6 3.2L12 5l-2.7 2.6.7 3.8L7 9.8l-3 1.6.7-3.8L2 5l3.4-.8L7 1z" fill="currentColor" opacity="0.8"/></svg>
                        <span id="rec-label">Get Recommendations</span>
                        <span id="rec-loading" style="display:none;align-items:center;gap:0.375rem;">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="animation:spin 1s linear infinite"><circle cx="7" cy="7" r="5.5" stroke="rgba(255,255,255,0.25)" stroke-width="1.5"/><path d="M7 1.5a5.5 5.5 0 0 1 5.5 5.5" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg>
                            Thinking...
                        </span>
                    </button>
                </form>
            </section>

            {{-- Results panel --}}
            <section class="card" style="overflow:hidden;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
                    <div>
                        <div class="section-eyebrow">Results</div>
                        <h2 style="font-family:'Syne',sans-serif;font-size:1.375rem;font-weight:700;color:#fff;">
                            @if ($recommendations ?? null)
                                Top 10 Picks
                            @else
                                Awaiting input
                            @endif
                        </h2>
                    </div>
                    <span class="badge badge-muted">Ranked list</span>
                </div>

                @if ($recommendations ?? null)
                    <div style="display:flex;flex-direction:column;gap:0.5rem;max-height:520px;overflow-y:auto;padding-right:0.25rem;">
                        @foreach ($recommendations as $index => $item)
                            @php
                                $rank = $index + 1;
                                $circleStyle = match (true) {
                                    $rank === 1 => 'background:rgba(234,179,8,0.9); color:#000;',
                                    $rank === 2 => 'background:rgba(148,163,184,0.7); color:#000;',
                                    $rank === 3 => 'background:rgba(234,88,12,0.8); color:#fff;',
                                    default => 'background:rgba(255,255,255,0.06); color:var(--text-muted);',
                                };
                                $borderColor = match (true) {
                                    $rank === 1 => 'rgba(234,179,8,0.6)',
                                    $rank === 2 => 'rgba(148,163,184,0.4)',
                                    $rank === 3 => 'rgba(234,88,12,0.5)',
                                    default => 'rgba(255,255,255,0.05)',
                                };
                            @endphp
                            <div class="rec-card" style="border-left-color: {{ $borderColor }};">
                                <div class="rank-circle" style="{{ $circleStyle }}">{{ $rank }}</div>
                                <div style="flex:1;min-width:0;">
                                    <p style="font-family:'Syne',sans-serif;font-size:0.825rem;font-weight:700;color:#fff;margin-bottom:0.25rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $item['name'] ?? 'Item ' . $rank }}
                                    </p>
                                    <p style="font-size:0.75rem;line-height:1.5;color:var(--text-muted);">{{ $item['reason'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <path d="M20 4l3.2 6.4L30 12l-5.4 5.2 1.4 7.6-6-3.2-6 3.2 1.4-7.6L10 12l6.8-1.6L20 4z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                            <path d="M10 28h20M14 33h12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <div>
                            <p style="font-family:'Syne',sans-serif;font-weight:600;color:rgba(255,255,255,0.4);margin-bottom:0.25rem;">No recommendations yet</p>
                            <p style="color:var(--text-muted);font-size:0.8rem;">Fill in the form and hit Get Recommendations</p>
                        </div>
                    </div>
                @endif
            </section>
        </div>

        {{-- ── Multi-turn refinement ── --}}
        @if ($recommendations ?? null)
            <section class="card fade-up fade-up-3" style="border-color:rgba(22,163,74,0.2);">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:1.75rem;flex-wrap:wrap;">
                    <div>
                        <div class="section-eyebrow">Refinement</div>
                        <h2 style="font-family:'Syne',sans-serif;font-size:1.375rem;font-weight:700;color:#fff;">Refine These Recommendations</h2>
                        <p style="font-size:0.825rem;color:var(--text-muted);margin-top:0.375rem;">The agent remembers the full conversation — ask it to adjust, narrow, or expand the list.</p>
                    </div>
                    <span class="badge badge-green">
                        <svg width="8" height="8" viewBox="0 0 8 8" fill="none"><circle cx="4" cy="4" r="3" stroke="currentColor" stroke-width="1.2"/><path d="M4 2v2.5L5.5 6" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                        Multi-turn
                    </span>
                </div>

                {{-- Conversation history --}}
                @if (!empty($history))
                    <div style="display:flex;flex-direction:column;gap:0.75rem;max-height:280px;overflow-y:auto;background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.05);border-radius:14px;padding:1rem;margin-bottom:1.25rem;">
                        @foreach ($history as $message)
                            @if (($message['role'] ?? '') === 'user')
                                <div style="display:flex;justify-content:flex-end;">
                                    <div class="chat-user">{{ $message['message'] ?? '' }}</div>
                                </div>
                            @else
                                <div style="display:flex;justify-content:flex-start;">
                                    <div class="chat-ai">{{ $message['message'] ?? '' }}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('task-b.refine') }}" method="POST" id="refine-form" style="display:flex;gap:0.75rem;align-items:stretch;">
                    @csrf
                    <input type="hidden" name="scenario" value="{{ $selectedScenario }}">
                    <input id="refinement" name="refinement" type="text"
                        placeholder="e.g. Remove expensive options · Nigerian content only · Something calmer"
                        class="input-field" style="flex:1;" />
                    <button type="submit" id="refine-btn" class="btn-primary" style="padding:0.75rem 1.25rem;flex-shrink:0;">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" id="refine-icon"><path d="M2 7h10M8 3l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span id="refine-label">Refine</span>
                        <span id="refine-loading" style="display:none;">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" style="animation:spin 1s linear infinite"><circle cx="6" cy="6" r="4.5" stroke="rgba(255,255,255,0.25)" stroke-width="1.5"/><path d="M6 1.5a4.5 4.5 0 0 1 4.5 4.5" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </span>
                    </button>
                </form>
            </section>
        @endif

    </div>

    <style>
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @media (max-width: 900px) {
            #rec-grid      { grid-template-columns: 1fr !important; }
            #scenario-cards { grid-template-columns: 1fr !important; }
        }
        @media (max-width: 640px) {
            #input-pair { grid-template-columns: 1fr !important; }
        }
    </style>

    <script>
        function selectScenario(key) {
            var keys = ['normal', 'cold_start', 'cross_domain'];
            document.getElementById('scenario-input').value = key;

            keys.forEach(function (k) {
                var card  = document.getElementById('card-' + k);
                var check = document.getElementById('check-' + k);
                if (k === key) {
                    card.classList.add('selected');
                    check.style.display = '';
                } else {
                    card.classList.remove('selected');
                    check.style.display = 'none';
                }
            });
        }

        var recForm = document.getElementById('rec-form');
        if (recForm) {
            recForm.addEventListener('submit', function () {
                var btn     = document.getElementById('rec-btn');
                var label   = document.getElementById('rec-label');
                var loading = document.getElementById('rec-loading');
                var icon    = document.getElementById('rec-icon');
                btn.disabled = true;
                label.style.display   = 'none';
                icon.style.display    = 'none';
                loading.style.display = 'flex';
            });
        }

        var refineForm = document.getElementById('refine-form');
        if (refineForm) {
            refineForm.addEventListener('submit', function () {
                var btn     = document.getElementById('refine-btn');
                var label   = document.getElementById('refine-label');
                var loading = document.getElementById('refine-loading');
                var icon    = document.getElementById('refine-icon');
                btn.disabled = true;
                label.style.display   = 'none';
                icon.style.display    = 'none';
                loading.style.display = '';
            });
        }
    </script>
@endsection