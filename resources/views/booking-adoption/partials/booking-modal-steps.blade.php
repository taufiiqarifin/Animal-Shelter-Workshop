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

<div id="bookingModal-{{ $booking->id }}" class="modal-backdrop hidden fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden" onclick="event.stopPropagation()">

        {{-- Row 1: Header (Full Width) --}}
        <div class="bg-gradient-to-r from-purple-600 via-purple-700 to-indigo-700 text-white p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-white bg-opacity-20 p-3 rounded-xl backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold" id="step-title-{{ $booking->id }}">Booking Details</h2>
                        <p class="text-purple-100 text-sm" id="step-subtitle-{{ $booking->id }}">Review your booking information</p>
                    </div>
                </div>
                <button type="button" onclick="closeBookingModal({{ $booking->id }})" class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-lg transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Row 2: Two Columns (Steps + Content) --}}
        <div class="grid grid-cols-12 gap-0">

            {{-- Column 1: Steps Sidebar (3 cols) --}}
            <div class="col-span-3 bg-gradient-to-br from-purple-50 via-purple-100 to-indigo-50 border-r border-purple-200 p-6">
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-purple-900">Adoption Steps</h3>
                    <p class="text-sm text-purple-700 mt-1 font-medium">Booking #{{ $booking->id }}</p>
                </div>

                {{-- Step Indicators --}}
                <div class="relative space-y-1">
                    {{-- Vertical connecting line --}}
                    <div class="absolute left-4 top-10 bottom-10 w-px bg-purple-300"></div>

                    {{-- Step 1 --}}
                    <div class="step-indicator relative flex items-start gap-3 py-2" data-step="1">
                        <div class="step-circle relative z-10 flex-shrink-0 w-8 h-8 rounded-full border-2 border-purple-600 bg-purple-600 flex items-center justify-center shadow-md">
                            <div class="step-number-icon text-white font-bold text-sm">1</div>
                            <svg class="step-checkmark-icon hidden w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1 pt-0.5">
                            <h4 class="font-bold text-sm text-purple-900">Details</h4>
                            <p class="text-purple-600 text-xs mt-0.5">Review booking</p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="step-indicator relative flex items-start gap-3 py-2 opacity-60" data-step="2">
                        <div class="step-circle relative z-10 flex-shrink-0 w-8 h-8 rounded-full border-2 border-purple-300 bg-white flex items-center justify-center">
                            <div class="step-number-icon text-purple-400 font-bold text-sm">2</div>
                            <svg class="step-checkmark-icon hidden w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1 pt-0.5">
                            <h4 class="font-semibold text-sm text-gray-700">Select</h4>
                            <p class="text-gray-500 text-xs mt-0.5">Choose animals</p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="step-indicator relative flex items-start gap-3 py-2 opacity-60" data-step="3">
                        <div class="step-circle relative z-10 flex-shrink-0 w-8 h-8 rounded-full border-2 border-purple-300 bg-white flex items-center justify-center">
                            <div class="step-number-icon text-purple-400 font-bold text-sm">3</div>
                            <svg class="step-checkmark-icon hidden w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1 pt-0.5">
                            <h4 class="font-semibold text-sm text-gray-700">Confirm</h4>
                            <p class="text-gray-500 text-xs mt-0.5">Complete adoption</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Column 2: Main Content (9 cols) --}}
            <div class="col-span-9 flex flex-col max-h-[calc(90vh-120px)]">
                {{-- Content Steps --}}
                <div class="flex-1 overflow-y-auto p-6">

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

                {{-- Footer Navigation - Always Visible --}}
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-t border-gray-200 flex items-center justify-between flex-shrink-0 sticky bottom-0">
                    <button type="button"
                            id="prev-btn-{{ $booking->id }}"
                            onclick="previousStep({{ $booking->id }})"
                            class="hidden inline-flex items-center gap-2 px-6 py-3 bg-white hover:bg-gray-100 text-gray-700 font-bold rounded-xl transition-all shadow-sm hover:shadow-md border-2 border-gray-200 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span>Back</span>
                    </button>

                    @if(in_array(strtolower($booking->status), ['pending', 'confirmed']))
                        <button type="button"
                                id="next-btn-{{ $booking->id }}"
                                onclick="nextStep({{ $booking->id }})"
                                class="ml-auto inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-sm">
                            <span>Next</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
