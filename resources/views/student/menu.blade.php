<x-app title="Menu Siswa — Perpustakaan">
    <div class="min-h-screen flex items-center justify-center p-6">

        <div class="w-full max-w-340 bg-white border border-accent rounded-[20px]
                    shadow-[0px_8px_40px_0px_rgba(43,122,120,0.1)] px-6 py-16">

            <div class="flex flex-col items-center gap-12 max-w-200 mx-auto">

                {{-- Judul --}}
                <h1 class="font-outfit font-extrabold text-[2.5rem] text-gray-900 text-center">
                    Perpustakaan
                </h1>

                {{-- Dua kartu menu --}}
                <div class="grid grid-cols-2 gap-8 w-full">

                    <x-menu-card
                        href="{{ route('borrow.step1') }}"
                        label="Peminjaman"
                        description="Pinjam buku paket sesuai kelas">
                        <x-slot name="icon">
                            {{-- Icon: book with plus --}}
                            <svg class="w-10 h-10 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 4v16m8-8H4" />
                            </svg>
                        </x-slot>
                    </x-menu-card>

                    <x-menu-card
                        href="{{ route('return.step1') }}"
                        label="Pengembalian"
                        description="Kembalikan buku yang dipinjam">
                        <x-slot name="icon">
                            {{-- Icon: checkmark --}}
                            <svg class="w-10 h-10 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M5 13l4 4L19 7" />
                            </svg>
                        </x-slot>
                    </x-menu-card>

                </div>
            </div>
        </div>
    </div>
</x-app>
