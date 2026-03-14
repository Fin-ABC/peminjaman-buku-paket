<x-step-layout title="Verifikasi Siswa" :currentStep="5">

    {{-- Kartu form verifikasi --}}
    <div class="w-full max-w-175 bg-white border border-accent rounded-[20px]
                shadow-[0px_4px_20px_0px_rgba(43,122,120,0.08)] px-12.5 py-12.5">

        {{-- Info box --}}
        <div class="flex items-start gap-3 bg-accent/60 rounded-xl px-5 py-4 mb-8">
            <svg class="w-5 h-5 text-primary mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z" />
            </svg>
            <p class="font-inter text-sm text-gray-700 leading-relaxed">
                Untuk melanjutkan, silahkan masukkan <span class="font-semibold text-primary">2 Nomor Induk Siswa (NIS)</span>
                dari kelas <span class="font-semibold text-primary">{{ $level }} - {{ $major->major_code }}</span>
            </p>
        </div>

        {{-- Pesan error --}}
        @if (session('verification_error'))
            <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-600
                        rounded-[10px] px-4 py-3 mb-6 font-inter text-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ session('verification_error') }}
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('return.step5.verify') }}" method="POST">
            @csrf

            {{-- Teruskan semua parameter step sebelumnya --}}
            <input type="hidden" name="level"    value="{{ $level }}">
            <input type="hidden" name="major_id" value="{{ $majorId }}">
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <input type="hidden" name="semester" value="{{ $semester }}">

            {{-- NIS Pertama --}}
            <div class="mb-5">
                <label class="block font-inter font-medium text-sm text-gray-800 mb-2">
                    NIS Siswa Pertama <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="nis_1"
                       value="{{ old('nis_1') }}"
                       placeholder="Masukkan NIS (10-16 digit)"
                       maxlength="16"
                       autocomplete="off"
                       @class([
                           'w-full font-inter text-base px-[22px] py-[18px]',
                           'border rounded-[12px] outline-none transition-all duration-200',
                           'placeholder:text-gray-400 text-gray-900',
                           'focus:border-primary focus:ring-2 focus:ring-primary/20',
                           'border-red-300 bg-red-50' => $errors->has('nis_1'),
                           'border-accent bg-white'   => !$errors->has('nis_1'),
                       ]) />
                @error('nis_1')
                    <p class="mt-1.5 font-inter text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- NIS Kedua --}}
            <div class="mb-8">
                <label class="block font-inter font-medium text-sm text-gray-800 mb-2">
                    NIS Siswa Kedua <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="nis_2"
                       value="{{ old('nis_2') }}"
                       placeholder="Masukkan NIS (10-16 digit)"
                       maxlength="16"
                       autocomplete="off"
                       @class([
                           'w-full font-inter text-base px-[22px] py-[18px]',
                           'border rounded-[12px] outline-none transition-all duration-200',
                           'placeholder:text-gray-400 text-gray-900',
                           'focus:border-primary focus:ring-2 focus:ring-primary/20',
                           'border-red-300 bg-red-50' => $errors->has('nis_2'),
                           'border-accent bg-white'   => !$errors->has('nis_2'),
                       ]) />
                @error('nis_2')
                    <p class="mt-1.5 font-inter text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full font-outfit font-bold text-lg text-white
                           bg-primary hover:bg-primary-dark active:bg-primary-dark
                           py-4.5 rounded-xl
                           transition-colors duration-200
                           shadow-[0px_4px_15px_0px_rgba(43,122,120,0.3)]">
                Verifikasi & Lanjutkan
            </button>
        </form>
    </div>

</x-step-layout>
