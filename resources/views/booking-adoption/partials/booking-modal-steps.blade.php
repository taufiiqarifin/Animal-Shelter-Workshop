{{-- Multi-Step Booking Modal --}}
@php
    $animals = $booking->animals ?? collect();
    $allFeeBreakdowns = [];
    $totalFee = 0;

    // Species-based fee structure (same as controller)
    $speciesBaseFees = [
        'dog' => 20,
        'cat' => 10,
    ];
    $medicalRate = 10;
    $vaccinationRate = 20;

    if ($animals->isNotEmpty()) {
        foreach ($animals as $animal) {
            $species = strtolower($animal->species);
            $baseFee = $speciesBaseFees[$species] ?? 100;

            $medicalCount = $animal->medicals ? $animal->medicals->count() : 0;
            $medicalFee = $medicalCount * $medicalRate;

            $vaccinationCount = $animal->vaccinations ? $animal->vaccinations->count() : 0;
            $vaccinationFee = $vaccinationCount * $vaccinationRate;

            $animalTotal = $baseFee + $medicalFee + $vaccinationFee;

            $allFeeBreakdowns[$animal->id] = [
                'base_fee' => $baseFee,
                'medical_rate' => $medicalRate,
                'medical_count' => $medicalCount,
                'medical_fee' => $medicalFee,
                'vaccination_rate' => $vaccinationRate,
                'vaccination_count' => $vaccinationCount,
                'vaccination_fee' => $vaccinationFee,
                'total_fee' => $animalTotal,
            ];

            $totalFee += $animalTotal;
        }
    }
@endphp

<div id="bookingModal-{{ $booking->id }}" class="modal-backdrop hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[95vh] overflow-hidden flex" onclick="event.stopPropagation()">

        {{-- Left Sidebar - Steps Indicator --}}
        <div class="bg-gradient-to-b from-purple-600 to-purple-800 text-white w-64 p-8 flex-shrink-0">
            <div class="mb-8">
                <h3 class="text-2xl font-bold">Adoption Process</h3>
                <p class="text-purple-200 text-sm mt-2">Booking #{{ $booking->id }}</p>
            </div>

            {{-- Step Indicators --}}
            <div class="space-y-6">
                {{-- Step 1 --}}
                <div class="step-indicator flex items-start gap-4" data-step="1">
                    <div class="step-number flex-shrink-0 w-10 h-10 rounded-full bg-white text-purple-700 flex items-center justify-center font-bold text-lg">
                        1
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-lg">Booking Details</h4>
                        <p class="text-purple-200 text-sm mt-1">Review your appointment</p>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="step-indicator flex items-start gap-4 opacity-50" data-step="2">
                    <div class="step-number flex-shrink-0 w-10 h-10 rounded-full bg-purple-400 text-white flex items-center justify-center font-bold text-lg">
                        2
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-lg">Select Animals</h4>
                        <p class="text-purple-200 text-sm mt-1">Choose animals to adopt</p>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="step-indicator flex items-start gap-4 opacity-50" data-step="3">
                    <div class="step-number flex-shrink-0 w-10 h-10 rounded-full bg-purple-400 text-white flex items-center justify-center font-bold text-lg">
                        3
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-lg">Confirm & Pay</h4>
                        <p class="text-purple-200 text-sm mt-1">Review and complete</p>
                    </div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="mt-12">
                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="text-purple-200">Progress</span>
                    <span class="font-semibold" id="progress-text-{{ $booking->id }}">33%</span>
                </div>
                <div class="w-full bg-purple-900 rounded-full h-2">
                    <div id="progress-bar-{{ $booking->id }}" class="bg-white h-2 rounded-full transition-all duration-300" style="width: 33%"></div>
                </div>
            </div>
        </div>

        {{-- Right Content Area --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Header --}}
            <div class="bg-gray-50 border-b border-gray-200 p-6 flex items-center justify-between flex-shrink-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800" id="step-title-{{ $booking->id }}">Booking Details</h2>
                    <p class="text-gray-600 text-sm mt-1" id="step-subtitle-{{ $booking->id }}">Review your booking information</p>
                </div>
                <button type="button" onclick="closeBookingModal({{ $booking->id }})" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Content Steps --}}
            <div class="flex-1 overflow-y-auto p-8">

                {{-- Step 1: Booking Details --}}
                <div class="step-content" data-step="1" id="step1-{{ $booking->id }}">
                    @include('booking-adoption.partials.step1-details', ['booking' => $booking])
                </div>

                {{-- Step 2: Select Animals --}}
                <div class="step-content hidden" data-step="2" id="step2-{{ $booking->id }}">
                    @include('booking-adoption.partials.step2-select', ['booking' => $booking, 'animals' => $animals, 'allFeeBreakdowns' => $allFeeBreakdowns])
                </div>

                {{-- Step 3: Confirm & Pay --}}
                <div class="step-content hidden" data-step="3" id="step3-{{ $booking->id }}">
                    @include('booking-adoption.partials.step3-confirm', ['booking' => $booking])
                </div>

            </div>

            {{-- Footer Navigation --}}
            <div class="bg-gray-50 border-t border-gray-200 p-6 flex items-center justify-between flex-shrink-0">
                <button type="button"
                        id="prev-btn-{{ $booking->id }}"
                        onclick="previousStep({{ $booking->id }})"
                        class="hidden px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition duration-300 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back
                </button>

                <div class="flex gap-3 ml-auto">
                    @if(in_array(strtolower($booking->status), ['pending', 'confirmed']))
                        <button type="button"
                                id="next-btn-{{ $booking->id }}"
                                onclick="nextStep({{ $booking->id }})"
                                class="px-8 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-semibold rounded-lg transition duration-300 shadow-lg flex items-center gap-2">
                            Next
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Cancel Confirmation Modal --}}
@if(in_array(strtolower($booking->status), ['pending', 'confirmed']))
    @include('booking-adoption.partials.cancel-modal', ['booking' => $booking])
@endif
