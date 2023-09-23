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
                    <h1>Exit the parking</h1>
                    <form method="POST" action="{{ route('api-exit-parking') }}">
                        @csrf
                        <div class="form-group">
                            <label for="plate_number">Plate number of the vehicle:</label>
                            <input type="text" id="plate_number" name="plate_number" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Exit Parking Lot</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
