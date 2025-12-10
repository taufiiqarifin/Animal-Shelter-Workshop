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

<div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-5xl font-bold mb-4">My Bookings</h1>
                <p class="text-xl text-purple-100">View and manage your appointment bookings for adoptions</p>
            </div>
            @unless($bookings->isEmpty())
                <div class="mt-6 md:mt-0">
                    <a href="{{ route('animal:main') }}" class="inline-flex items-center gap-2 bg-white text-purple-700 px-8 py-3 rounded-lg font-semibold hover:bg-purple-50 transition duration-300 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>New Booking</span>
                    </a>
                </div>
            @endunless
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

    <!-- Stats Cards as Filter Buttons -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-10">
        <!-- Total Bookings Card -->
        <a href="{{ route('bookings.index') }}"
           class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 cursor-pointer {{ !request('status') ? 'ring-2 ring-purple-500' : '' }}">
            <div class="text-3xl mb-2">📅</div>
            <p class="text-3xl font-bold text-purple-700 mb-1">{{ $totalBookings }}</p>
            <p class="text-sm text-gray-600">Total</p>
            @if(!request('status'))
                <div class="mt-1 text-xs text-purple-600 font-semibold">● Active</div>
            @endif
        </a>

        <!-- Pending Card -->
        <a href="{{ route('bookings.index', ['status' => 'Pending']) }}"
           class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 cursor-pointer {{ request('status') == 'Pending' ? 'ring-2 ring-yellow-500' : '' }}">
            <div class="text-3xl mb-2">⏳</div>
            <p class="text-3xl font-bold text-yellow-600 mb-1">{{ $statusCounts['Pending'] ?? 0 }}</p>
            <p class="text-sm text-gray-600">Pending</p>
            @if(request('status') == 'Pending')
                <div class="mt-1 text-xs text-yellow-600 font-semibold">● Active</div>
            @endif
        </a>

        <!-- Confirmed Card -->
        <a href="{{ route('bookings.index', ['status' => 'Confirmed']) }}"
           class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 cursor-pointer {{ request('status') == 'Confirmed' ? 'ring-2 ring-blue-500' : '' }}">
            <div class="text-3xl mb-2">✅</div>
            <p class="text-3xl font-bold text-blue-600 mb-1">{{ $statusCounts['Confirmed'] ?? 0 }}</p>
            <p class="text-sm text-gray-600">Confirmed</p>
            @if(request('status') == 'Confirmed')
                <div class="mt-1 text-xs text-blue-600 font-semibold">● Active</div>
            @endif
        </a>

        <!-- Completed Card -->
        <a href="{{ route('bookings.index', ['status' => 'Completed']) }}"
           class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 cursor-pointer {{ request('status') == 'Completed' ? 'ring-2 ring-green-500' : '' }}">
            <div class="text-3xl mb-2">🎉</div>
            <p class="text-3xl font-bold text-green-600 mb-1">{{ $statusCounts['Completed'] ?? 0 }}</p>
            <p class="text-sm text-gray-600">Completed</p>
            @if(request('status') == 'Completed')
                <div class="mt-1 text-xs text-green-600 font-semibold">● Active</div>
            @endif
        </a>

        <!-- Cancelled Card -->
        <a href="{{ route('bookings.index', ['status' => 'Cancelled']) }}"
           class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 cursor-pointer {{ request('status') == 'Cancelled' ? 'ring-2 ring-red-500' : '' }}">
            <div class="text-3xl mb-2">❌</div>
            <p class="text-3xl font-bold text-red-600 mb-1">{{ $statusCounts['Cancelled'] ?? 0 }}</p>
            <p class="text-sm text-gray-600">Cancelled</p>
            @if(request('status') == 'Cancelled')
                <div class="mt-1 text-xs text-red-600 font-semibold">● Active</div>
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
            <h3 class="text-3xl font-bold text-gray-700 mb-3">No Bookings Yet</h3>
            <p class="text-gray-500 text-lg mb-8">You haven't made any bookings. Start by creating your first appointment!</p>
            <a href="{{ route('animal:main') }}" class="inline-flex items-center gap-2 bg-purple-700 hover:bg-purple-800 text-white px-8 py-3 rounded-lg font-semibold transition duration-300 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Create First Booking</span>
            </a>
        </div>
    @else
        <!-- Table View -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-purple-600 to-purple-700">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                            Booking ID
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                            Date & Time
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                            Animals
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                            Adoption Status
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($bookings as $booking)
                        @php
                            $statusKey = strtolower($booking->status);
                            $badgeColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                'confirmed' => 'bg-blue-100 text-blue-800 border-blue-300',
                                'completed' => 'bg-green-100 text-green-800 border-green-300',
                                'cancelled' => 'bg-red-100 text-red-800 border-red-300',
                            ];
                            $statusEmojis = [
                                'pending' => '⏳',
                                'confirmed' => '✅',
                                'completed' => '🎉',
                                'cancelled' => '❌',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <!-- Booking ID -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-bold text-purple-700">#{{ $booking->id }}</div>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold border {{ $badgeColors[$statusKey] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                        <span>{{ $statusEmojis[$statusKey] ?? '📅' }}</span>
                                        <span>{{ ucfirst($booking->status) }}</span>
                                    </span>
                            </td>

                            <!-- Date & Time -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">
                                    {{ \Carbon\Carbon::parse($booking->appointment_date)->format('M d, Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($booking->appointment_time)->format('h:i A') }}
                                </div>
                            </td>

                            <!-- Animals -->
                            <td class="px-6 py-4">
                                @if($booking->animals->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($booking->animals->take(3) as $animal)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-700">
                                                    {{ $animal->name }}
                                                </span>
                                        @endforeach
                                        @if($booking->animals->count() > 3)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                                                    +{{ $booking->animals->count() - 3 }} more
                                                </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">No animals</span>
                                @endif
                            </td>

                            <!-- Adoption Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($booking->adoptions->isNotEmpty())
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm font-semibold text-green-800">
                                                {{ $booking->adoptions->count() }} Adopted
                                            </span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">Not yet</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button type="button"
                                        onclick="openModal('bookingModal-{{ $booking->id }}')"
                                        class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition duration-200 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Details
                                </button>
                            </td>
                        </tr>

                        <!-- Include the modal for each booking (hidden by default) -->
                        @include('booking-adoption.show-modal', ['booking' => $booking])
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($bookings->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $bookings->links() }}
            </div>
        @endif
    @endif

    <div class="mt-16 bg-gradient-to-r from-purple-700 to-purple-900 rounded-lg p-12 text-center text-white">
        <h2 class="text-3xl font-bold mb-4">Need Help with Your Booking?</h2>
        <p class="text-xl mb-6">Contact us if you have any questions about your appointments or need to make changes.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact') }}" class="bg-white text-purple-700 px-8 py-3 rounded-lg font-semibold hover:bg-purple-50 transition duration-300 inline-block">
                Contact Support
            </a>
        </div>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
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

    // Back to booking details - just close adoption fee modal
    function backToBookingDetails(bookingId) {
        closeModal('adoptionFeeModal-' + bookingId);
    }

    // Update selection count and estimated fee in booking details modal
    function updateSelectionSummary(bookingId) {
        const checkboxes = document.querySelectorAll('.animal-select-' + bookingId + ':checked');
        let total = 0;
        let count = 0;

        checkboxes.forEach(function(cb) {
            total += parseFloat(cb.dataset.fee) || 0;
            count++;
        });

        const countEl = document.getElementById('selectedCount-' + bookingId);
        const feeEl = document.getElementById('estimatedFee-' + bookingId);

        if (countEl) countEl.innerText = count;
        if (feeEl) feeEl.innerText = 'RM ' + total.toFixed(2);
    }

    // Open adoption fee modal and populate with selected animals
    function openAdoptionFeeModal(bookingId) {
        const checkboxes = document.querySelectorAll('.animal-select-' + bookingId + ':checked');

        if (checkboxes.length === 0) {
            alert('Please select at least one animal to adopt.');
            return;
        }

        const listContainer = document.getElementById('selectedAnimalsList-' + bookingId);
        const hiddenInputsContainer = document.getElementById('hiddenAnimalInputs-' + bookingId);
        const grandTotalEl = document.getElementById('grandTotal-' + bookingId);
        const noAnimalsMsg = document.getElementById('noAnimalsSelected-' + bookingId);
        const submitBtn = document.getElementById('submitBtn-' + bookingId);

        listContainer.innerHTML = '';
        hiddenInputsContainer.innerHTML = '';

        let grandTotal = 0;

        checkboxes.forEach(function(cb) {
            const animalId = cb.dataset.animalId;
            const animalName = cb.dataset.animalName;
            const animalSpecies = cb.dataset.animalSpecies;
            const baseFee = parseFloat(cb.dataset.baseFee) || 0;
            const medicalFee = parseFloat(cb.dataset.medicalFee ?? 0);
            const medicalCount = parseInt(cb.dataset.medicalCount ?? 0);
            const vaccinationFee = parseFloat(cb.dataset.vaccinationFee ?? 0);
            const vaccinationCount = parseInt(cb.dataset.vaccinationCount ?? 0);
            const totalFee = parseFloat(cb.dataset.fee) || 0;

            grandTotal += totalFee;

            const animalCard = document.createElement('div');
            animalCard.className = 'flex items-center justify-between bg-white rounded-lg p-3 border border-gray-200';
            animalCard.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">${animalName} (${animalSpecies})</p>
                        <p class="text-sm text-gray-600">
                            Base: RM ${baseFee.toFixed(2)} |
                            Medical: RM ${medicalFee.toFixed(2)} (${medicalCount} record${medicalCount !== 1 ? 's' : ''}) |
                            Vaccination: RM ${vaccinationFee.toFixed(2)} (${vaccinationCount} shot${vaccinationCount !== 1 ? 's' : ''})
                        </p>
                    </div>
                </div>
                <span class="font-bold text-gray-800">RM ${totalFee.toFixed(2)}</span>
            `;
            listContainer.appendChild(animalCard);

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'animal_ids[]';
            hiddenInput.value = animalId;
            hiddenInputsContainer.appendChild(hiddenInput);
        });

        const totalFeeInput = document.createElement('input');
        totalFeeInput.type = 'hidden';
        totalFeeInput.name = 'total_fee';
        totalFeeInput.value = grandTotal.toFixed(2);
        hiddenInputsContainer.appendChild(totalFeeInput);

        grandTotalEl.innerText = 'RM ' + grandTotal.toFixed(2);

        if (checkboxes.length === 0) {
            noAnimalsMsg.classList.remove('hidden');
            submitBtn.disabled = true;
        } else {
            noAnimalsMsg.classList.add('hidden');
            submitBtn.disabled = false;
        }

        openModal('adoptionFeeModal-' + bookingId);
    }

    // Add event listeners for animal selection checkboxes
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[class*="animal-select-"]').forEach(function(cb) {
            cb.addEventListener('change', function() {
                const classes = this.className.split(' ');
                const selectClass = classes.find(c => c.startsWith('animal-select-'));
                if (selectClass) {
                    const bookingId = selectClass.replace('animal-select-', '');
                    updateSelectionSummary(bookingId);
                }
            });
        });
    });
</script>
</body>
</html>
