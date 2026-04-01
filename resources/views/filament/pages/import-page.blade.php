<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Import Buku --}}
        <x-filament::section>
            <x-slot name="heading">Import Buku</x-slot>
            <x-slot name="description">
                Import data buku paket dari file Excel atau CSV.
                Duplikat akan dilewati otomatis.
            </x-slot>
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Download template terlebih dahulu, isi data, lalu upload file-nya.
                </p>
                <div class="flex gap-2">
                    <x-filament::button
                        wire:click="mountAction('downloadTemplateBuku')"
                        color="gray"
                        icon="heroicon-o-arrow-down-tray"
                    >
                        Download Template
                    </x-filament::button>
                    <x-filament::button
                        wire:click="mountAction('importBuku')"
                        color="primary"
                        icon="heroicon-o-arrow-up-tray"
                    >
                        Import Buku
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- Import Kelas --}}
        <x-filament::section>
            <x-slot name="heading">Import Kelas</x-slot>
            <x-slot name="description">
                Import data kelas dari file Excel atau CSV.
                Data yang sudah ada akan diperbarui otomatis (upsert).
            </x-slot>
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Download template terlebih dahulu, isi data, lalu upload file-nya.
                </p>
                <div class="flex gap-2">
                    <x-filament::button
                        wire:click="mountAction('downloadTemplateKelas')"
                        color="gray"
                        icon="heroicon-o-arrow-down-tray"
                    >
                        Download Template
                    </x-filament::button>
                    <x-filament::button
                        wire:click="mountAction('importKelas')"
                        color="primary"
                        icon="heroicon-o-arrow-up-tray"
                    >
                        Import Kelas
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- Import Siswa --}}
        <x-filament::section>
            <x-slot name="heading">Import Siswa</x-slot>
            <x-slot name="description">
                Import data siswa dari file Excel atau CSV.
                Siswa dengan NISN yang sudah ada akan dilewati.
            </x-slot>
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Download template terlebih dahulu, isi data, lalu upload file-nya.
                </p>
                <div class="flex gap-2">
                    <x-filament::button
                        wire:click="mountAction('downloadTemplateSiswa')"
                        color="gray"
                        icon="heroicon-o-arrow-down-tray"
                    >
                        Download Template
                    </x-filament::button>
                    <x-filament::button
                        wire:click="mountAction('importSiswa')"
                        color="primary"
                        icon="heroicon-o-arrow-up-tray"
                    >
                        Import Siswa
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- Import Mata Pelajaran --}}
        <x-filament::section>
            <x-slot name="heading">Import Mata Pelajaran</x-slot>
            <x-slot name="description">
                Import data mata pelajaran dari file Excel atau CSV.
                Data yang sudah ada akan diperbarui otomatis (upsert).
            </x-slot>
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Download template terlebih dahulu, isi data, lalu upload file-nya.
                </p>
                <div class="flex gap-2">
                    <x-filament::button
                        wire:click="mountAction('downloadTemplateMapel')"
                        color="gray"
                        icon="heroicon-o-arrow-down-tray"
                    >
                        Download Template
                    </x-filament::button>
                    <x-filament::button
                        wire:click="mountAction('importMapel')"
                        color="primary"
                        icon="heroicon-o-arrow-up-tray"
                    >
                        Import Mata Pelajaran
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

    </div>

    {{-- Wajib ada agar modal form action bisa muncul --}}
    <x-filament-actions::modals />
</x-filament-panels::page>
