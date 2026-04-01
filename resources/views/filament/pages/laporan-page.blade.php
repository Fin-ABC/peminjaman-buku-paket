<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Laporan Per Angkatan --}}
        <x-filament::section>
            <x-slot name="heading">Laporan Per Angkatan</x-slot>
            <x-slot name="description">
                Rekapitulasi data per tingkat kelas (10, 11, 12) untuk satu tahun ajaran.
            </x-slot>
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Pilih tahun ajaran lalu klik Export untuk mengunduh laporan dalam format Excel.
                </p>
                <x-filament::button
                    wire:click="mountAction('exportGradeLevel')"
                    color="success"
                    icon="heroicon-o-arrow-down-tray"
                >
                    Export Laporan Per Angkatan
                </x-filament::button>
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
                <x-filament::button
                    wire:click="mountAction('exportSemester')"
                    color="success"
                    icon="heroicon-o-arrow-down-tray"
                >
                    Export Laporan Per Semester
                </x-filament::button>
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
                <x-filament::button
                    wire:click="mountAction('exportSchoolYear')"
                    color="success"
                    icon="heroicon-o-arrow-down-tray"
                >
                    Export Laporan Per Tahun Ajaran
                </x-filament::button>
            </div>
        </x-filament::section>

    </div>

    {{-- Wajib ada agar modal form action bisa muncul --}}
    <x-filament-actions::modals />
</x-filament-panels::page>
