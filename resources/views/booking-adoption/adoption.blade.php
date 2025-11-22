<!-- Adoption Fee Modal -->
<div id="adoptionFeeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">

        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 text-white p-6 sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">💰</span>
                    <div>
                        <h2 class="text-2xl font-bold">Adoption Fee Breakdown</h2>
                        <p class="text-green-100 text-sm">Booking #{{ $booking->id }}</p>
                    </div>
                </div>
                <button type="button" onclick="closeAdoptionFeeModal()" class="text-white hover:text-gray-200 transition">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">

            <!-- Select Animals -->
            <div class="bg-purple-50 border-l-4 border-purple-600 rounded-lg p-5">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                    <i class="fas fa-paw text-purple-600 mr-2"></i>
                    Select Animals to Adopt
                </h3>
                <div class="space-y-3">
                    @foreach($animals as $animal)
                        @php $fee = $allFeeBreakdowns[$animal->id]; @endphp
                        <label class="flex items-center justify-between bg-white rounded-lg p-3 border border-gray-200 cursor-pointer hover:border-purple-500 transition">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="animal_ids[]" value="{{ $animal->id }}" class="animal-checkbox h-5 w-5 text-green-600 border-gray-300 rounded" checked>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $animal->name }} ({{ $animal->species }})</p>
                                    <p class="text-sm text-gray-600">
                                        Base: RM {{ number_format($fee['base_fee'], 2) }},
                                        Medical: RM {{ number_format($fee['medical_fee'], 2) }},
                                        Vaccination: RM {{ number_format($fee['vaccination_fee'], 2) }}
                                    </p>
                                </div>
                            </div>
                            <span class="font-bold text-gray-800">RM {{ number_format($fee['total_fee'], 2) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Grand Total -->
            <div class="flex justify-between items-center py-4 bg-green-50 rounded-lg px-4 mt-4">
                <p class="text-xl font-bold text-gray-800">Total Adoption Fee</p>
                <span id="grandTotal" class="text-3xl font-bold text-green-600">RM {{ number_format($totalFee, 2) }}</span>
            </div>

            <!-- Terms -->
            <div class="mb-4 flex items-start">
                <input type="checkbox"
                       id="agree_terms"
                       name="agree_terms"
                       class="mt-1 mr-3 h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                       required>
                <label for="agree_terms" class="text-sm text-gray-700">
                    I understand and agree to pay the adoption fee for the selected animals. <span class="text-red-600">*</span>
                </label>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-50 p-6 border-t border-gray-200">
            <form id="confirmAdoptionForm" action="{{ route('bookings.confirm', $booking->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="animal_ids" id="selectedAnimalIds">

                <div class="flex flex-wrap justify-end gap-3">
                    <button type="button"
                            onclick="closeAdoptionFeeModal()"
                            class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition duration-300">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg hover:from-green-700 hover:to-green-800 transition duration-300 shadow-lg">
                        <i class="fas fa-check-circle mr-2"></i>Complete Adoption
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Close modal
    function closeAdoptionFeeModal() {
        document.getElementById('adoptionFeeModal')?.remove();
    }

    // Calculate Grand Total dynamically
    function calculateGrandTotal() {
        let total = 0;
        const feeBreakdowns = @json($allFeeBreakdowns);

        // Loop through checked checkboxes
        document.querySelectorAll('.animal-checkbox:checked').forEach(cb => {
            const animalId = cb.value;
            if (feeBreakdowns[animalId]) {
                total += feeBreakdowns[animalId].total_fee;
            }
        });

        // Update UI
        document.getElementById('grandTotal').innerText = 'RM ' + total.toFixed(2);

        // Set hidden input for form submission
        const selectedIds = Array.from(document.querySelectorAll('.animal-checkbox:checked')).map(cb => cb.value);
        document.getElementById('selectedAnimalIds').value = selectedIds.join(',');
    }

    // Listen for changes
    document.querySelectorAll('.animal-checkbox').forEach(cb => {
        cb.addEventListener('change', calculateGrandTotal);
    });

    // Initialize total on modal load
    calculateGrandTotal();
</script>
