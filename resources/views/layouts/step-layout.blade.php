@props([
    'title' => '',
    'currentStep' => 1,
    'totalSteps' => 6,
])

<x-app :title="$title . ' — Perpustakaan'">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div
            class="w-full max-w-340 bg-white border border-accent rounded-[20px]
                    shadow-[0px_8px_40px_0px_rgba(43,122,120,0.1)] px-6 py-16">

            <div class="flex flex-col items-center gap-8 max-w-275 mx-auto">

                {{-- Tombol kembali (muncul di semua step) --}}
                <div class="w-full flex items-center">
                    <button onclick="history.back()"
                        class="flex items-center gap-2 font-inter font-medium text-sm
                                   text-primary hover:text-primary-dark
                                   transition-colors duration-200 group">
                        <svg class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali
                    </button>
                </div>

                {{-- Step badge --}}
                <div
                    class="bg-accent text-primary font-inter font-semibold text-sm
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
