@props([
    'title'       => '',
    'currentStep' => 1,
    'totalSteps'  => 6,
])

<x-app :title="$title . ' — Perpustakaan'">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-340 bg-white border border-accent rounded-[20px]
                    shadow-[0px_8px_40px_0px_rgba(43,122,120,0.1)] px-6 py-16">

            <div class="flex flex-col items-center gap-8 max-w-275 mx-auto">

                {{-- Step badge --}}
                <div class="bg-accent text-primary font-inter font-semibold text-sm
                            px-5 py-1.5 rounded-full">
                    Step {{ $currentStep }} of {{ $totalSteps }}
                </div>

                {{-- Judul step --}}
                <h2 class="font-outfit font-extrabold text-[2.2rem] text-gray-900 text-center -mt-4">
                    {{ $title }}
                </h2>

                {{-- Konten step (kartu pilihan, form, dll) --}}
                {{ $slot }}

            </div>
        </div>
    </div>
</x-app>
