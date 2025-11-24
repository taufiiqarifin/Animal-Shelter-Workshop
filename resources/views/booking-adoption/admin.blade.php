<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
@include('navbar')

<div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-5xl font-bold mb-4">All Bookings</h1>
                <p class="text-xl text-purple-100">Admin view of all appointment bookings</p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-600 text-green-700 p-4 rounded-lg mb-8 shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="font-semibold">{{ session('success') }}</p>
            </div>
        </div>
    @endif

        <!-- Stats Cards as Filter Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-10">
            <!-- Total Bookings Card -->
            <a href="{{ route('bookings.index-admin') }}"
               class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer {{ !request('status') ? 'ring-4 ring-purple-500' : '' }}">
                <div class="text-5xl mb-4">📅</div>
                <p class="text-4xl font-bold text-purple-700 mb-2">{{ $totalBookings }}</p>
                <p class="text-gray-600">Total Bookings</p>
                @if(!request('status'))
                    <div class="mt-2 text-xs text-purple-600 font-semibold">● Active</div>
                @endif
            </a>

            <!-- Pending Card -->
            <a href="{{ route('bookings.index-admin', ['status' => 'Pending']) }}"
               class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer {{ request('status') == 'Pending' ? 'ring-4 ring-yellow-500' : '' }}">
                <div class="text-5xl mb-4">⏳</div>
                <p class="text-4xl font-bold text-yellow-600 mb-2">{{ $statusCounts['Pending'] ?? 0 }}</p>
                <p class="text-gray-600">Pending</p>
                @if(request('status') == 'Pending')
                    <div class="mt-2 text-xs text-yellow-600 font-semibold">● Active</div>
                @endif
            </a>

            <!-- Confirmed Card -->
            <a href="{{ route('bookings.index-admin', ['status' => 'Confirmed']) }}"
               class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer {{ request('status') == 'Confirmed' ? 'ring-4 ring-blue-500' : '' }}">
                <div class="text-5xl mb-4">✅</div>
                <p class="text-4xl font-bold text-blue-600 mb-2">{{ $statusCounts['Confirmed'] ?? 0 }}</p>
                <p class="text-gray-600">Confirmed</p>
                @if(request('status') == 'Confirmed')
                    <div class="mt-2 text-xs text-blue-600 font-semibold">● Active</div>
                @endif
            </a>

            <!-- Completed Card -->
            <a href="{{ route('bookings.index-admin', ['status' => 'Completed']) }}"
               class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer {{ request('status') == 'Completed' ? 'ring-4 ring-green-500' : '' }}">
                <div class="text-5xl mb-4">🎉</div>
                <p class="text-4xl font-bold text-green-600 mb-2">{{ $statusCounts['Completed'] ?? 0 }}</p>
                <p class="text-gray-600">Completed</p>
                @if(request('status') == 'Completed')
                    <div class="mt-2 text-xs text-green-600 font-semibold">● Active</div>
                @endif
            </a>

            <!-- Cancelled Card -->
            <a href="{{ route('bookings.index-admin', ['status' => 'Cancelled']) }}"
               class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 cursor-pointer {{ request('status') == 'Cancelled' ? 'ring-4 ring-red-500' : '' }}">
                <div class="text-5xl mb-4">❌</div>
                <p class="text-4xl font-bold text-red-600 mb-2">{{ $statusCounts['Cancelled'] ?? 0 }}</p>
                <p class="text-gray-600">Cancelled</p>
                @if(request('status') == 'Cancelled')
                    <div class="mt-2 text-xs text-red-600 font-semibold">● Active</div>
                @endif
            </a>
        </div>


    @if($bookings->isEmpty())
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <div class="mb-6">
                <svg class="w-32 h-32 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="text-3xl font-bold text-gray-700 mb-3">No Bookings Found</h3>
            <p class="text-gray-500 text-lg">There are no bookings in the system yet.</p>
        </div>
    @else
        <h2 class="text-3xl font-bold text-gray-800 mb-6">All Appointments</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($bookings as $booking)
                @php
                    $statusKey = strtolower($booking->status);
                    $statusColors = [
                        'pending' => 'from-yellow-300 to-yellow-400',
                        'confirmed' => 'from-blue-300 to-blue-400',
                        'completed' => 'from-green-300 to-green-400',
                        'cancelled' => 'from-red-300 to-red-400',
                    ];
                    $statusEmojis = [
                        'pending' => '⏳',
                        'confirmed' => '✅',
                        'completed' => '🎉',
                        'cancelled' => '❌',
                    ];
                    $badgeColors = [
                        'pending' => 'bg-yellow-500',
                        'confirmed' => 'bg-blue-500',
                        'completed' => 'bg-green-500',
                        'cancelled' => 'bg-red-500',
                    ];
                @endphp
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-2xl transition duration-300">
                    <div class="relative">
                        <div class="h-32 bg-gradient-to-br {{ $statusColors[$statusKey] ?? 'from-gray-300 to-gray-400' }} flex items-center justify-center">
                            <span class="text-7xl">{{ $statusEmojis[$statusKey] ?? '📅' }}</span>
                        </div>
                        <div class="absolute top-4 right-4 {{ $badgeColors[$statusKey] ?? 'bg-gray-500' }} text-white px-3 py-1 rounded-full text-sm font-semibold">
                            {{ ucfirst($booking->status) }}
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-2xl font-bold text-gray-800">Booking #{{ $booking->id }}</h3>
                        </div>

                        <div class="space-y-3 mb-4">
                            <!-- User Info -->
                            @if($booking->user)
                                <div class="flex items-start">
                                    <div class="bg-green-100 rounded-full p-2 mr-3 flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium">User</p>
                                        <p class="font-semibold text-gray-800">{{ $booking->user->name }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-start">
                                <div class="bg-purple-100 rounded-full p-2 mr-3 flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Appointment Date</p>
                                    <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($booking->appointment_date)->format('F d, Y') }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="bg-purple-100 rounded-full p-2 mr-3 flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Time</p>
                                    <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($booking->appointment_time)->format('h:i A') }}</p>
                                </div>
                            </div>

                            @if($booking->animals->isNotEmpty())
                                <div class="flex items-start">
                                    <div class="bg-purple-100 rounded-full p-2 mr-3 flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 font-medium">Animals ({{ $booking->animals->count() }})</p>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($booking->animals->take(3) as $animal)
                                                <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded-full text-xs font-medium">
                                                    {{ $animal->name }}
                                                </span>
                                            @endforeach
                                            @if($booking->animals->count() > 3)
                                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs font-medium">
                                                    +{{ $booking->animals->count() - 3 }} more
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($booking->adoptions->isNotEmpty())
                                <div class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200 cursor-pointer hover:bg-green-100 transition"
                                     onclick="openAdoptionModal({{ $booking->id }})">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm font-semibold text-green-800">
                                                Adoption Record Available
                                            </span>
                                        </div>
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-2 mt-6">
                            <button type="button"
                                    onclick="openBookingModal({{ $booking->id }})"
                                    class="flex-1 text-center bg-purple-700 hover:bg-purple-800 text-white py-3 rounded-lg font-semibold transition duration-300">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Booking Details Modal -->
                <div id="bookingModal-{{ $booking->id }}" class="modal-backdrop hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-7xl w-full max-h-[100vh] overflow-y-auto">
                        <!-- Modal Header -->
                        <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-6 text-white sticky top-0 z-10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-2xl font-bold">Booking Details #{{ $booking->id }}</h2>
                                    <p class="text-purple-100 text-sm">Admin View</p>
                                </div>
                                <button onclick="closeModal('bookingModal-{{ $booking->id }}')" class="text-white hover:text-gray-200">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-6 space-y-6">
                            <!-- Status Badge -->
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-bold text-gray-800">Booking Status</h3>
                                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $badgeColors[$statusKey] ?? 'bg-gray-500' }} text-white">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>

                            <!-- User Information -->
                            @if($booking->user)
                                <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-300 rounded-xl p-6">
                                    <h3 class="font-bold text-gray-800 mb-4 text-xl">User Information</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Name</p>
                                            <p class="text-gray-800 font-medium">{{ $booking->user->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Email</p>
                                            <p class="text-gray-800 font-medium">{{ $booking->user->email }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Phone</p>
                                            <p class="text-gray-800 font-medium">{{ $booking->user->phoneNum ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Animals Section -->
                            @if($booking->animals->isNotEmpty())
                                <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-300 rounded-xl p-6">
                                    <h3 class="font-bold text-gray-800 mb-4 text-xl">Animals in Booking ({{ $booking->animals->count() }})</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($booking->animals as $animal)
                                            <div class="bg-white rounded-xl p-4 shadow-md">
                                                @if($animal->images && $animal->images->count() > 0)
                                                    <img src="{{ asset('storage/' . $animal->images->first()->image_path) }}"
                                                         alt="{{ $animal->name }}"
                                                         class="w-full h-32 object-cover rounded-lg mb-3">
                                                @endif
                                                <h4 class="font-bold text-gray-800 mb-2">{{ $animal->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $animal->species }} • {{ $animal->age }} • {{ $animal->gender }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Appointment Details -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-300 rounded-xl p-6">
                                <h3 class="font-bold text-gray-800 mb-4 text-xl">Appointment Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Date</p>
                                        <p class="text-gray-800 font-bold text-lg">{{ \Carbon\Carbon::parse($booking->appointment_date)->format('F d, Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Time</p>
                                        <p class="text-gray-800 font-bold text-lg">{{ \Carbon\Carbon::parse($booking->appointment_time)->format('h:i A') }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($booking->adoptions->isNotEmpty())
                                <button onclick="openAdoptionModal({{ $booking->id }})"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition">
                                    View Adoption Records
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Adoption Records Modal -->
                @if($booking->adoptions->isNotEmpty())
                    <div id="adoptionModal-{{ $booking->id }}" class="modal-backdrop hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60] p-4">
                        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                            <!-- Modal Header -->
                            <div class="bg-gradient-to-r from-green-600 to-green-700 p-6 text-white sticky top-0 z-10">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h2 class="text-2xl font-bold">Adoption Records</h2>
                                        <p class="text-green-100 text-sm">Booking #{{ $booking->id }}</p>
                                    </div>
                                    <button onclick="closeAdoptionModal({{ $booking->id }})" class="text-white hover:text-gray-200">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Modal Body -->
                            <div class="p-6 space-y-6">
                                @foreach($booking->adoptions as $adoption)
                                    <div class="bg-green-50 border-2 border-green-300 rounded-xl p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-xl font-bold text-gray-800">Adoption #{{ $adoption->id }}</h3>
                                            <span class="px-3 py-1 bg-green-600 text-white rounded-full text-sm font-semibold">Completed</span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-sm text-gray-500 font-medium">Adoption Fee</p>
                                                <p class="text-2xl font-bold text-green-600">RM {{ number_format($adoption->fee, 2) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500 font-medium">Date</p>
                                                <p class="text-lg font-semibold text-gray-800">{{ $adoption->created_at->format('F d, Y') }}</p>
                                            </div>
                                        </div>

                                        <div class="bg-white rounded-lg p-4 mb-4">
                                            <p class="text-sm text-gray-500 font-medium mb-1">Remarks</p>
                                            <p class="text-gray-800">{{ $adoption->remarks ?? 'No remarks' }}</p>
                                        </div>

                                        @if($adoption->transaction)
                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                                <h4 class="font-semibold text-gray-800 mb-2">Payment Information</h4>
                                                <div class="grid grid-cols-2 gap-2 text-sm">
                                                    <div>
                                                        <span class="text-gray-600">Amount:</span>
                                                        <span class="font-medium text-gray-800">RM {{ number_format($adoption->transaction->amount, 2) }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600">Status:</span>
                                                        <span class="font-medium text-green-600">{{ $adoption->transaction->status }}</span>
                                                    </div>
                                                    @if($adoption->transaction->bill_code)
                                                        <div>
                                                            <span class="text-gray-600">Bill Code:</span>
                                                            <span class="font-medium text-gray-800">{{ $adoption->transaction->bill_code }}</span>
                                                        </div>
                                                    @endif
                                                    @if($adoption->transaction->reference_no)
                                                        <div>
                                                            <span class="text-gray-600">Reference:</span>
                                                            <span class="font-medium text-gray-800">{{ $adoption->transaction->reference_no }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                <button onclick="closeAdoptionModal({{ $booking->id }})"
                                        class="w-full bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-lg font-semibold transition">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    @if($bookings->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $bookings->links() }}
        </div>
    @endif
</div>

<script>
    function openBookingModal(bookingId) {
        document.getElementById('bookingModal-' + bookingId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openAdoptionModal(bookingId) {
        document.getElementById('adoptionModal-' + bookingId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAdoptionModal(bookingId) {
        document.getElementById('adoptionModal-' + bookingId).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop')) {
            e.target.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-backdrop:not(.hidden)').forEach(modal => {
                modal.classList.add('hidden');
            });
            document.body.style.overflow = 'auto';
        }
    });
</script>
</body>
</html>
