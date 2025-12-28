{{-- Step 2: Select Animals to Adopt --}}
<div class="space-y-6">

    @if($animals->isNotEmpty())
        {{-- Instructions --}}
        <div class="bg-purple-50 border-l-4 border-purple-600 rounded-lg p-5">
            <h3 class="font-bold text-gray-800 mb-2 flex items-center">
                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Select Animals to Adopt
            </h3>
            <p class="text-sm text-gray-700">
                Choose one or more animals from your booking that you would like to adopt. The adoption fee will be calculated based on your selection.
            </p>
        </div>

        {{-- Animal Selection Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($animals as $index => $animal)
                @php
                    $breakdown = $allFeeBreakdowns[$animal->id] ?? null;
                @endphp

                <label class="cursor-pointer group block">
                    <div class="bg-white rounded-xl p-5 shadow-lg border-3 border-transparent group-hover:border-purple-400 transition-all relative
                        has-[:checked]:border-purple-600 has-[:checked]:ring-4 has-[:checked]:ring-purple-200 has-[:checked]:shadow-xl">

                        {{-- Checkbox --}}
                        <input type="checkbox"
                               id="selectAnimal-{{ $booking->id }}-{{ $animal->id }}"
                               class="animal-select-{{ $booking->id }} absolute top-4 right-4 w-6 h-6 text-purple-600 border-gray-300 rounded focus:ring-purple-500 cursor-pointer"
                               data-animal-id="{{ $animal->id }}"
                               data-animal-name="{{ $animal->name }}"
                               data-animal-species="{{ $animal->species }}"
                               data-fee="{{ $breakdown['total_fee'] ?? 0 }}"
                               data-base-fee="{{ $breakdown['base_fee'] ?? 0 }}"
                               data-medical-fee="{{ $breakdown['medical_fee'] ?? 0 }}"
                               data-vaccination-fee="{{ $breakdown['vaccination_fee'] ?? 0 }}"
                               data-medical-count="{{ $breakdown['medical_count'] ?? 0 }}"
                               data-vaccination-count="{{ $breakdown['vaccination_count'] ?? 0 }}"
                               {{ $index === 0 ? 'checked' : '' }}>

                        {{-- Selected Badge --}}
                        <div class="selected-badge hidden absolute top-4 right-4 bg-purple-600 text-white rounded-full p-2 shadow-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>

                        {{-- Animal Image --}}
                        @if($animal->images && $animal->images->count() > 0)
                            <img src="{{ $animal->images->first()->url }}"
                                 alt="{{ $animal->name }}"
                                 class="w-full h-48 object-cover rounded-lg mb-4">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-purple-300 to-purple-400 rounded-lg flex items-center justify-center mb-4">
                                <span class="text-6xl">
                                    @if(strtolower($animal->species) == 'dog') 🐕
                                    @elseif(strtolower($animal->species) == 'cat') 🐈
                                    @else 🐾
                                    @endif
                                </span>
                            </div>
                        @endif

                        {{-- Animal Info --}}
                        <div class="mb-4">
                            <h4 class="text-xl font-bold text-gray-800 mb-1">{{ $animal->name }}</h4>
                            <p class="text-gray-600 text-sm mb-3">{{ $animal->species }} • {{ $animal->age }} • {{ $animal->gender }}</p>

                            {{-- Health Records Summary --}}
                            <div class="flex gap-2 flex-wrap mb-3">
                                @if($breakdown && $breakdown['medical_count'] > 0)
                                    <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $breakdown['medical_count'] }} Medical Record{{ $breakdown['medical_count'] > 1 ? 's' : '' }}
                                    </span>
                                @endif
                                @if($breakdown && $breakdown['vaccination_count'] > 0)
                                    <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $breakdown['vaccination_count'] }} Vaccination{{ $breakdown['vaccination_count'] > 1 ? 's' : '' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Fee Breakdown --}}
                        @if($breakdown)
                            <div class="border-t pt-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Base Fee ({{ $animal->species }}):</span>
                                    <span class="font-medium text-gray-800">RM {{ number_format($breakdown['base_fee'], 2) }}</span>
                                </div>
                                @if($breakdown['medical_fee'] > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Medical ({{ $breakdown['medical_count'] }} × RM {{ $breakdown['medical_rate'] }}):</span>
                                        <span class="font-medium text-gray-800">RM {{ number_format($breakdown['medical_fee'], 2) }}</span>
                                    </div>
                                @endif
                                @if($breakdown['vaccination_fee'] > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Vaccination ({{ $breakdown['vaccination_count'] }} × RM {{ $breakdown['vaccination_rate'] }}):</span>
                                        <span class="font-medium text-gray-800">RM {{ number_format($breakdown['vaccination_fee'], 2) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between text-base font-bold pt-2 border-t">
                                    <span class="text-purple-700">Total Fee:</span>
                                    <span class="text-purple-700">RM {{ number_format($breakdown['total_fee'], 2) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </label>
            @endforeach
        </div>

        {{-- Selection Summary --}}
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 border-2 border-purple-300 rounded-xl p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center text-lg">
                <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                Selection Summary
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-5 shadow-md">
                    <p class="text-sm text-gray-600 mb-1">Animals Selected</p>
                    <p class="text-3xl font-bold text-purple-700">
                        <span id="selectedCount-{{ $booking->id }}">1</span>
                        <span class="text-lg text-gray-600">/ {{ $animals->count() }}</span>
                    </p>
                </div>
                <div class="bg-white rounded-lg p-5 shadow-md">
                    <p class="text-sm text-gray-600 mb-1">Estimated Adoption Fee</p>
                    <p class="text-3xl font-bold text-green-600" id="estimatedFee-{{ $booking->id }}">
                        RM {{ number_format($allFeeBreakdowns[$animals->first()->id]['total_fee'] ?? 0, 2) }}
                    </p>
                </div>
            </div>
        </div>

    @else
        <div class="bg-gray-100 rounded-xl p-8 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg font-medium">No animals associated with this booking.</p>
        </div>
    @endif

</div>

<style>
    /* Show selected badge when checkbox is checked */
    .animal-select-{{ $booking->id }}:checked ~ .selected-badge {
        display: block !important;
    }

    /* Hide checkbox when selected */
    .animal-select-{{ $booking->id }}:checked {
        display: none;
    }
</style>
