{{-- Step 1: Booking Details --}}
<div class="space-y-6">

    {{-- Status Badge --}}
    <div class="flex justify-between items-center bg-gradient-to-r from-purple-50 to-purple-100 border-2 border-purple-200 rounded-xl p-6">
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Booking Status</h3>
            <p class="text-gray-600 text-sm">Current status of your booking</p>
        </div>
        @php
            $statusBadgeColors = [
                'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
                'confirmed' => 'bg-blue-100 text-blue-700 border-blue-300',
                'completed' => 'bg-green-100 text-green-700 border-green-300',
                'cancelled' => 'bg-red-100 text-red-700 border-red-300',
            ];
            $statusKey = strtolower($booking->status);
        @endphp
        <span class="px-6 py-3 rounded-full text-base font-semibold border-2 {{ $statusBadgeColors[$statusKey] ?? 'bg-gray-100 text-gray-700' }}">
            {{ $booking->status }}
        </span>
    </div>

    {{-- Appointment Details --}}
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-300 rounded-xl p-6">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center text-xl">
            <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Appointment Details
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-lg p-5 shadow-md">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Appointment Date</p>
                </div>
                <p class="text-gray-800 font-bold text-xl">
                    {{ \Carbon\Carbon::parse($booking->appointment_date)->format('F d, Y') }}
                </p>
            </div>
            <div class="bg-white rounded-lg p-5 shadow-md">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-xs font-semibold text-gray-500 uppercase">Appointment Time</p>
                </div>
                <p class="text-gray-800 font-bold text-xl">
                    {{ \Carbon\Carbon::parse($booking->appointment_time)->format('h:i A') }}
                </p>
            </div>
        </div>
        <div class="mt-4 bg-white rounded-lg p-5 shadow-md">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-xs font-semibold text-gray-500 uppercase">Booked On</p>
            </div>
            <p class="text-gray-800 font-medium text-lg">
                {{ $booking->created_at->format('F d, Y') }}
            </p>
        </div>
    </div>

    {{-- Animals in Booking --}}
    @if($booking->animals && $booking->animals->isNotEmpty())
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-300 rounded-xl p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center text-xl">
                <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Animals in this Booking
                <span class="ml-2 px-3 py-1 bg-purple-600 text-white rounded-full text-sm">{{ $booking->animals->count() }}</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($booking->animals as $animal)
                    <div class="bg-white rounded-xl p-4 shadow-md border-2 border-transparent hover:border-purple-400 transition">
                        @if($animal->images && $animal->images->count() > 0)
                            <img src="{{ $animal->images->first()->url }}"
                                 alt="{{ $animal->name }}"
                                 class="w-full h-40 object-cover rounded-lg mb-3">
                        @else
                            <div class="w-full h-40 bg-gradient-to-br from-purple-300 to-purple-400 rounded-lg flex items-center justify-center mb-3">
                                <span class="text-5xl">
                                    @if(strtolower($animal->species) == 'dog') 🐕
                                    @elseif(strtolower($animal->species) == 'cat') 🐈
                                    @else 🐾
                                    @endif
                                </span>
                            </div>
                        @endif
                        <div class="text-gray-800 font-semibold text-lg">{{ $animal->name }}</div>
                        <div class="text-gray-600 text-sm mt-1">{{ $animal->species }} • {{ $animal->age }} • {{ $animal->gender }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- User Information --}}
    @if($booking->user)
        <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-300 rounded-xl p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center text-xl">
                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Your Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Name</p>
                    <p class="text-gray-800 font-medium text-lg">{{ $booking->user->name }}</p>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Email</p>
                    <p class="text-gray-800 font-medium">{{ $booking->user->email }}</p>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Phone Number</p>
                    <p class="text-gray-800 font-medium">{{ $booking->user->phoneNum ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Important Information --}}
    @if(in_array(strtolower($booking->status), ['pending', 'confirmed']))
        <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-5">
            <h3 class="font-bold text-gray-800 mb-3 flex items-center text-lg">
                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Important Reminders
            </h3>
            <ul class="text-sm text-gray-700 space-y-2">
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Please arrive 10 minutes before your scheduled appointment
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Bring a valid government-issued ID
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Be prepared to discuss your living situation and pet care experience
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    If you need to reschedule or cancel, please notify us at least 24 hours in advance
                </li>
            </ul>
        </div>
    @endif

    {{-- Action Buttons --}}
    @if(in_array(strtolower($booking->status), ['pending', 'confirmed']))
        <div class="flex gap-3 justify-end pt-4">
            <button type="button"
                    onclick="openCancelModal({{ $booking->id }})"
                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition duration-300 shadow-md flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel Booking
            </button>
        </div>
    @endif
</div>
