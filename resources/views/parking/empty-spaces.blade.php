<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1>{{ __("Currently there are: ") }} <strong>{{ $parkingLotEmptySlots }}</strong> {{ __("empty spaces") }}</h1>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
