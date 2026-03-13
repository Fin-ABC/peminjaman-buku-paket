<x-app title="Perpustakaan SMKN 1 Sumedang">
    <div class="min-h-screen flex items-center justify-center p-6">

        {{-- Card utama --}}
        <div class="w-full max-w-340 bg-white border border-accent rounded-[20px]
                    shadow-[0px_8px_40px_0px_rgba(43,122,120,0.1)] px-6 py-16">

            <div class="flex flex-col items-center gap-6">

                {{-- Logo placeholder --}}
                <div class="w-20 h-20 rounded-[20px] flex items-center justify-center
                            bg-linear-to-br from-primary to-primary-light
                            shadow-[0px_4px_20px_0px_rgba(43,122,120,0.2)]">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>

                {{-- Judul --}}
                <div class="text-center">
                    <h1 class="font-inter font-extrabold text-[2.8rem] leading-tight text-gray-900 uppercase">
                        Perpustakaan<br>SMKN 1 Sumedang
                    </h1>
                </div>

                {{-- Subjudul --}}
                <p class="font-inter text-lg text-[#5A5A5A] text-center">
                    Silahkan pilih untuk melanjutkan sebagai Siswa atau Petugas
                </p>

                {{-- Tombol pilihan --}}
                <div class="flex flex-wrap gap-6 justify-center mt-4">

                    <x-role-card href="{{ route('student.menu') }}" label="Siswa">
                        <x-slot name="icon">
                            {{-- Icon siswa --}}
                            <svg class="w-8 h-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </x-slot>
                    </x-role-card>

                    <x-role-card href="admin/login" label="Petugas">
                        <x-slot name="icon">
                            {{-- Icon petugas --}}
                            <svg class="w-8 h-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </x-slot>
                    </x-role-card>

                </div>
            </div>
        </div>
    </div>
</x-app>
