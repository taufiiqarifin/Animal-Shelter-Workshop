<!-- Modal for Animal Profile -->
<div id="animalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
    @php
        // Helper function to safely get a value from the profile data or old input after failure
        $getProfileValue = fn ($key) => optional($animalProfile)->{$key} ?? old($key);
    @endphp

    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold">Animal Profile</h2>
                <button type="button" onclick="closeAnimalModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('animal.profile.store', ['animal' => $animal->id]) }}" method="POST" class="p-6 space-y-4" id="animalProfileForm" onsubmit="handleAnimalProfileSubmit(event)">
            @csrf

            {{-- Optionally include a hidden field for the Animal Profile ID if updating --}}
            @if ($animalProfile)
                <input type="hidden" name="profile_id" value="{{ $animalProfile->id }}">
            @endif

            <!-- Size -->
            <div>
                <label class="block text-gray-800 font-semibold mb-2">Size <span class="text-red-600">*</span></label>
                <select name="size" class="w-full border-gray-300 rounded-lg shadow-sm px-4 py-3 border focus:border-purple-500 focus:ring focus:ring-purple-200 transition" required>
                    <option value="">Select size</option>
                    <option value="small" @selected($getProfileValue('size') == 'small')>Small (Under 10kg)</option>
                    <option value="medium" @selected($getProfileValue('size') == 'medium')>Medium (10-25kg)</option>
                    <option value="large" @selected($getProfileValue('size') == 'large')>Large (Over 25kg)</option>
                </select>
            </div>

            <!-- Energy Level -->
            <div>
                <label class="block text-gray-800 font-semibold mb-2">Energy Level <span class="text-red-600">*</span></label>
                <select name="energy_level" class="w-full border-gray-300 rounded-lg shadow-sm px-4 py-3 border focus:border-purple-500 focus:ring focus:ring-purple-200 transition" required>
                    <option value="">Select energy level</option>
                    <option value="low" @selected($getProfileValue('energy_level') == 'low')>Low - Calm and relaxed</option>
                    <option value="medium" @selected($getProfileValue('energy_level') == 'medium')>Medium - Moderately active</option>
                    <option value="high" @selected($getProfileValue('energy_level') == 'high')>High - Very energetic and playful</option>
                </select>
            </div>

            <!-- Good with Kids -->
            <div>
                <label class="block text-gray-800 font-semibold mb-2">Good with Kids? <span class="text-red-600">*</span></label>
                <select name="good_with_kids" class="w-full border-gray-300 rounded-lg shadow-sm px-4 py-3 border focus:border-purple-500 focus:ring focus:ring-purple-200 transition" required>
                    <option value="">Select option</option>
                    <option value="1" @selected($getProfileValue('good_with_kids') == 1)>Yes</option>
                    <option value="0" @selected($getProfileValue('good_with_kids') == 0 && $getProfileValue('good_with_kids') !== null)>No</option>
                </select>
            </div>

            <!-- Good with Other Pets -->
            <div>
                <label class="block text-gray-800 font-semibold mb-2">Good with Other Pets? <span class="text-red-600">*</span></label>
                <select name="good_with_pets" class="w-full border-gray-300 rounded-lg shadow-sm px-4 py-3 border focus:border-purple-500 focus:ring focus:ring-purple-200 transition" required>
                    <option value="">Select option</option>
                    <option value="1" @selected($getProfileValue('good_with_pets') == 1)>Yes</option>
                    <option value="0" @selected($getProfileValue('good_with_pets') == 0 && $getProfileValue('good_with_pets') !== null)>No</option>
                </select>
            </div>

            <!-- Temperament -->
            <div>
                <label class="block text-gray-800 font-semibold mb-2">Temperament <span class="text-red-600">*</span></label>
                <select name="temperament" class="w-full border-gray-300 rounded-lg shadow-sm px-4 py-3 border focus:border-purple-500 focus:ring focus:ring-purple-200 transition" required>
                    <option value="">Select temperament</option>
                    <option value="calm" @selected($getProfileValue('temperament') == 'calm')>Calm</option>
                    <option value="active" @selected($getProfileValue('temperament') == 'active')>Active</option>
                    <option value="shy" @selected($getProfileValue('temperament') == 'shy')>Shy</option>
                    <option value="friendly" @selected($getProfileValue('temperament') == 'friendly')>Friendly</option>
                    <option value="independent" @selected($getProfileValue('temperament') == 'independent')>Independent</option>
                </select>
            </div>

            <!-- Medical Needs -->
            <div>
                <label class="block text-gray-800 font-semibold mb-2">Medical Needs <span class="text-red-600">*</span></label>
                <select name="medical_needs" class="w-full border-gray-300 rounded-lg shadow-sm px-4 py-3 border focus:border-purple-500 focus:ring focus:ring-purple-200 transition" required>
                    <option value="">Select medical needs</option>
                    <option value="none" @selected($getProfileValue('medical_needs') == 'none')>None</option>
                    <option value="minor" @selected($getProfileValue('medical_needs') == 'minor')>Minor</option>
                    <option value="moderate" @selected($getProfileValue('medical_needs') == 'moderate')>Moderate</option>
                    <option value="special" @selected($getProfileValue('medical_needs') == 'special')>Special</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeAnimalModal()" class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition duration-300">
                    Cancel
                </button>
                <button type="submit" id="animalProfileSubmitBtn" class="px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white font-semibold rounded-lg hover:from-purple-600 hover:to-purple-700 transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="animalProfileBtnText">
                        <i class="fas fa-save mr-2"></i>Save Profile
                    </span>
                    <span id="animalProfileBtnLoading" class="hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Saving...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Function to open the modal
    function openAnimalModal() {
        const modal = document.getElementById('animalModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    // Function to close the modal
    function closeAnimalModal() {
        document.getElementById('animalModal').classList.add('hidden');
        document.getElementById('animalModal').classList.remove('flex');
    }

    // Close modal when clicking outside
    document.getElementById('animalModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeAnimalModal();
        }
    });

    // Handle form submission with loading state
    function handleAnimalProfileSubmit(event) {
        const submitBtn = document.getElementById('animalProfileSubmitBtn');
        const btnText = document.getElementById('animalProfileBtnText');
        const btnLoading = document.getElementById('animalProfileBtnLoading');

        // Show loading state
        submitBtn.disabled = true;
        btnText.classList.add('hidden');
        btnLoading.classList.remove('hidden');

        // Form will submit normally, button stays disabled
    }
</script>
