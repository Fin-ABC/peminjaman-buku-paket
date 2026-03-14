<x-app title="Peminjaman Berhasil — Perpustakaan">
    <div class="min-h-screen flex items-center justify-center p-6 bg-gray-400/30">

        {{-- Modal card --}}
        <div class="bg-white rounded-[20px] shadow-[0px_8px_40px_0px_rgba(0,0,0,0.15)]
                    w-full max-w-[500px] px-[50px] py-[50px] flex flex-col items-center gap-6">

            {{-- Icon sukses --}}
            <div class="w-[80px] h-[80px] rounded-full bg-green-500
                        flex items-center justify-center
                        shadow-[0px_4px_20px_0px_rgba(34,197,94,0.4)]">
                <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            {{-- Judul & subjudul --}}
            <div class="text-center">
                <h2 class="font-outfit font-extrabold text-[1.8rem] text-gray-900">
                    Peminjaman Berhasil!
                </h2>
                <p class="font-inter text-sm text-[#5A5A5A] mt-2 leading-relaxed">
                    Buku telah berhasil dipinjamkan kepada siswa yang dipilih.
                </p>
            </div>

            {{-- Detail peminjaman --}}
            <div class="w-full bg-accent/50 rounded-[14px] overflow-hidden">

                {{-- Buku --}}
                <div class="flex items-center justify-between px-5 py-3
                            border-b border-accent">
                    <span class="font-inter text-sm text-[#5A5A5A]">Buku</span>
                    <span class="font-inter font-bold text-sm text-primary text-right max-w-[220px]">
                        {{ session('book_title', '-') }}
                    </span>
                </div>

                {{-- Kelas --}}
                <div class="flex items-center justify-between px-5 py-3
                            border-b border-accent">
                    <span class="font-inter text-sm text-[#5A5A5A]">Kelas</span>
                    <span class="font-inter font-bold text-sm text-primary">
                        {{ session('class_name', '') }}
                    </span>
                </div>

                {{-- Jumlah Siswa --}}
                <div class="flex items-center justify-between px-5 py-3
                            border-b border-accent">
                    <span class="font-inter text-sm text-[#5A5A5A]">Jumlah Siswa</span>
                    <span class="font-inter font-bold text-sm text-primary">
                        {{ session('student_count', 0) }} Siswa
                    </span>
                </div>

                {{-- Tanggal --}}
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="font-inter text-sm text-[#5A5A5A]">Tanggal</span>
                    <span class="font-inter font-bold text-sm text-primary">
                        {{ session('borrow_date', '-') }}
                    </span>
                </div>

            </div>

            {{-- Tombol kembali --}}
            <a href="{{ route('student.menu') }}"
               class="w-full text-center font-outfit font-bold text-base text-white
                      bg-primary hover:bg-primary-dark
                      py-[16px] rounded-[12px]
                      transition-colors duration-200
                      shadow-[0px_4px_15px_0px_rgba(43,122,120,0.3)]">
                Kembali ke Menu
            </a>

        </div>
    </div>
</x-app>
