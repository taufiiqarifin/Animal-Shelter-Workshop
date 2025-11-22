<script>
    // Update selection summary when checkboxes change
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($animals as $animal)
        @if(in_array(strtolower($booking->status), ['pending', 'confirmed']))
        const checkboxes{{ $booking->id }} = document.querySelectorAll('.animal-select-{{ $booking->id }}');

        checkboxes{{ $booking->id }}.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectionSummary{{ $booking->id }}();
            });
        });

        function updateSelectionSummary{{ $booking->id }}() {
            const checked = document.querySelectorAll('.animal-select-{{ $booking->id }}:checked');
            const count = checked.length;
            let totalFee = 0;

            checked.forEach(cb => {
                totalFee += parseFloat(cb.dataset.fee);
            });

            document.getElementById('selectedCount-{{ $booking->id }}').textContent = count;
            document.getElementById('estimatedFee-{{ $booking->id }}').textContent = 'RM ' + totalFee.toFixed(2);
        }

        // Initial update
        updateSelectionSummary{{ $booking->id }}();
        break;
        @endif
        @endforeach
    });

    // Open Adoption Fee Modal with selected animals
    function openAdoptionFeeModal(bookingId) {
        const checkboxes = document.querySelectorAll('.animal-select-' + bookingId + ':checked');

        if (checkboxes.length === 0) {
            alert('Please select at least one animal to adopt.');
            return;
        }

        // Populate selected animals list
        const animalsList = document.getElementById('selectedAnimalsList-' + bookingId);
        const hiddenInputs = document.getElementById('hiddenAnimalInputs-' + bookingId);
        const noAnimalsMsg = document.getElementById('noAnimalsSelected-' + bookingId);

        animalsList.innerHTML = '';
        hiddenInputs.innerHTML = '';

        let totalFee = 0;

        checkboxes.forEach((checkbox, index) => {
            const animalId = checkbox.dataset.animalId;
            const animalName = checkbox.dataset.animalName;
            const animalSpecies = checkbox.dataset.animalSpecies;
            const fee = parseFloat(checkbox.dataset.fee);
            const baseFee = parseFloat(checkbox.dataset.baseFee);
            const medicalFee = parseFloat(checkbox.dataset.medicalFee);
            const vaccinationFee = parseFloat(checkbox.dataset.vaccinationFee);

            totalFee += fee;

            // Add animal card to list
            const animalCard = `
                <div class="bg-white rounded-lg p-4 border-2 border-purple-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-bold text-gray-800">${animalName}</h4>
                            <p class="text-sm text-gray-600">${animalSpecies}</p>
                            <div class="mt-2 space-y-1 text-xs text-gray-600">
                                <p>Base Fee: RM ${baseFee.toFixed(2)}</p>
                                ${medicalFee > 0 ? `<p>Medical: RM ${medicalFee.toFixed(2)}</p>` : ''}
                                ${vaccinationFee > 0 ? `<p>Vaccination: RM ${vaccinationFee.toFixed(2)}</p>` : ''}
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Subtotal</p>
                            <p class="text-lg font-bold text-green-600">RM ${fee.toFixed(2)}</p>
                        </div>
                    </div>
                </div>
            `;

            animalsList.innerHTML += animalCard;

            // Add hidden input for animal ID
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'animal_ids[]';
            hiddenInput.value = animalId;
            hiddenInputs.appendChild(hiddenInput);
        });

        // Add hidden input for total fee
        const totalFeeInput = document.createElement('input');
        totalFeeInput.type = 'hidden';
        totalFeeInput.name = 'total_fee';
        totalFeeInput.value = totalFee.toFixed(2);
        hiddenInputs.appendChild(totalFeeInput);

        // Update grand total display
        document.getElementById('grandTotal-' + bookingId).textContent = 'RM ' + totalFee.toFixed(2);

        // Hide no animals message
        noAnimalsMsg.classList.add('hidden');

        // Close booking details modal and open adoption fee modal
        closeModal('bookingModal-' + bookingId);
        document.getElementById('adoptionFeeModal-' + bookingId).classList.remove('hidden');
    }

    // Close modal function
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Back to booking details
    function backToBookingDetails(bookingId) {
        closeModal('adoptionFeeModal-' + bookingId);
        document.getElementById('bookingModal-' + bookingId).classList.remove('hidden');
    }

    // Close modal on background click
    document.querySelectorAll('.modal-backdrop').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    });
</script>
