<x-step-layout title="Pilih Jurusan" :currentStep="2">

    <div class="flex flex-wrap gap-5 justify-center w-full">
        @foreach ($majors as $major)
            <x-major-card
                :href="route('borrow.step3', ['grade' => $grade, 'major_id' => $major->id])"
                :code="$major->major_code"
                :name="$major->major_name" />
        @endforeach
    </div>

</x-step-layout>
