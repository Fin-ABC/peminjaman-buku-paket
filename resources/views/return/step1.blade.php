<x-step-layout title="Pilih Tingkat" :currentStep="1" action="Pengembalian">
    <div class="flex gap-6 items-center justify-center w-full">
        <x-choice-card :href="route('return.step2', ['grade' => 'X'])">X</x-choice-card>
        <x-choice-card :href="route('return.step2', ['grade' => 'XI'])">XI</x-choice-card>
        <x-choice-card :href="route('return.step2', ['grade' => 'XII'])">XII</x-choice-card>
    </div>
</x-step-layout>
