<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <h1>Enter the parking</h1>
                    <form action="{{ route('process-entry') }}" method="post">
                        @csrf
                        <p>
                            <label for="vehicle_category">Vehicle Category:</label>
                            <select id="vehicle_category" name="vehicle_category_id" required>
                                <option value="" disabled selected>Select a category</option>
                                @foreach ($vehicleCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </p>
                        <p>
                            <label for="plate_number">License Plate Number:</label>
                            <input type="text" id="plate_number" name="plate_number" required>
                        </p>
                        <p>
                            <label for="has_discount">Do you have a discount card?</label>
                            <input type="checkbox" id="has_discount" name="has_discount" />
                        </p>
                        <p class="discountCard hidden">
                            <label for="discount_card">Enter discount card code:</label>
                            <input type="text" id="discount_card" name="discount_card" placeholder="please enter valid card">
                        </p>
                        <button type="submit">Enter Parking Lot</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
