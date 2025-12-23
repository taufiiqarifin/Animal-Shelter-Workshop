<div class="bg-white rounded-lg shadow p-6 mb-8">
    <form method="GET" action="{{ route('animal-management.index') }}">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..." class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Species</label>
                <select name="species" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">All</option>
                    <option value="Dog" {{ request('species') == 'Dog' ? 'selected' : '' }}>Dog</option>
                    <option value="Cat" {{ request('species') == 'Cat' ? 'selected' : '' }}>Cat</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Health Condition</label>
                <select name="health_details" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">All</option>
                    <option value="Healthy" {{ request('health_details') == 'Healthy' ? 'selected' : '' }}>Healthy</option>
                    <option value="Sick" {{ request('health_details') == 'Sick' ? 'selected' : '' }}>Sick</option>
                    <option value="Need Observation" {{ request('health_details') == 'Need Observation' ? 'selected' : '' }}>Need Observation</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Adoption Status</label>
                <select name="adoption_status" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">All</option>
                    <option value="Not Adopted" {{ request('adoption_status') == 'Not Adopted' ? 'selected' : '' }}>Available</option>
                    <option value="Adopted" {{ request('adoption_status') == 'Adopted' ? 'selected' : '' }}>Adopted</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                <select name="gender" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">All</option>
                    <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex justify-between items-center">
            <button type="submit" class="bg-purple-700 text-white px-6 py-2 rounded-lg hover:bg-purple-800 transition">Apply Filters</button>
            <p class="text-gray-600">Showing <span class="font-semibold">{{ $animals->total() }}</span> animals</p>
        </div>
    </form>
</div>
