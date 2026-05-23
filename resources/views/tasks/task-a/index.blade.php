@extends('layouts.app')

@section('title', 'Task A — User Modeling')@section('content')<div class="space-y-6 fade-up fade-up-1">

        {{-- ── Page Header ── --}}
        <section class="card" style="background: linear-gradient(135deg, rgba(22,163,74,0.08) 0%, rgba(15,23,42,0.6) 100%); border-color: rgba(22,163,74,0.2); padding: 2.25rem;">
            <div style="display:flex; flex-wrap:wrap; align-items:flex-start; gap:1.25rem; justify-content:space-between;">
                <div style="flex:1; min-width:260px;">
                    <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.875rem;">
                        <span class="badge badge-green">
                            <!-- icon -->
                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><circle cx="5" cy="3" r="2" fill="currentColor"/><path d="M1 9c0-2.2 1.8-4 4-4s4 1.8 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            Task A
                        </span>
                        <span class="badge badge-muted">User Modeling</span>
                    </div>
                    <h1 style="font-family:'Syne',sans-serif; font-size:1.875rem; font-weight:800; color:#fff; margin-bottom:0.75rem; line-height:1.2;">
                        User <span class="gradient-text">Modeling</span> Agent
                    </h1>
                    <p style="font-size:0.875rem; line-height:1.7; color:var(--text-muted); max-width:520px;">
                        Select a Yelp persona and enter a product or business. The agent generates a review and star rating that matches the user's voice, tone, and behavioural pattern.
                    </p>
                </div>

                <!-- Decorative icon -->
                <div style="flex-shrink:0; display:flex; align-items:center; justify-content:center; width:72px; height:72px; border-radius:20px; background:rgba(22,163,74,0.1); border:1px solid rgba(22,163,74,0.2);">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 26l4-12 6 6 5-8 4 6 5-10" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="24" cy="8" r="3" fill="rgba(234,179,8,0.8)"/>
                    </svg>
                </div>
            </div>
        </section>

        {{-- ── Error alert ── --}}
        @if ($errors->any())
            <div class="alert-error fade-up fade-up-2">
                @foreach ($errors->all() as $error)
                    <p style="display:flex;align-items:center;gap:0.5rem;">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="6" stroke="#fca5a5" stroke-width="1.5"/><path d="M7 4v3M7 9.5v.5" stroke="#fca5a5" stroke-width="1.5" stroke-linecap="round"/></svg>
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif

        {{-- ── Two-column grid ── --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;" class="fade-up fade-up-2" id="main-grid">

            {{-- Input panel --}}
            <section class="card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:1.75rem;">
                    <div>
                        <div class="section-eyebrow">Input</div>
                        <h2 style="font-family:'Syne',sans-serif; font-size:1.375rem; font-weight:700; color:#fff;">Build a review prompt</h2>
                    </div>
                    <span class="badge badge-muted">Yelp persona</span>
                </div>

                <form action="{{ route('task-a.generate') }}" method="POST" class="space-y-5" id="task-a-form" style="display:flex;flex-direction:column;gap:1.25rem;">
                    @csrf

                    <div>
                        <label for="persona_id" class="field-label">Select User Persona</label>
                        <select id="persona_id" name="persona_id" class="select-field">
                            @foreach ($personas as $p)
                                <option value="{{ $p['id'] }}" {{ old('persona_id', $persona['id'] ?? '') === $p['id'] ? 'selected' : '' }}>
                                    {{ $p['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="product" class="field-label">Product / Business to Review</label>
                        <input id="product" name="product" type="text"
                            value="{{ old('product', $product ?? '') }}"
                            placeholder="e.g. Mama Titi's Kitchen, Lagos"
                            class="input-field" />
                    </div>

                    <button type="submit" id="generate-btn" class="btn-primary" style="width:100%; margin-top:0.5rem;">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" id="btn-icon"><path d="M8 1v6M8 9v6M1 8h6M9 8h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity="0.6"/><circle cx="8" cy="8" r="2" fill="currentColor"/></svg>
                        <span id="btn-label">Generate Review</span>
                        <span id="btn-loading" style="display:none;">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="animation:spin 1s linear infinite"><circle cx="7" cy="7" r="5.5" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"/><path d="M7 1.5a5.5 5.5 0 0 1 5.5 5.5" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg>
                            Generating...
                        </span>
                    </button>
                </form>
            </section>

            {{-- Output panel --}}
            <section class="card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:1.75rem;">
                    <div>
                        <div class="section-eyebrow">Generated Output</div>
                        <h2 style="font-family:'Syne',sans-serif; font-size:1.375rem; font-weight:700; color:#fff;">Review result</h2>
                    </div>
                    <span class="badge badge-gold">
                        <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
                        Mistral AI
                    </span>
                </div>

                @if ($result ?? null)
                    <div style="display:flex;flex-direction:column;gap:1.25rem;">
                        <!-- Stars -->
                        <div style="display:flex;align-items:center;gap:0.375rem;">
                            @for ($i = 1; $i <= 5; $i++)
                                <span style="font-size:1.5rem;" class="{{ $i <= intval($result['rating'] ?? 0) ? 'star-filled' : 'star-empty' }}">
                                    {{ $i <= intval($result['rating'] ?? 0) ? '★' : '★' }}
                                </span>
                            @endfor
                            <span style="margin-left:0.5rem;font-family:'Syne',sans-serif;font-size:0.875rem;font-weight:700;color:var(--gold-bright);">
                                {{ $result['rating'] ?? 0 }}<span style="color:var(--text-muted);font-weight:400;"> / 5</span>
                            </span>
                        </div>

                        <!-- Review text -->
                        <div class="review-result">
                            <p style="font-style:italic;color:var(--text-secondary);line-height:1.8;font-size:0.9rem;">
                                {{ $result['review'] ?? 'No review generated.' }}
                            </p>
                        </div>

                        <p style="font-family:'Syne',sans-serif;font-size:0.65rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:rgba(255,255,255,0.2);display:flex;align-items:center;gap:0.375rem;">
                            <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><circle cx="5" cy="5" r="4" stroke="currentColor" stroke-width="1.2"/><path d="M5 3v2.5L6.5 7" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                            Generated by Mistral AI · Behavioural simulation
                        </p>
                    </div>
                @else
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="4" y="8" width="32" height="24" rx="6" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M10 16h20M10 21h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <circle cx="30" cy="26" r="6" fill="rgba(22,163,74,0.15)" stroke="rgba(22,163,74,0.4)" stroke-width="1.5"/>
                            <path d="M28 26l1.5 1.5L32 24" stroke="#22C55E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div>
                            <p style="font-family:'Syne',sans-serif;font-weight:600;color:rgba(255,255,255,0.4);margin-bottom:0.25rem;">No output yet</p>
                            <p style="color:var(--text-muted);font-size:0.8rem;">Select a persona and enter a product to generate a review</p>
                        </div>
                    </div>
                @endif
            </section>
        </div>

        {{-- ── Persona Analysis panel ── --}}
        @if ($persona ?? null)
            <section class="card fade-up fade-up-3">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:1.75rem; flex-wrap:wrap;">
                    <div>
                        <div class="section-eyebrow">Selected Persona</div>
                        <h2 style="font-family:'Syne',sans-serif; font-size:1.375rem; font-weight:700; color:#fff;">{{ $persona['name'] }}</h2>
                    </div>
                    <span class="badge badge-muted">Review profile</span>
                </div>

                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.75rem;" id="stats-grid">
                    <div class="stat-card">
                        <div class="stat-label">Average Rating</div>
                        <div class="stat-value">{{ $persona['avg_rating'] }}<span style="font-size:1.25rem;color:var(--gold-bright);">★</span></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Total Reviews</div>
                        <div class="stat-value">{{ $persona['review_count'] }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Writing Style</div>
                        <div style="font-family:'DM Sans',sans-serif; font-size:0.875rem; font-weight:500; color:var(--text-secondary); line-height:1.5; margin-top:0.25rem;">{{ Str::limit($persona['style'], 60) }}</div>
                    </div>
                </div>

                @if (!empty($persona['reviews']))
                    <div>
                        <div class="section-eyebrow" style="margin-bottom:1rem;">Sample Reviews from this Persona</div>
                        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem;" id="sample-reviews-grid">
                            @foreach (array_slice($persona['reviews'], 0, 3) as $sample)
                                <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:14px; padding:1rem;">
                                    <div style="display:flex;align-items:center;gap:0.25rem;margin-bottom:0.75rem;">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span style="font-size:0.8rem;" class="{{ $i <= $sample['rating'] ? 'star-filled' : 'star-empty' }}">★</span>
                                        @endfor
                                    </div>
                                    <p style="font-size:0.825rem;line-height:1.65;color:var(--text-muted);">{{ Str::limit($sample['text'], 160) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        @endif
    </div>

    <style>
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @media (max-width: 900px) {
            #main-grid { grid-template-columns: 1fr !important; }
            #stats-grid { grid-template-columns: 1fr 1fr !important; }
            #sample-reviews-grid { grid-template-columns: 1fr !important; }
        }
        @media (max-width: 600px) {
            #stats-grid { grid-template-columns: 1fr !important; }
        }
    </style>

    <script>
        document.getElementById('task-a-form').addEventListener('submit', function () {
            var btn     = document.getElementById('generate-btn');
            var label   = document.getElementById('btn-label');
            var loading = document.getElementById('btn-loading');
            var icon    = document.getElementById('btn-icon');
            btn.disabled = true;
            label.style.display = 'none';
            icon.style.display  = 'none';
            loading.style.display = 'flex';
        });
    </script>
@endsection