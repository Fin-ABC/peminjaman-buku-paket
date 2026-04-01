<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Laporan Per Angkatan --}}
        <x-filament::section>
            <x-slot name="heading">Laporan Per Angkatan</x-slot>
            <x-slot name="description">
                Rekapitulasi data per tingkat kelas (10, 11, 12) untuk satu tahun ajaran.
            </x-slot>

            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Pilih tahun ajaran lalu klik Export untuk mengunduh laporan dalam format Excel.
                </div>
            </div>
        </x-filament::section>

        {{-- Laporan Per Semester --}}
        <x-filament::section>
            <x-slot name="heading">Laporan Per Semester</x-slot>
            <x-slot name="description">
                Status pengembalian buku per kelas dalam satu semester tertentu.
            </x-slot>
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Pilih tahun ajaran dan semester lalu klik Export untuk mengunduh laporan.
                </p>
            </div>
        </x-filament::section>

        {{-- Laporan Per Tahun Ajaran --}}
        <x-filament::section>
            <x-slot name="heading">Laporan Per Tahun Ajaran</x-slot>
            <x-slot name="description">
                Stock opname tahunan — kondisi seluruh koleksi buku perpustakaan.
            </x-slot>
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Pilih tahun ajaran lalu klik Export untuk mengunduh laporan stock opname.
                </p>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>
