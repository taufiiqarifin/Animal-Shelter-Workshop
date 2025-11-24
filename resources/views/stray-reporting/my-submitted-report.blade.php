<!-- My Reports Modal -->
<div id="myReportsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-[1400px] max-w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white p-6 sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">📋</span>
                    <div>
                        <h2 class="text-2xl font-bold">My Reports</h2>
                        <p class="text-purple-100 text-sm">View all your submitted reports</p>
                    </div>
                </div>
                <button onclick="closeMyReportsModal()" class="text-white hover:text-gray-200 transition">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            @if($userReports->isEmpty())
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🐾</div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">No reports yet</h3>
                    <p class="text-gray-600 mb-6">You haven't submitted any reports</p>
                    <button onclick="closeMyReportsModal()" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        Close
                    </button>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($userReports as $report)
                        <div class="border border-gray-200 rounded-xl p-5 hover:shadow-lg transition">
                            <!-- Report Header -->
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xl">📍</span>
                                        <h3 class="text-lg font-bold text-gray-800">Report #{{ $report->id }}</h3>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        {{ $report->created_at->format('M d, Y - h:i A') }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if($report->report_status == 'Pending') bg-yellow-100 text-yellow-700
                                    @elseif($report->report_status == 'In Progress') bg-blue-100 text-blue-700
                                    @elseif($report->report_status == 'Resolved') bg-green-100 text-green-700
                                    @endif">
                                    {{ $report->report_status }}
                                </span>
                            </div>

                            <!-- Report Content -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Location Info -->
                                <div class="bg-purple-50 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-map-marker-alt text-purple-600 mr-2"></i>
                                        Location
                                    </h4>
                                    <div class="space-y-2 text-sm">
                                        <div>
                                            <span class="text-gray-600">Address:</span>
                                            <p class="text-gray-800 font-medium">{{ $report->address }}</p>
                                        </div>
                                        <div class="flex gap-4">
                                            <div>
                                                <span class="text-gray-600">City:</span>
                                                <p class="text-gray-800 font-medium">{{ $report->city }}</p>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">State:</span>
                                                <p class="text-gray-800 font-medium">{{ $report->state }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Map Preview -->
                                <div>
                                    <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                        <i class="fas fa-map text-purple-600 mr-2"></i>
                                        Map Preview
                                    </h4>
                                    <div id="mini-map-{{ $report->id }}"
                                         class="rounded-lg border border-gray-200"
                                         style="height: 150px;"
                                         data-lat="{{ $report->latitude }}"
                                         data-lng="{{ $report->longitude }}"></div>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($report->description)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <h4 class="font-semibold text-gray-800 mb-2 text-sm flex items-center">
                                        <i class="fas fa-comment-alt text-purple-600 mr-2"></i>
                                        Description
                                    </h4>
                                    <p class="text-gray-700 text-sm">{{ $report->description }}</p>
                                </div>
                            @endif

                            <!-- Images Preview -->
                            @if($report->images->count() > 0)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <h4 class="font-semibold text-gray-800 mb-3 text-sm flex items-center">
                                        <i class="fas fa-images text-purple-600 mr-2"></i>
                                        Images ({{ $report->images->count() }})
                                    </h4>
                                    <div class="flex gap-2 overflow-x-auto pb-2">
                                        @foreach($report->images->take(4) as $image)
                                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                                 alt="Report Image"
                                                 class="w-20 h-20 object-cover rounded-lg cursor-pointer hover:opacity-75 transition shadow-sm flex-shrink-0"
                                                 onclick="openImageModal('{{ asset('storage/' . $image->image_path) }}')">
                                        @endforeach
                                        @if($report->images->count() > 4)
                                            <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center text-gray-600 text-sm font-semibold flex-shrink-0">
                                                +{{ $report->images->count() - 4 }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Pagination in Modal -->
                @if($userReports->hasPages())
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        {{ $userReports->appends(['open_modal' => 1])->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Image Modal (Full Size Preview) -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-90 z-[60] flex items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-6xl max-h-full">
        <button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <img id="modalImage" src="" alt="Full size image" class="max-w-full max-h-screen rounded-xl shadow-2xl">
    </div>
</div>

<script>
    let miniMaps = {};
    let mapsInitialized = false;

    // Initialize all mini maps
    function initializeMiniMaps() {
        if (mapsInitialized) return;

        // Find all map containers
        const mapContainers = document.querySelectorAll('[id^="mini-map-"]');

        mapContainers.forEach(container => {
            const reportId = container.id.replace('mini-map-', '');
            const lat = parseFloat(container.dataset.lat);
            const lng = parseFloat(container.dataset.lng);

            // Check if container is visible and has dimensions
            if (container.offsetWidth === 0 || container.offsetHeight === 0) {
                return;
            }

            // Only initialize if not already done
            if (!miniMaps[reportId]) {
                try {
                    miniMaps[reportId] = L.map(container.id, {
                        zoomControl: false,
                        dragging: false,
                        scrollWheelZoom: false,
                        doubleClickZoom: false,
                        touchZoom: false
                    }).setView([lat, lng], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(miniMaps[reportId]);

                    L.marker([lat, lng]).addTo(miniMaps[reportId]);

                    // Force map to recalculate size
                    setTimeout(() => {
                        miniMaps[reportId].invalidateSize();
                    }, 100);
                } catch (error) {
                    console.error('Error initializing map for report ' + reportId, error);
                }
            }
        });

        mapsInitialized = true;
    }

    // Open My Reports Modal
    function openMyReportsModal() {
        const modal = document.getElementById('myReportsModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Reset flag to allow re-initialization
        mapsInitialized = false;

        // Initialize maps after modal is fully visible
        setTimeout(() => {
            initializeMiniMaps();
        }, 300);
    }

    // Close My Reports Modal
    function closeMyReportsModal() {
        document.getElementById('myReportsModal').classList.add('hidden');
        document.body.style.overflow = 'auto';

        // Clean up maps
        Object.keys(miniMaps).forEach(key => {
            if (miniMaps[key]) {
                miniMaps[key].remove();
                delete miniMaps[key];
            }
        });
        mapsInitialized = false;
    }

    // Image modal functions
    function openImageModal(imageSrc) {
        event.stopPropagation();
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('myReportsModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeMyReportsModal();
        }
    });

    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const imageModal = document.getElementById('imageModal');
            const reportsModal = document.getElementById('myReportsModal');

            if (!imageModal.classList.contains('hidden')) {
                closeImageModal();
            } else if (!reportsModal.classList.contains('hidden')) {
                closeMyReportsModal();
            }
        }
    });

    // Re-open modal automatically if pagination triggers a reload with ?open_modal=1
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('open_modal') == 1) {
        window.addEventListener('load', () => {
            openMyReportsModal();
        });
    }
</script>
