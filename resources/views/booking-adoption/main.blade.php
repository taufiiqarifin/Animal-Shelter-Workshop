<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Stray Animal Shelter</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    @include('navbar')

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-5xl font-bold mb-4">My Bookings</h1>
                    <p class="text-xl text-purple-100">View and manage your appointment bookings</p>
                </div>
                <div class="mt-6 md:mt-0">
                    <a href="{{ route('bookings.create') }}" class="inline-flex items-center gap-2 bg-white text-purple-700 px-8 py-3 rounded-lg font-semibold hover:bg-purple-50 transition duration-300 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>New Booking</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- Success/Error Messages -->
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

        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-600 text-red-700 p-4 rounded-lg mb-8 shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="font-semibold">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="text-5xl mb-4">📅</div>
                <p class="text-4xl font-bold text-purple-700 mb-2">{{ $bookings->count() }}</p>
                <p class="text-gray-600">Total Bookings</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="text-5xl mb-4">⏳</div>
                <p class="text-4xl font-bold text-yellow-600 mb-2">{{ $bookings->where('status', 'pending')->count() }}</p>
                <p class="text-gray-600">Pending</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="text-5xl mb-4">✅</div>
                <p class="text-4xl font-bold text-blue-600 mb-2">{{ $bookings->where('status', 'confirmed')->count() }}</p>
                <p class="text-gray-600">Confirmed</p>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="text-5xl mb-4">🎉</div>
                <p class="text-4xl font-bold text-green-600 mb-2">{{ $bookings->where('status', 'completed')->count() }}</p>
                <p class="text-gray-600">Completed</p>
            </div>
        </div>

        <!-- Bookings List -->
        @if($bookings->isEmpty())
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <div class="mb-6">
                    <svg class="w-32 h-32 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-3xl font-bold text-gray-700 mb-3">No Bookings Yet</h3>
                <p class="text-gray-500 text-lg mb-8">You haven't made any bookings. Start by creating your first appointment!</p>
                <a href="{{ route('bookings.create') }}" class="inline-flex items-center gap-2 bg-purple-700 hover:bg-purple-800 text-white px-8 py-3 rounded-lg font-semibold transition duration-300 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Create First Booking</span>
                </a>
            </div>
        @else
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Your Appointments</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($bookings as $booking)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-2xl transition duration-300">
                        
                        <!-- Status Header -->
                        <div class="relative">
                            @php
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
                                $statusColor = $statusColors[$booking->status] ?? 'from-gray-300 to-gray-400';
                                $statusEmoji = $statusEmojis[$booking->status] ?? '📅';
                            @endphp
                            <div class="h-32 bg-gradient-to-br {{ $statusColor }} flex items-center justify-center">
                                <span class="text-7xl">{{ $statusEmoji }}</span>
                            </div>
                            @php
                                $badgeColors = [
                                    'pending' => 'bg-yellow-500',
                                    'confirmed' => 'bg-blue-500',
                                    'completed' => 'bg-green-500',
                                    'cancelled' => 'bg-red-500',
                                ];
                                $badgeColor = $badgeColors[$booking->status] ?? 'bg-gray-500';
                            @endphp
                            <div class="absolute top-4 right-4 {{ $badgeColor }} text-white px-3 py-1 rounded-full text-sm font-semibold">
                                {{ ucfirst($booking->status) }}
                            </div>
                        </div>

                        <!-- Booking Details -->
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-2xl font-bold text-gray-800">Booking #{{ $booking->id }}</h3>
                            </div>

                            <div class="space-y-3 mb-4">
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
                                        <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') }}</p>
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
                                            <p class="text-sm text-gray-500 font-medium">Animals</p>
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach($booking->animals as $animal)
                                                    <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded-full text-xs font-medium">
                                                        {{ $animal->name ?? 'Animal #' . $animal->id }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($booking->adoption)
                                    <div class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm font-semibold text-green-800">Adoption Confirmed</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2 mt-6">
                                <a href="{{ route('bookings.show', $booking->id) }}" class="flex-1 text-center bg-purple-700 hover:bg-purple-800 text-white py-3 rounded-lg font-semibold transition duration-300">
                                    View Details
                                </a>
                                
                                @if(in_array($booking->status, ['pending', 'confirmed']))
                                    <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-semibold transition duration-300">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Info Section -->
        <div class="mt-16 bg-gradient-to-r from-purple-700 to-purple-900 rounded-lg p-12 text-center text-white">
            <h2 class="text-3xl font-bold mb-4">Need Help with Your Booking?</h2>
            <p class="text-xl mb-6">Contact us if you have any questions about your appointments or need to make changes.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('bookings.create') }}" class="bg-white text-purple-700 px-8 py-3 rounded-lg font-semibold hover:bg-purple-50 transition duration-300 inline-block">
                    Book New Appointment
                </a>
                <a href="#" class="bg-purple-800 hover:bg-purple-900 text-white px-8 py-3 rounded-lg font-semibold transition duration-300 inline-block border-2 border-white">
                    Contact Support
                </a>
            </div>
        </div>
    </div>
</body>
</html>