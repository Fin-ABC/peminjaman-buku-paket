@props([
    'href'   => '#',
    'active' => false,
])

<a href="{{ $href }}"
   @class([
       'group flex items-center justify-center min-w-[140px] h-[138px] px-12 py-9',
       'bg-white border-2 rounded-[16px] cursor-pointer',
       'transition-all duration-200',
       'shadow-[0px_4px_15px_0px_rgba(43,122,120,0.1)]',
       'hover:shadow-[0px_8px_25px_0px_rgba(43,122,120,0.25)] hover:-translate-y-1',
       // Active state
       'border-primary bg-primary/5 shadow-[0px_8px_25px_0px_rgba(43,122,120,0.25)] -translate-y-1' => $active,
       'border-accent' => !$active,
   ])>
    <span @class([
        'font-outfit font-extrabold text-[2.5rem] leading-none',
        'text-primary'      => !$active,
        'text-primary-dark' => $active,
    ])>
        {{ $slot }}
    </span>
</a>
