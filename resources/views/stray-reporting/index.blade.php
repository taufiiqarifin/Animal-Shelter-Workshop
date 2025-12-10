<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stray Animal Reports - Stray Animals Shelter</title>

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
        <h1 class="text-4xl font-bold text-white mb-2">
            <span class="text-4xl md:text-5xl">📋</span>
            Stray Animal Reports
        </h1>
        <p class="text-purple-100">View and manage all submitted reports</p>
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

    @if($reports->isEmpty())
        <div class="bg-white rounded-2xl shadow-2xl p-12 text-center">
            <div class="text-6xl mb-4">🐾</div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No reports yet</h3>
            <p class="text-gray-600 mb-6 text-lg">No stray animal reports have been submitted.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Table Container with Horizontal Scroll -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-purple-600 to-purple-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            Report #
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            City/State
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            Submitted
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            Images
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reports as $report)
                        <tr class="hover:bg-purple-50 transition duration-150 cursor-pointer" onclick="window.location='{{ route('reports.show', $report->id) }}'">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-lg mr-2">📍</span>
                                    <span class="text-sm font-bold text-gray-900">#{{ $report->id }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($report->report_status == 'Pending') bg-yellow-100 text-yellow-800
                                            @elseif($report->report_status == 'In Progress') bg-blue-100 text-blue-800
                                            @elseif($report->report_status == 'Resolved') bg-green-100 text-green-800
                                            @endif">
                                            {{ $report->report_status }}
                                        </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $report->address }}">
                                    {{ $report->address }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $report->latitude }}, {{ $report->longitude }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $report->city }}</div>
                                <div class="text-xs text-gray-500">{{ $report->state }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $report->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $report->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($report->images->count() > 0)
                                    <div class="flex items-center">
                                        <span class="text-purple-600 font-semibold text-sm mr-2">{{ $report->images->count() }}</span>
                                        <button onclick="event.stopPropagation(); showImagesModal({{ $report->id }}, {{ json_encode($report->images->map(fn($img) => asset('storage/' . $img->image_path))) }})"
                                                class="text-purple-600 hover:text-purple-800 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">No images</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex gap-2" onclick="event.stopPropagation()">
                                    <a href="{{ route('reports.show', $report->id) }}"
                                       class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-xs font-semibold rounded-lg hover:bg-purple-700 transition duration-300 shadow">
                                        View
                                    </a>
                                    <button onclick="showMapModal({{ $report->latitude }}, {{ $report->longitude }}, '{{ $report->address }}')"
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
            {{ $reports->links() }}
        </div>
    @endif
</div>

{{-- Map Modal --}}
<div id="mapModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closeMapModal()">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Location Map</h3>
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

{{-- Images Modal --}}
<div id="imagesModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closeImagesModal()">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-auto" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center p-6 border-b border-gray-200 sticky top-0 bg-white z-10">
            <h3 class="text-xl font-bold text-gray-900">Attached Images</h3>
            <button onclick="closeImagesModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="imagesContainer" class="p-6 grid grid-cols-2 sm:grid-cols-3 gap-4"></div>
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

    // Images modal functions
    function showImagesModal(reportId, images) {
        const container = document.getElementById('imagesContainer');
        container.innerHTML = '';

        images.forEach(imagePath => {
            const div = document.createElement('div');
            div.className = 'relative group cursor-pointer';
            div.innerHTML = `
                    <img src="${imagePath}"
                         alt="Report Image"
                         class="w-full h-48 object-cover rounded-lg shadow-md hover:shadow-xl transition"
                         onclick="openFullImage('${imagePath}')">
                `;
            container.appendChild(div);
        });

        document.getElementById('imagesModal').classList.remove('hidden');
    }

    function closeImagesModal() {
        document.getElementById('imagesModal').classList.add('hidden');
    }

    function openFullImage(imageSrc) {
        window.open(imageSrc, '_blank');
    }

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMapModal();
            closeImagesModal();
        }
    });
</script>
</body>
</html>
