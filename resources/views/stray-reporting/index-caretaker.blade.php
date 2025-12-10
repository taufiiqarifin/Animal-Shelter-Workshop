<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Assigned Rescues - Stray Animals Shelter</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Include Navbar -->
@include('navbar')

<div class="mb-8 bg-gradient-to-r from-purple-600 to-purple-800 shadow-lg p-8 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    <span class="text-4xl md:text-5xl">🚑</span>
                    My Assigned Rescues
                </h1>
                <p class="text-purple-100">Manage your assigned animal rescue missions</p>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-lg px-6 py-3">
                <p class="text-sm font-semibold text-white">Total Assigned: <span class="text-3xl">{{ $rescues->total() }}</span></p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto mt-10 p-4 md:p-6 pb-10">
    @if (session('success'))
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="bg-green-50 border-l-4 border-green-600 text-green-700 p-4">
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Filter Tabs -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="flex flex-wrap gap-2 p-4 border-b border-gray-200">
            <a href="{{ route('rescues.index') }}"
               class="px-4 py-2 rounded-lg font-semibold transition {{ !request('status') ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                All Rescues
            </a>
            <a href="{{ route('rescues.index', ['status' => 'Scheduled']) }}"
               class="px-4 py-2 rounded-lg font-semibold transition {{ request('status') == 'Scheduled' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Scheduled
            </a>
            <a href="{{ route('rescues.index', ['status' => 'In Progress']) }}"
               class="px-4 py-2 rounded-lg font-semibold transition {{ request('status') == 'In Progress' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                In Progress
            </a>
            <a href="{{ route('rescues.index', ['status' => 'Success']) }}"
               class="px-4 py-2 rounded-lg font-semibold transition {{ request('status') == 'Success' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Success
            </a>
            <a href="{{ route('rescues.index', ['status' => 'Failed']) }}"
               class="px-4 py-2 rounded-lg font-semibold transition {{ request('status') == 'Failed' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Failed
            </a>
        </div>
    </div>

    @if($rescues->isEmpty())
        <div class="bg-white rounded-2xl shadow-2xl p-12 text-center">
            <div class="text-6xl mb-4">📋</div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No rescues assigned yet</h3>
            <p class="text-gray-600 mb-6 text-lg">You don't have any rescue missions assigned at the moment.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Table Container with Horizontal Scroll -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-purple-600 to-purple-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            Rescue #
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            Report #
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            Rescue Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            Report Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            City/State
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            Scheduled Date
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            Assigned On
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rescues as $rescue)
                        <tr class="hover:bg-purple-50 transition duration-150 cursor-pointer" onclick="window.location='{{ route('rescues.show', $rescue->id) }}'">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-lg mr-2">🚨</span>
                                    <span class="text-sm font-bold text-gray-900">#{{ $rescue->id }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-lg mr-2">📍</span>
                                    <span class="text-sm font-semibold text-purple-600">#{{ $rescue->report->id }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($rescue->status == 'Scheduled') bg-yellow-100 text-yellow-800
                                            @elseif($rescue->status == 'In Progress') bg-blue-100 text-blue-800
                                            @elseif($rescue->status == 'Success') bg-green-100 text-green-800
                                            @elseif($rescue->status == 'Failed') bg-red-100 text-red-800
                                            @endif">
                                            {{ $rescue->status }}
                                        </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $rescue->report->report_status }}
                                        </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $rescue->report->address }}">
                                    {{ $rescue->report->address }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $rescue->report->latitude }}, {{ $rescue->report->longitude }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $rescue->report->city }}</div>
                                <div class="text-xs text-gray-500">{{ $rescue->report->state }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($rescue->date)
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($rescue->date)->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($rescue->date)->format('h:i A') }}</div>
                                @else
                                    <span class="text-gray-400 text-sm">Not scheduled</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $rescue->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $rescue->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex gap-2" onclick="event.stopPropagation()">
                                    <a href="{{ route('rescues.show', $rescue->id) }}"
                                       class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-xs font-semibold rounded-lg hover:bg-purple-700 transition duration-300 shadow">
                                        View
                                    </a>
                                    <button onclick="showMapModal({{ $rescue->report->latitude }}, {{ $rescue->report->longitude }}, '{{ $rescue->report->address }}')"
                                            class="inline-flex items-center px-3 py-2 bg-white border border-purple-600 text-purple-600 text-xs font-semibold rounded-lg hover:bg-purple-50 transition duration-300">
                                        Map
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-6 bg-white rounded-xl shadow-lg p-4">
            {{ $rescues->links() }}
        </div>
    @endif
</div>

{{-- Map Modal --}}
<div id="mapModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closeMapModal()">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Rescue Location Map</h3>
                <p id="mapModalAddress" class="text-sm text-gray-600 mt-1"></p>
            </div>
            <button onclick="closeMapModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="modalMap" class="w-full" style="height: 500px;"></div>
    </div>
</div>

{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let modalMapInstance = null;

    // Map modal functions
    function showMapModal(lat, lng, address) {
        document.getElementById('mapModalAddress').textContent = address;
        document.getElementById('mapModal').classList.remove('hidden');

        setTimeout(() => {
            if (modalMapInstance) {
                modalMapInstance.remove();
            }

            modalMapInstance = L.map('modalMap').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(modalMapInstance);

            L.marker([lat, lng]).addTo(modalMapInstance);
        }, 100);
    }

    function closeMapModal() {
        document.getElementById('mapModal').classList.add('hidden');
        if (modalMapInstance) {
            modalMapInstance.remove();
            modalMapInstance = null;
        }
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMapModal();
        }
    });
</script>
</body>
</html>
