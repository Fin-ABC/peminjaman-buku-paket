<x-step-layout title="Pilih Semester" :currentStep="4">
    <div class="flex gap-6 justify-center w-full" x-data="{ selected: null }">

        <a href="{{ route('return.step5', [
                'level'    => $level,
                'major_id' => $majorId,
                'class_id' => $classId,
                'semester' => 'odd',
            ]) }}"
           @click="selected = 'odd'"
           :class="selected === 'odd'
               ? 'bg-primary border-primary text-white shadow-[0px_8px_25px_0px_rgba(43,122,120,0.35)]'
               : 'bg-white border-accent text-primary hover:shadow-[0px_8px_25px_0px_rgba(43,122,120,0.2)] hover:-translate-y-1'"
           class="flex items-center justify-center min-w-[190px] h-[102px] px-12
                  border-2 rounded-[16px] cursor-pointer font-outfit font-extrabold text-[2rem]
                  transition-all duration-200 shadow-[0px_4px_15px_0px_rgba(43,122,120,0.1)]">
            Ganjil
        </a>

        <a href="{{ route('return.step5', [
                'level'    => $level,
                'major_id' => $majorId,
                'class_id' => $classId,
                'semester' => 'even',
            ]) }}"
           @click="selected = 'even'"
           :class="selected === 'even'
               ? 'bg-primary border-primary text-white shadow-[0px_8px_25px_0px_rgba(43,122,120,0.35)]'
               : 'bg-white border-accent text-primary hover:shadow-[0px_8px_25px_0px_rgba(43,122,120,0.2)] hover:-translate-y-1'"
           class="flex items-center justify-center min-w-[190px] h-[102px] px-12
                  border-2 rounded-[16px] cursor-pointer font-outfit font-extrabold text-[2rem]
                  transition-all duration-200 shadow-[0px_4px_15px_0px_rgba(43,122,120,0.1)]">
            Genap
        </a>

    </div>
</x-step-layout>
