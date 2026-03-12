@props(['href' => '#', 'icon' => null, 'label' => ''])

<a href="{{ $href }}"
    class="group flex flex-col items-center justify-center gap-5 w-60 h-[213px]
          bg-white border-2 border-accent rounded-2xl
          shadow-[0px_4px_15px_0px_rgba(43,122,120,0.1)]
          transition-all duration-200
          hover:shadow-[0px_8px_30px_0px_rgba(43,122,120,0.2)] hover:-translate-y-1 cursor-pointer">

    {{-- Icon container --}}
    <div
        class="flex items-center justify-center w-17.5 h-17.5 bg-accent rounded-[35px]
                transition-colors duration-200 group-hover:bg-primary-light/20">
        {{ $icon }}
    </div>

    {{-- Label --}}
    <span class="font-outfit font-bold text-2xl text-gray-900">
        {{ $label }}
    </span>
</a>
