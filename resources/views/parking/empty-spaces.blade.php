<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1>{{ __("Currently in ") }} {{ $parkingLot['name'] }} {{ __(" there are: ") }} <strong>{{ $parkingLot['empty_slots'] }}</strong> {{ __("empty spaces") }}</h1>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
