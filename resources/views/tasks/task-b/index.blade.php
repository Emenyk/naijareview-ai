@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <section class="bg-white border border-gray-200 rounded-3xl shadow-sm p-8">
            <div class="flex flex-col gap-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.24em] text-emerald-700 font-semibold">Task B</p>
                    <h1 class="mt-2 text-3xl font-semibold text-slate-900">Recommendation Engine</h1>
                </div>
                <p class="max-w-2xl text-gray-600 text-base leading-7">
                    Generate focused recommendations based on user context and preference signals. This page introduces a reliable, professional workflow for delivering relevant suggestions.
                </p>
            </div>
        </section>

        <section class="grid gap-6 md:grid-cols-2">
            <div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-slate-900">What to do</h2>
                <p class="mt-3 text-gray-600 leading-7">
                    Use the task to create concise, relevant product or content recommendations based on a user identifier and optional preference notes.
                </p>
            </div>

            <div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-slate-900">Why it matters</h2>
                <p class="mt-3 text-gray-600 leading-7">
                    High-quality recommendations increase engagement and help users find value faster, while preserving a simple, professional presentation.
                </p>
            </div>
        </section>

        <section class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-slate-900">Key details</h2>
            <ul class="mt-4 space-y-3 text-gray-600">
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-600"></span>
                    <span>Designed for user-centric recommendation flows.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-600"></span>
                    <span>Supports explicit preference input and user context.</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="mt-1 h-2.5 w-2.5 rounded-full bg-emerald-600"></span>
                    <span>Consistent layout with Task A for a unified app experience.</span>
                </li>
            </ul>
        </section>
    </div>
@endsection