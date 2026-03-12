@props([
    'href' => '#',
    'label' => '',
    'description' => '',
])

<a href="{{ $href }}"
    class="group relative flex flex-col items-center justify-center gap-0
          w-full bg-white border-2 border-accent rounded-[20px]
          shadow-[0px_4px_20px_0px_rgba(43,122,120,0.1)]
          transition-all duration-200 overflow-hidden
          hover:shadow-[0px_8px_30px_0px_rgba(43,122,120,0.25)] hover:-translate-y-1
          py-12.5 px-10 min-h-72.5">

    {{-- Gradient accent bar — muncul saat hover --}}
    <span
        class="absolute top-0 left-0 right-0 h-1 rounded-t-[20px]
                 bg-linear-to-r from-primary to-primary-light
                 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></span>

    {{-- Icon container --}}
    <div
        class="flex items-center justify-center w-20 h-20 bg-accent rounded-[40px]
                transition-colors duration-200 group-hover:bg-primary/10 mb-13.75">
        {{ $icon }}
    </div>

    {{-- Label --}}
    <h3 class="font-outfit font-bold text-[1.8rem] text-gray-900 text-center">
        {{ $label }}
    </h3>

    {{-- Description --}}
    <p class="font-inter text-base text-[#5A5A5A] text-center mt-1">
        {{ $description }}
    </p>
</a>
