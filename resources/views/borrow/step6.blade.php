<x-step-layout title="Pilih Buku & Siswa" :currentStep="6">

    <form action="{{ route('borrow.confirm') }}" method="POST" class="w-full" x-data="step6()">
        @csrf
        <input type="hidden" name="class_id" value="{{ $classId }}">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <input type="hidden" name="book_id" :value="selectedBook">

        @if (session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 font-inter text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- ===================== DAFTAR BUKU ===================== --}}
        <div class="w-full bg-accent/40 rounded-2xl p-6 mb-6">
            <h3 class="font-outfit font-bold text-primary text-lg mb-4">Daftar Buku Tersedia</h3>

            <div class="flex flex-wrap gap-4">
                @foreach ($books as $book)
                    <div @click="selectedBook = {{ $book->id }}"
                        :class="selectedBook == {{ $book->id }} ?
                            'bg-primary border-primary text-white' :
                            'bg-white border-accent text-gray-900 hover:-translate-y-0.5'"
                        class="cursor-pointer flex flex-col gap-1 w-62 p-5.5
                                border-2 rounded-[14px] transition-all duration-200
                                shadow-[0px_2px_10px_0px_rgba(43,122,120,0.1)]">

                        {{-- Kode buku --}}
                        <span class="font-inter text-xs"
                            :class="selectedBook == {{ $book->id }} ? 'text-white/70' : 'text-gray-500'">
                            {{ $book->book_code }}
                        </span>

                        {{-- Judul --}}
                        <span class="font-outfit font-bold text-sm leading-snug">
                            {{ $book->title }}
                        </span>

                        {{-- Status & stok --}}
                        <div class="flex items-center justify-between mt-2">
                            <span class="font-inter text-xs flex items-center gap-1"
                                :class="selectedBook == {{ $book->id }} ? 'text-white/80' : 'text-primary'">
                                ✓ {{ $book->remaining_stock > 0 ? 'Tersedia' : 'Habis' }}
                            </span>
                            <span class="font-inter text-xs"
                                :class="selectedBook == {{ $book->id }} ? 'text-white/80' : 'text-gray-500'">
                                {{ $book->remaining_stock }} buku
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ===================== DAFTAR SISWA ===================== --}}
        <div class="w-full">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-outfit font-bold text-primary text-lg">
                    Daftar Siswa — {{ $major->major_code }} {{ $class->class_name }}
                </h3>
                <div class="flex gap-2">
                    <button type="button" @click="selectAll()"
                        class="font-inter font-medium text-sm px-4 py-2 rounded-lg
                                   bg-primary text-white hover:bg-primary-dark transition-colors duration-200">
                        Pilih Semua
                    </button>
                    <button type="button" @click="deselectAll()"
                        class="font-inter font-medium text-sm px-4 py-2 rounded-lg
                                   border-2 border-accent text-primary hover:bg-accent/40 transition-colors duration-200">
                        Batalkan Semua
                    </button>
                </div>
            </div>

            {{-- Info bar: dipilih & sisa stok --}}
            <div
                class="flex items-center justify-between bg-[#FEFFDF] border-l-4 border-primary
            rounded-r-[10px] px-6 py-3 mb-2">
                <span class="font-inter text-sm text-gray-700">
                    Dipilih: <span class="font-bold text-primary" x-text="selectedStudents.length + ' Siswa'"></span>
                </span>
                <span class="font-inter text-sm text-gray-700">
                    Sisa Stok:
                    <span class="font-bold text-primary" x-text="remainingStock + ' buku'">
                    </span>
                </span>
            </div>

            {{-- Tabel siswa --}}
            <div class="rounded-xl border border-accent overflow-hidden">
                {{-- Header --}}
                <div class="grid grid-cols-[56px_80px_1fr_160px] bg-accent/50 px-4 py-3 border-b border-accent">
                    <div></div>
                    <span class="font-inter font-semibold text-sm text-gray-700">No</span>
                    <span class="font-inter font-semibold text-sm text-gray-700">Nama Siswa</span>
                    <span class="font-inter font-semibold text-sm text-gray-700 text-right">NIS</span>
                </div>

                {{-- Body --}}
                @foreach ($students as $i => $student)
                    @php
                        $alreadyBorrowed = in_array($student->id, $borrowedMap[$books->first()?->id] ?? []);
                    @endphp
                    <div class="grid grid-cols-[56px_80px_1fr_160px] items-center px-4 py-3
                                border-b border-accent/60 last:border-b-0
                                transition-colors duration-150"
                        :class="selectedStudents.includes({{ $student->id }}) ? 'bg-primary/5' : 'hover:bg-gray-50'">

                        {{-- Checkbox --}}
                        <div class="flex items-center">
                            @if ($alreadyBorrowed)
                                <input type="checkbox" disabled
                                    class="w-5 h-5 rounded border-gray-300 opacity-40 cursor-not-allowed" />
                            @else
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                    x-model="selectedStudents" :value="{{ $student->id }}" {{-- ✅ Disable kalau stok habis DAN siswa ini belum dipilih --}}
                                    :disabled="remainingStock <= 0 && !selectedStudents.includes({{ $student->id }})"
                                    class="w-5 h-5 rounded border-accent text-primary
                      focus:ring-primary focus:ring-2 cursor-pointer
                      disabled:opacity-40 disabled:cursor-not-allowed" />
                            @endif
                        </div>

                        {{-- No --}}
                        <span class="font-inter text-sm text-gray-600">{{ $i + 1 }}</span>

                        {{-- Nama --}}
                        <div class="flex items-center gap-2">
                            <span class="font-inter text-sm text-gray-900">{{ $student->student_name }}</span>
                            @if ($alreadyBorrowed)
                                <span
                                    class="font-inter text-xs text-amber-600 bg-amber-50
                                             border border-amber-200 px-2 py-0.5 rounded-full">
                                    Sudah meminjam
                                </span>
                            @endif
                        </div>

                        {{-- NIS --}}
                        <span class="font-inter text-sm text-gray-600 text-right">{{ $student->nis }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Tombol konfirmasi --}}
            <button type="submit" :disabled="selectedStudents.length === 0 || !selectedBook || remainingStock < 0"
                class="w-full mt-6 font-outfit font-bold text-lg text-white
               bg-primary hover:bg-primary-dark
               disabled:opacity-40 disabled:cursor-not-allowed
               py-4.5 rounded-xl
               transition-colors duration-200
               shadow-[0px_4px_15px_0px_rgba(43,122,120,0.3)]">
                Konfirmasi Peminjaman
            </button>
        </div>
    </form>

</x-step-layout>

<script>
    function step6() {
        return {
            selectedBook: null,
            selectedStudents: [],

            // ✅ Tambahkan data books dari Laravel
            books: @json(
                $books->map(function ($book) {
                    return [
                        'id' => $book->id,
                        'remaining_stock' => $book->remaining_stock,
                    ];
                })),

            // ✅ Computed property untuk sisa stok
            get remainingStock() {
                if (!this.selectedBook) return 0;

                const book = this.books.find(b => b.id == this.selectedBook);


                return book ? Math.max(0, book.remaining_stock - this.selectedStudents.length) : 0;
            },

            selectAll() {
                const checkboxes = document.querySelectorAll('input[name="student_ids[]"]:not(:disabled)');
                this.selectedStudents = Array.from(checkboxes).map(c => parseInt(c.value));
            },

            deselectAll() {
                this.selectedStudents = [];
            },
        }
    }
</script>
