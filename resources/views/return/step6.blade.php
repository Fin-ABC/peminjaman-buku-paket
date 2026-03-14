<x-step-layout title="Pilih Buku & Siswa" :currentStep="6">

    <div class="w-full" x-data="returnStep6()">

        {{-- ===================== DAFTAR BUKU ===================== --}}
        <div class="w-full bg-accent/40 rounded-[16px] p-6 mb-6">
            <h3 class="font-outfit font-bold text-primary text-lg mb-4">Daftar Buku yang Dipinjam</h3>

            @if ($books->isEmpty())
                <div class="flex items-center justify-center py-10">
                    <p class="font-outfit font-bold text-2xl text-primary/50">Tidak Ada Buku yang Dipinjam</p>
                </div>
            @else
                <div class="flex flex-wrap gap-4">
                    @foreach ($books as $book)
                        <div @click="selectBook({{ $book['transaction_id'] }})"
                            :class="selectedTransactionId == {{ $book['transaction_id'] }} ?
                                'border-primary bg-primary/5' :
                                'border-accent bg-white hover:-translate-y-0.5'"
                            class="cursor-pointer flex flex-col gap-1 w-[248px] p-[22px]
                                    border-2 rounded-[14px] transition-all duration-200
                                    shadow-[0px_2px_10px_0px_rgba(43,122,120,0.1)]">

                            {{-- Kode buku --}}
                            <span class="font-inter text-xs text-gray-500">
                                {{ $book['book_code'] }}
                            </span>

                            {{-- Judul --}}
                            <span class="font-outfit font-bold text-sm text-gray-900 leading-snug">
                                {{ $book['title'] }}
                            </span>

                            {{-- Status --}}
                            <div class="flex items-center justify-between mt-2">
                                <span class="font-inter text-xs text-primary">✓ Dikembalikan</span>
                                <span class="font-inter text-xs text-gray-500">{{ $book['returned'] }} buku</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-inter text-xs text-red-500">✗ Belum Kembali</span>
                                <span class="font-inter text-xs text-gray-500">{{ $book['not_returned'] }} buku</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===================== DAFTAR SISWA ===================== --}}
        <div class="w-full">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-outfit font-bold text-primary text-lg">
                    Daftar Peminjam — {{ $major->major_code }} {{ $class->class_name }}
                </h3>
                <div class="flex gap-2">
                    <button type="button" @click="selectAll()"
                        class="font-inter font-medium text-sm px-4 py-2 rounded-[8px]
                                   bg-primary text-white hover:bg-primary-dark transition-colors duration-200">
                        Pilih Semua
                    </button>
                    <button type="button" @click="deselectAll()"
                        class="font-inter font-medium text-sm px-4 py-2 rounded-[8px]
                                   border-2 border-accent text-primary hover:bg-accent/40 transition-colors duration-200">
                        Batalkan Semua
                    </button>
                </div>
            </div>

            {{-- Info bar --}}
            <div
                class="flex items-center justify-between bg-[#FEFFDF] border-l-4 border-primary
                        rounded-r-[10px] px-6 py-3 mb-2">
                <span class="font-inter text-sm text-gray-700">
                    Dipilih: <span class="font-bold text-primary" x-text="selectedDetails.length + ' Siswa'"></span>
                </span>
                <span class="font-inter text-sm text-gray-700">
                    Belum Kembali: <span class="font-bold text-primary" x-text="notReturnedCount + ' buku'"></span>
                </span>
            </div>

            {{-- Loading state --}}
            <div x-show="loading" class="flex justify-center py-10">
                <span class="font-inter text-sm text-gray-400">Memuat data siswa...</span>
            </div>

            {{-- Empty state --}}
            <div x-show="!loading && !selectedTransactionId"
                class="flex justify-center py-10 rounded-[12px] border border-accent">
                <span class="font-inter text-sm text-gray-400">Pilih buku terlebih dahulu</span>
            </div>

            {{-- Tabel siswa --}}
            <div x-show="!loading && selectedTransactionId && students.length > 0"
                class="rounded-[12px] border border-accent overflow-hidden">

                {{-- Header --}}
                <div class="grid grid-cols-[56px_60px_1fr_140px_160px] bg-accent/50 px-4 py-3 border-b border-accent">
                    <div></div>
                    <span class="font-inter font-semibold text-sm text-gray-700">No</span>
                    <span class="font-inter font-semibold text-sm text-gray-700">Nama Siswa</span>
                    <span class="font-inter font-semibold text-sm text-gray-700">NIS</span>
                    <span class="font-inter font-semibold text-sm text-gray-700">Status</span>
                </div>

                {{-- Body --}}
                <template x-for="(student, index) in students" :key="student.detail_id">
                    <div class="grid grid-cols-[56px_60px_1fr_140px_160px] items-center px-4 py-3
                                border-b border-accent/60 last:border-b-0 transition-colors duration-150"
                        :class="isSelected(student.detail_id) ? 'bg-primary/5' : 'hover:bg-gray-50'">

                        {{-- Checkbox --}}
                        <div class="flex items-center">
                            <input type="checkbox" :checked="isSelected(student.detail_id)"
                                @change="toggleStudent(student)"
                                class="w-5 h-5 rounded border-accent text-primary
                                          focus:ring-primary focus:ring-2 cursor-pointer" />
                        </div>

                        {{-- No --}}
                        <span class="font-inter text-sm text-gray-600" x-text="index + 1"></span>

                        {{-- Nama --}}
                        <div class="flex items-center gap-2">
                            <span class="font-inter text-sm text-gray-900" x-text="student.student_name"></span>
                            <span x-show="student.is_overdue"
                                class="font-inter text-xs text-red-600 bg-red-50
                                         border border-red-200 px-2 py-0.5 rounded-full">
                                Terlambat
                            </span>
                        </div>

                        {{-- NIS --}}
                        <span class="font-inter text-sm text-gray-600" x-text="student.nis"></span>

                        {{-- Dropdown status --}}
                        <select x-model="student.status" :disabled="!isSelected(student.detail_id)"
                            class="font-inter text-sm bg-primary text-white
                                       px-3 py-1.5 rounded-[8px] border-0 outline-none
                                       disabled:opacity-40 disabled:cursor-not-allowed
                                       cursor-pointer">
                            <option value="Borrowed">Dipinjam</option>
                            <option value="Returned">Dikembalikan</option>
                            <option value="lost">Lost</option>
                            <option value="Overdue">Terlambat</option>
                        </select>
                    </div>
                </template>
            </div>

            {{-- Form hidden + tombol submit --}}
            <form :action="'{{ route('return.confirm') }}'" method="POST" x-ref="returnForm">
                @csrf
                <input type="hidden" name="transaction_id" :value="selectedTransactionId">
                <input type="hidden" name="class_id" value="{{ $classId }}">

                <template x-for="student in selectedDetails" :key="student.detail_id">
                    <div>
                        <input type="hidden" :name="'details[' + student.detail_id + '][detail_id]'"
                            :value="student.detail_id">
                        <input type="hidden" :name="'details[' + student.detail_id + '][status]'"
                            :value="student.status"> {{-- ← sekarang ambil langsung dari students array --}}
                    </div>
                </template>

                <button type="submit" :disabled="selectedDetails.length === 0 || !selectedTransactionId"
                    class="w-full mt-6 font-outfit font-bold text-lg text-white
                               bg-primary hover:bg-primary-dark
                               disabled:opacity-40 disabled:cursor-not-allowed
                               py-[18px] rounded-[12px]
                               transition-colors duration-200
                               shadow-[0px_4px_15px_0px_rgba(43,122,120,0.3)]">
                    Konfirmasi Pengembalian
                </button>
            </form>
        </div>
    </div>

