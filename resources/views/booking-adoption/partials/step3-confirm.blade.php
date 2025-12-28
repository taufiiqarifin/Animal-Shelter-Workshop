{{-- Step 3: Confirm & Pay --}}
<form action="{{ route('bookings.confirm', $booking->id) }}" method="POST" id="confirmForm-{{ $booking->id }}">
    @csrf
    @method('PATCH')

    <div class="space-y-6">

        {{-- Selected Animals Summary --}}
        <div class="bg-purple-50 border-l-4 border-purple-600 rounded-lg p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center text-lg">
                <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Selected Animals for Adoption
            </h3>

            {{-- Container for dynamically populated animals --}}
            <div id="selectedAnimalsList-{{ $booking->id }}" class="space-y-3">
                {{-- Will be populated by JavaScript --}}
            </div>

            <p id="noAnimalsSelected-{{ $booking->id }}" class="text-red-600 text-sm mt-3 hidden">
                Please go back and select at least one animal.
            </p>
        </div>

        {{-- Fee Breakdown --}}
        <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-300 rounded-xl p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center text-lg">
                <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Fee Breakdown
            </h3>

            <div class="bg-white rounded-lg p-5 shadow-md mb-4">
                <div id="feeBreakdownList-{{ $booking->id }}" class="space-y-3">
                    {{-- Will be populated by JavaScript --}}
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-lg border-2 border-green-400">
                <div class="flex justify-between items-center">
                    <span class="text-2xl font-bold text-gray-800">Total Adoption Fee</span>
                    <span id="grandTotal-{{ $booking->id }}" class="text-4xl font-bold text-green-600">RM 0.00</span>
                </div>
            </div>
        </div>

        {{-- Payment Information --}}
        <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-5">
            <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Payment Information
            </h3>
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    You will be redirected to our secure payment gateway (ToyyibPay)
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Payment methods: Online Banking (FPX), Credit/Debit Card
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    After successful payment, your booking will be marked as "Completed"
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    The adopted animals will be ready for pickup after payment
                </li>
            </ul>
        </div>

        {{-- Terms and Conditions --}}
        <div class="bg-white border-2 border-gray-200 rounded-xl p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center text-lg">
                <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Terms and Conditions
            </h3>

            <div class="bg-gray-50 rounded-lg p-4 mb-4 max-h-48 overflow-y-auto">
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start">
                        <span class="text-purple-600 font-bold mr-2">•</span>
                        I confirm that I am 18 years or older and legally able to adopt an animal
                    </li>
                    <li class="flex items-start">
                        <span class="text-purple-600 font-bold mr-2">•</span>
                        I understand that the adoption fee covers medical care, vaccinations, and shelter expenses
                    </li>
                    <li class="flex items-start">
                        <span class="text-purple-600 font-bold mr-2">•</span>
                        I commit to providing a safe, loving, and permanent home for the adopted animal(s)
                    </li>
                    <li class="flex items-start">
                        <span class="text-purple-600 font-bold mr-2">•</span>
                        I will provide proper food, water, shelter, medical care, and attention to the animal(s)
                    </li>
                    <li class="flex items-start">
                        <span class="text-purple-600 font-bold mr-2">•</span>
                        I understand that the adoption fee is non-refundable once payment is completed
                    </li>
                    <li class="flex items-start">
                        <span class="text-purple-600 font-bold mr-2">•</span>
                        I agree to comply with all local animal welfare laws and regulations
                    </li>
                    <li class="flex items-start">
                        <span class="text-purple-600 font-bold mr-2">•</span>
                        I understand that the shelter may conduct follow-up visits to ensure animal welfare
                    </li>
                </ul>
            </div>

            <div class="flex items-start">
                <input type="checkbox"
                       id="agree_terms_{{ $booking->id }}"
                       name="agree_terms"
                       class="mt-1 mr-3 h-5 w-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500 cursor-pointer"
                       required>
                <label for="agree_terms_{{ $booking->id }}" class="text-sm text-gray-700 cursor-pointer">
                    <span class="font-semibold">I have read and agree to all the terms and conditions above.</span>
                    <span class="text-red-600 font-bold ml-1">*</span>
                    <p class="text-xs text-gray-500 mt-1">By checking this box, you acknowledge that you understand and accept all terms and conditions of this adoption.</p>
                </label>
            </div>
        </div>

        {{-- Hidden inputs for selected animal IDs and total fee --}}
        <div id="hiddenAnimalInputs-{{ $booking->id }}"></div>

        {{-- Submit Button --}}
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 border-2 border-purple-300 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-gray-800 text-lg mb-1">Ready to Complete Your Adoption?</h4>
                    <p class="text-gray-600 text-sm">Click the button below to proceed to payment</p>
                </div>
                <button type="submit"
                        id="submitBtn-{{ $booking->id }}"
                        class="px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white font-bold text-lg rounded-xl hover:from-green-700 hover:to-green-800 transition duration-300 shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span id="submitBtnText-{{ $booking->id }}">Proceed to Payment</span>
                </button>
            </div>
        </div>

    </div>
</form>

<style>
    /* Loading spinner animation */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
