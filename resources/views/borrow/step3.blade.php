<x-step-layout title="Pilih Kelas" :currentStep="3">

    <div class="flex flex-wrap gap-5 justify-center w-full">
        @foreach ($classes as $class)
            <x-major-card :href="route('borrow.step4', [
                'grade' => $grade,
                'major_id' => $major->id,
                'class_id' => $class->id,
            ])" :code="$class->class_name" :name="$class->class_name" />
        @endforeach
    </div>

</x-step-layout>