</x-step-layout>

<script>
    // function returnStep6() {
    //     return {
    //         selectedTransactionId: null,
    //         students: [],
    //         selectedDetails: [],
    //         loading: false,

    //         get notReturnedCount() {
    //             return this.students.filter(s => s.status !== 'Returned' && s.status !== 'lost').length;
    //         },

    //         async selectBook(transactionId) {
    //             this.selectedTransactionId = transactionId;
    //             this.selectedDetails = [];
    //             this.loading = true;

    //             try {
    //                 const res = await fetch(`{{ route('return.students') }}?transaction_id=${transactionId}`);
    //                 this.students = await res.json();
    //             } catch (e) {
    //                 console.error('Gagal memuat data siswa', e);
    //             } finally {
    //                 this.loading = false;
    //             }
    //         },

    //         isSelected(detailId) {
    //             return this.selectedDetails.some(d => d.detail_id === detailId);
    //         },

    //         toggleStudent(student) {
    //             const idx = this.selectedDetails.findIndex(d => d.detail_id === student.detail_id);
    //             if (idx >= 0) {
    //                 this.selectedDetails.splice(idx, 1);
    //             } else {
    //                 this.selectedDetails.push({
    //                     detail_id: student.detail_id,
    //                     status: student.status
    //                 });
    //             }
    //         },

    //         selectAll() {
    //             this.selectedDetails = this.students.map(s => ({
    //                 detail_id: s.detail_id,
    //                 status: s.status,
    //             }));
    //         },

    //         deselectAll() {
    //             this.selectedDetails = [];
    //         },
    //     }
    // }

    function returnStep6() {
        return {
            selectedTransactionId: null,
            students: [],
            selectedDetailIds: [], // ← simpan ID saja, bukan object

            get selectedDetails() {
                // Computed — ambil data terbaru dari students array
                return this.students.filter(s => this.selectedDetailIds.includes(s.detail_id));
            },

            get notReturnedCount() {
                return this.students.filter(s => s.status !== 'Returned' && s.status !== 'lost').length;
            },

            async selectBook(transactionId) {
                this.selectedTransactionId = transactionId;
                this.selectedDetailIds = [];
                this.loading = true;

                try {
                    const res = await fetch(`{{ route('return.students') }}?transaction_id=${transactionId}`);
                    this.students = await res.json();
                } catch (e) {
                    console.error('Gagal memuat data siswa', e);
                } finally {
                    this.loading = false;
                }
            },

            isSelected(detailId) {
                return this.selectedDetailIds.includes(detailId);
            },

            toggleStudent(student) {
                const idx = this.selectedDetailIds.indexOf(student.detail_id);
                if (idx >= 0) {
                    this.selectedDetailIds.splice(idx, 1);
                } else {
                    this.selectedDetailIds.push(student.detail_id);
                }
            },

            selectAll() {
                this.selectedDetailIds = this.students.map(s => s.detail_id);
            },

            deselectAll() {
                this.selectedDetailIds = [];
            },
        }
    }
</script>
