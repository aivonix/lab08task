<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1>Enter Vehicle Number</h1>
                    <form action="{{ route('api-check-vehicle-expense') }}" method="POST">
                        @csrf
                        <input type="text" name="vehicle_number" pattern="[A-Z0-9]{10}" maxlength="10" required>
                        <button type="submit">Check Expense</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
