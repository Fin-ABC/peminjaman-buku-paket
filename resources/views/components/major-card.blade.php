@props([
    'href'        => '#',
    'code'        => '',
    'name'        => '',
    'active'      => false,
])

<a href="{{ $href }}"
   @class([
       'group flex flex-col items-center justify-center gap-1',
       'px-[22px] py-[26px] rounded-[14px] border-2 cursor-pointer',
       'transition-all duration-200',
       'shadow-[0px_2px_10px_0px_rgba(43,122,120,0.1)]',
       'hover:shadow-[0px_6px_20px_0px_rgba(43,122,120,0.2)] hover:-translate-y-1',
       'border-primary bg-primary/5 -translate-y-1 shadow-[0px_6px_20px_0px_rgba(43,122,120,0.2)]' => $active,
       'border-accent bg-white' => !$active,
   ])>

    <span @class([
        'font-outfit font-bold text-[17.6px] text-center',
        'text-primary-dark' => $active,
        'text-gray-900'     => !$active,
    ])>
        {{ $code }}
    </span>

    <span class="font-inter text-[13.6px] text-[#5A5A5A] text-center leading-snug">
        {{ $name }}
    </span>
</a>
