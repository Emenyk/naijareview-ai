@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section class="bg-white border border-gray-200 rounded-3xl shadow-sm p-8">
            <div class="flex flex-col gap-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.24em] text-emerald-700 font-semibold">Task A</p>
                    <h1 class="mt-2 text-3xl font-semibold text-slate-900">User Modeling</h1>
                </div>
                <p class="max-w-2xl text-gray-600 text-base leading-7">
                    Build a clean, structured user profile from the submitted input. This task is designed for capturing user preferences, interests, and identity attributes in a clear format.
                </p>
            </div>
        </section>

        <section class="grid gap-6 md:grid-cols-2">
            <div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-slate-900">What to do</h2>
                <p class="mt-3 text-gray-600 leading-7">
                    Provide a concise user description and let the model create a polished user profile summary with core attributes and preferences.
                </p>
            </div>

            <div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-slate-900">Why it matters</h2>
                <p class="mt-3 text-gray-600 leading-7">
                    Structured user data improves personalization, recommendation accuracy, and downstream analytics for intelligent product experiences.
                </p>
            </div>
        </section>

        <section class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-slate-900">Key details</h2>
            <ul class="mt-4 space-y-3 text-gray-600">
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-600"></span>
                    <span>Accepts user-provided text as input.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-600"></span>
                    <span>Outputs simplified profile fields and preference cues.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-600"></span>
                    <span>Designed for clarity, consistency, and ease of integration.</span>
                </li>
            </ul>
        </section>
    </div>
@endsection