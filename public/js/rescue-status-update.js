// ==================== RESCUE STATUS UPDATE JAVASCRIPT ====================
// This file contains all the JavaScript functionality for the rescue detail page
// Global variables are initialized in the inline script in show-caretaker.blade.php

// ==================== GLOBAL VARIABLES (Initialized in inline script) ====================
// - map: Leaflet map instance
// - rescueImages: Array of rescue report images
// - rescueImageIndex: Current image index
// - selectedStatus: Current selected rescue status
// - currentStep: Current step in animal addition modal
// - totalAnimals: Total number of animals to add
// - currentAnimalIndex: Current animal being added
// - addedAnimals: Array of added animal data
// - animalImagesMap: Map of animal index to uploaded images
// - rescueId: Rescue ID from Blade template

// ==================== IMAGE MODAL FUNCTIONS ====================

function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// ==================== IMAGE SWIPER FUNCTIONS ====================

function rescueDisplayImage() {
    const content = document.getElementById('rescueImageSwiperContent');

    if (rescueImages.length === 0) return;

    content.innerHTML = `<img src="${rescueImages[rescueImageIndex].path}"
                              class="max-w-full max-h-full object-contain cursor-pointer"
                              onclick="openImageModal(this.src)">`;

    if (document.getElementById('rescueCurrentImageIndex')) {
        document.getElementById('rescueCurrentImageIndex').textContent = rescueImageIndex + 1;
    }

    rescueImages.forEach((_, index) => {
        const thumb = document.getElementById(`rescueThumbnail-${index}`);
        if (thumb) {
            thumb.className = `flex-shrink-0 w-16 h-16 cursor-pointer rounded overflow-hidden border-2 ${
                index === rescueImageIndex ? 'border-purple-500' : 'border-gray-200 hover:border-purple-300'
            }`;
        }
    });
}

function rescueGoToImage(index) {
    if (index >= 0 && index < rescueImages.length) {
        rescueImageIndex = index;
        rescueDisplayImage();
    }
}

function rescueNextImage() {
    rescueImageIndex = (rescueImageIndex + 1) % rescueImages.length;
    rescueDisplayImage();
}

function rescuePrevImage() {
    rescueImageIndex = (rescueImageIndex - 1 + rescueImages.length) % rescueImages.length;
    rescueDisplayImage();
}

// ==================== STATUS UPDATE FUNCTIONS ====================

function updateStatus(status) {
    selectedStatus = status;

    if (status === 'Success' || status === 'Failed') {
        openRemarksModal(status);
    } else {
        submitStatusDirectly(status);
    }
}

function openRemarksModal(status) {
    // Close any open map popups to prevent overlap
    map.closePopup();

    if (status === 'Success') {
        // Show success remarks modal FIRST
        const modal = document.getElementById('successRemarksModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('successRescueRemarks').focus(), 100);
    } else if (status === 'Failed') {
        const modal = document.getElementById('remarksModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalIcon = document.getElementById('modalIcon');
        const remarksTextarea = document.getElementById('remarks');

        remarksTextarea.value = '';

        modalTitle.textContent = 'Rescue Failed';
        modalIcon.innerHTML = '<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        remarksTextarea.placeholder = 'Explain why the rescue could not be completed...';

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => remarksTextarea.focus(), 100);
    }
}

function closeSuccessRemarksModal() {
    document.getElementById('successRemarksModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function proceedToAnimalAddition() {
    const remarks = document.getElementById('successRescueRemarks').value.trim();

    if (!remarks) {
        // Show validation error in the remarks modal
        const textarea = document.getElementById('successRescueRemarks');
        textarea.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
        textarea.classList.remove('border-gray-200', 'focus:ring-green-200', 'focus:border-green-500');

        // Create temporary error message
        let errorDiv = document.getElementById('remarksError');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'remarksError';
            errorDiv.className = 'mt-2 flex items-start gap-2 bg-red-50 p-3 rounded-lg border border-red-300';
            errorDiv.innerHTML = `
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-red-700 font-medium">Please provide rescue remarks before proceeding.</p>
            `;
            textarea.parentNode.appendChild(errorDiv);
        }

        textarea.focus();
        return;
    }

    // Remove error styling if it exists
    const textarea = document.getElementById('successRescueRemarks');
    textarea.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
    textarea.classList.add('border-gray-200', 'focus:ring-green-200', 'focus:border-green-500');
    const errorDiv = document.getElementById('remarksError');
    if (errorDiv) errorDiv.remove();

    // Store remarks temporarily
    window.tempRescueRemarks = remarks;

    // Close remarks modal and open animal addition modal
    closeSuccessRemarksModal();
    openSuccessModal();
}

function closeRemarksModal() {
    document.getElementById('remarksModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    selectedStatus = '';
}

function submitStatusUpdate(event) {
    event.preventDefault();

    const remarks = document.getElementById('remarks').value.trim();

    if (!remarks) {
        // Show validation error in the remarks modal
        const textarea = document.getElementById('remarks');
        textarea.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
        textarea.classList.remove('border-gray-300', 'focus:ring-purple-500', 'focus:border-transparent');
        textarea.focus();
        return;
    }

    // Continue with form submission
    const form = document.getElementById('statusForm');

    // Create hidden inputs BEFORE closing modal
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = selectedStatus;

    const remarksInput = document.createElement('input');
    remarksInput.type = 'hidden';
    remarksInput.name = 'remarks';
    remarksInput.value = remarks;

    form.appendChild(statusInput);
    form.appendChild(remarksInput);

    // Close the remarks modal
    closeRemarksModal();

    // Show full-screen loading overlay with blur
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Lower map z-index to prevent it from showing above overlay
    const mapContainer = document.getElementById('map');
    if (mapContainer) {
        mapContainer.style.zIndex = '1';
    }

    // Submit form immediately (inputs already appended)
    form.submit();
}

function submitStatusDirectly(status) {
    const form = document.getElementById('statusForm');

    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = status;

    form.appendChild(statusInput);

    // Show loading state on the clicked button
    showStatusButtonLoading(status);

    // Lower map z-index to prevent it from showing above loading overlay
    const mapContainer = document.getElementById('map');
    if (mapContainer) {
        mapContainer.style.zIndex = '1';
    }

    form.submit();
}

function showStatusButtonLoading(status) {
    // Map status to button ID and text ID
    const buttonMap = {
        'Scheduled': { btnId: 'statusScheduledBtn', textId: 'statusScheduledText' },
        'In Progress': { btnId: 'statusProgressBtn', textId: 'statusProgressText' },
        'Success': { btnId: 'statusSuccessBtn', textId: 'statusSuccessText' },
        'Failed': { btnId: 'statusFailedBtn', textId: 'statusFailedText' }
    };

    const mapping = buttonMap[status];
    if (!mapping) return;

    const btn = document.getElementById(mapping.btnId);
    const textSpan = document.getElementById(mapping.textId);

    if (btn && textSpan) {
        // Disable all status buttons
        Object.values(buttonMap).forEach(m => {
            const button = document.getElementById(m.btnId);
            if (button) button.disabled = true;
        });

        // Show spinner in the clicked button
        btn.innerHTML = `
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Updating...</span>
        `;
    }
}

// ==================== VALIDATION ALERT FUNCTIONS ====================

function showValidationAlert(message) {
    const alert = document.getElementById('validationAlert');
    const messageElement = document.getElementById('validationMessage');

    messageElement.textContent = message;
    alert.classList.remove('hidden');

    // Scroll to the alert
    alert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    // Auto-hide after 5 seconds
    setTimeout(() => {
        hideValidationAlert();
    }, 5000);
}

function hideValidationAlert() {
    const alert = document.getElementById('validationAlert');
    alert.classList.add('hidden');
}

// ==================== ANIMAL FORM FUNCTIONS ====================

function updateAnimalAgeCategories() {
    const species = document.getElementById('animalSpecies').value;
    const ageSelect = document.getElementById('animalAge');

    ageSelect.innerHTML = '<option value="">Select age category</option>';

    let ageOptions = [];
    if (species === 'Cat') {
        ageOptions = [
            { value: 'kitten', label: '🐱 Kitten' },
            { value: 'adult', label: '🐈 Adult' },
            { value: 'senior', label: '🐈‍⬛ Senior' }
        ];
    } else if (species === 'Dog') {
        ageOptions = [
            { value: 'puppy', label: '🐶 Puppy' },
            { value: 'adult', label: '🐕 Adult' },
            { value: 'senior', label: '🦮 Senior' }
        ];
    }

    ageOptions.forEach(age => {
        let option = document.createElement("option");
        option.value = age.value;
        option.textContent = age.label;
        ageSelect.appendChild(option);
    });
}

function handleAnimalImagePreview(event) {
    const preview = document.getElementById('animalImagePreview');
    preview.innerHTML = '';
    const files = Array.from(event.target.files);

    if (files.length > 0) {
        // Store files temporarily for this animal
        window.tempAnimalImages = files;

        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `
                    <img src="${e.target.result}"
                         class="w-full h-24 object-cover rounded-xl border-2 border-purple-200 shadow-md"
                         alt="Preview ${index + 1}">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition rounded-xl flex items-center justify-center">
                        <span class="text-white text-sm font-semibold opacity-0 group-hover:opacity-100 transition">
                            Image ${index + 1}
                        </span>
                    </div>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
}

// ==================== SUCCESS MODAL FUNCTIONS ====================

function openSuccessModal() {
    currentStep = 1;
    currentAnimalIndex = 0;
    addedAnimals = [];

    document.getElementById('successModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    updateStepDisplay();
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    selectedStatus = '';
}

function updateStepDisplay() {
    // Hide all steps
    document.getElementById('step1Content').classList.add('hidden');
    document.getElementById('step2Content').classList.add('hidden');
    document.getElementById('step3Content').classList.add('hidden');

    // Hide validation alert when changing steps
    hideValidationAlert();

    // Update progress indicators
    updateProgressIndicator();

    // Show current step
    if (currentStep === 1) {
        document.getElementById('step1Content').classList.remove('hidden');
        document.getElementById('backBtn').classList.add('hidden');
        document.getElementById('nextBtn').classList.remove('hidden');
        document.getElementById('submitSuccessBtn').classList.add('hidden');
    } else if (currentStep === 2) {
        document.getElementById('step2Content').classList.remove('hidden');
        document.getElementById('backBtn').classList.remove('hidden');
        document.getElementById('nextBtn').classList.remove('hidden');
        document.getElementById('submitSuccessBtn').classList.add('hidden');
        updateAnimalProgress();
    } else if (currentStep === 3) {
        document.getElementById('step3Content').classList.remove('hidden');
        document.getElementById('backBtn').classList.remove('hidden');
        document.getElementById('nextBtn').classList.add('hidden');
        document.getElementById('submitSuccessBtn').classList.remove('hidden');
        displayAnimalsSummary();
    }
}

function updateProgressIndicator() {
    const step1 = document.getElementById('step1Indicator');
    const step2 = document.getElementById('step2Indicator');
    const step3 = document.getElementById('step3Indicator');

    // Step 1: Always completed (or active)
    if (currentStep === 1) {
        step1.innerHTML = `
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold shadow-lg">
                    1
                </div>
                <div class="w-0.5 h-12 bg-gray-300"></div>
            </div>
            <span class="text-sm font-medium text-gray-900 -mt-8">Count</span>
        `;
    } else {
        step1.innerHTML = `
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="w-0.5 h-12 bg-gray-300"></div>
            </div>
            <span class="text-sm font-medium text-gray-900 -mt-8">Count</span>
        `;
    }

    // Step 2: Active or completed
    if (currentStep === 2) {
        step2.innerHTML = `
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold shadow-lg">
                    2
                </div>
                <div class="w-0.5 h-12 bg-gray-300"></div>
            </div>
            <span class="text-sm font-medium text-gray-900 -mt-8">Add Animals</span>
        `;
    } else if (currentStep > 2) {
        step2.innerHTML = `
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="w-0.5 h-12 bg-gray-300"></div>
            </div>
            <span class="text-sm font-medium text-gray-900 -mt-8">Add Animals</span>
        `;
    } else {
        step2.innerHTML = `
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-gray-300 text-white flex items-center justify-center font-bold">
                    2
                </div>
                <div class="w-0.5 h-12 bg-gray-300"></div>
            </div>
            <span class="text-sm font-medium text-gray-400 -mt-8">Add Animals</span>
        `;
    }

    // Step 3: Active or pending
    if (currentStep === 3) {
        step3.innerHTML = `
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold shadow-lg">
                    3
                </div>
            </div>
            <span class="text-sm font-medium text-gray-900 -mt-8">Confirm</span>
        `;
    } else {
        step3.innerHTML = `
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-gray-300 text-white flex items-center justify-center font-bold">
                    3
                </div>
            </div>
            <span class="text-sm font-medium text-gray-400 -mt-8">Confirm</span>
        `;
    }
}

function updateAnimalProgress() {
    const progressText = document.getElementById('animalProgress');
    progressText.textContent = `Adding Animal ${currentAnimalIndex + 1} of ${totalAnimals}`;
}

function nextStep() {
    if (currentStep === 1) {
        // Validate animal count
        const count = parseInt(document.getElementById('animalCount').value);
        if (!count || count < 1 || count > 20) {
            showValidationAlert('Please enter a valid number of animals (1-20)');
            document.getElementById('animalCount').focus();
            return;
        }

        // Hide validation alert if shown
        hideValidationAlert();

        totalAnimals = count;
        currentAnimalIndex = 0;
        currentStep = 2;

        // Clear form for first animal
        clearAnimalForm();
        updateStepDisplay();
    } else if (currentStep === 2) {
        // Validate and save current animal
        if (!validateAnimalForm()) {
            return;
        }

        saveCurrentAnimal();

        // Check if we need to add more animals
        if (currentAnimalIndex < totalAnimals - 1) {
            currentAnimalIndex++;
            clearAnimalForm();
            updateStepDisplay();
        } else {
            // All animals added, move to confirmation
            currentStep = 3;
            updateStepDisplay();
        }
    }
}

function prevStep() {
    if (currentStep === 2) {
        if (currentAnimalIndex > 0) {
            // Go back to previous animal
            currentAnimalIndex--;
            loadAnimalData(currentAnimalIndex);
            updateStepDisplay();
        } else {
            // Go back to step 1
            currentStep = 1;
            updateStepDisplay();
        }
    } else if (currentStep === 3) {
        // Go back to last animal
        currentStep = 2;
        currentAnimalIndex = totalAnimals - 1;
        loadAnimalData(currentAnimalIndex);
        updateStepDisplay();
    }
}

function validateAnimalForm() {
    const name = document.getElementById('animalName').value.trim();
    const species = document.getElementById('animalSpecies').value;
    const gender = document.getElementById('animalGender').value;
    const age = document.getElementById('animalAge').value;
    const weight = document.getElementById('animalWeight').value;
    const healthDetails = document.getElementById('animalHealthDetails').value;
    const images = document.getElementById('animalImages').files;

    if (!name || !species || !gender || !age || !weight || !healthDetails) {
        showValidationAlert('Please fill in all required fields');

        // Focus on first empty field
        if (!name) document.getElementById('animalName').focus();
        else if (!species) document.getElementById('animalSpecies').focus();
        else if (!gender) document.getElementById('animalGender').focus();
        else if (!age) document.getElementById('animalAge').focus();
        else if (!weight) document.getElementById('animalWeight').focus();
        else if (!healthDetails) document.getElementById('animalHealthDetails').focus();

        return false;
    }

    if (parseFloat(weight) <= 0) {
        showValidationAlert('Please enter a valid weight greater than 0');
        document.getElementById('animalWeight').focus();
        return false;
    }

    if (!images || images.length === 0) {
        showValidationAlert('Please upload at least 1 image of the animal');
        document.getElementById('animalImages').focus();
        return false;
    }

    // Hide validation alert if shown (all validations passed)
    hideValidationAlert();

    return true;
}

function saveCurrentAnimal() {
    // Match Animal model fillable fields
    const animalData = {
        name: document.getElementById('animalName').value.trim(),
        species: document.getElementById('animalSpecies').value,
        gender: document.getElementById('animalGender').value,
        age: document.getElementById('animalAge').value,
        weight: parseFloat(document.getElementById('animalWeight').value),
        health_details: document.getElementById('animalHealthDetails').value,
        adoption_status: 'Not Adopted', // All rescued animals start as 'Not Adopted'
        rescueID: rescueId // Uses global rescueId variable from inline script
    };

    // Store images for this animal
    const images = document.getElementById('animalImages').files;
    if (images && images.length > 0) {
        animalImagesMap[currentAnimalIndex] = Array.from(images);
    }

    // Update or add animal data
    if (addedAnimals[currentAnimalIndex]) {
        addedAnimals[currentAnimalIndex] = animalData;
    } else {
        addedAnimals.push(animalData);
    }
}

function clearAnimalForm() {
    document.getElementById('animalName').value = '';
    document.getElementById('animalSpecies').value = '';
    document.getElementById('animalGender').value = '';
    document.getElementById('animalAge').value = '';
    document.getElementById('animalWeight').value = '';
    document.getElementById('animalHealthDetails').value = '';
    document.getElementById('animalImages').value = '';
    document.getElementById('animalImagePreview').innerHTML = '';

    // Update age categories when species is cleared
    updateAnimalAgeCategories();
}

function loadAnimalData(index) {
    if (addedAnimals[index]) {
        const animal = addedAnimals[index];
        document.getElementById('animalName').value = animal.name;
        document.getElementById('animalSpecies').value = animal.species;
        updateAnimalAgeCategories(); // Update age options based on species
        document.getElementById('animalGender').value = animal.gender;
        document.getElementById('animalAge').value = animal.age;
        document.getElementById('animalWeight').value = animal.weight;
        document.getElementById('animalHealthDetails').value = animal.health_details;

        // Restore image preview if exists
        if (animalImagesMap[index] && animalImagesMap[index].length > 0) {
            const preview = document.getElementById('animalImagePreview');
            preview.innerHTML = '';
            animalImagesMap[index].forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}"
                             class="w-full h-24 object-cover rounded-xl border-2 border-purple-200 shadow-md"
                             alt="Preview ${idx + 1}">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition rounded-xl flex items-center justify-center">
                            <span class="text-white text-sm font-semibold opacity-0 group-hover:opacity-100 transition">
                                Image ${idx + 1}
                            </span>
                        </div>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    }
}

function displayAnimalsSummary() {
    const summaryContainer = document.getElementById('animalsSummary');
    summaryContainer.innerHTML = '';

    // Update total animal count in rescue info
    const totalAnimalCountElement = document.getElementById('totalAnimalCount');
    if (totalAnimalCountElement) {
        totalAnimalCountElement.textContent = addedAnimals.length;
    }

    addedAnimals.forEach((animal, index) => {
        const healthColors = {
            'Healthy': 'bg-green-100 text-green-800 border-green-300',
            'Sick': 'bg-red-100 text-red-800 border-red-300',
            'Need Observation': 'bg-yellow-100 text-yellow-800 border-yellow-300'
        };

        const genderIcon = animal.gender === 'Male' ? '♂️' : '♀️';
        const genderColor = animal.gender === 'Male' ? 'text-blue-600' : 'text-pink-600';
        const speciesIcon = animal.species === 'Dog' ? '🐕' : '🐈';

        // Format age category for display
        const ageDisplay = animal.age.charAt(0).toUpperCase() + animal.age.slice(1);

        // Get image count for this animal
        const imageCount = animalImagesMap[index] ? animalImagesMap[index].length : 0;

        const animalCard = `
            <div class="bg-gradient-to-r from-white to-purple-50 border-2 border-purple-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-gradient-to-br from-purple-600 to-indigo-600 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold text-base shadow-md">
                            ${index + 1}
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                                <i class="fas fa-paw text-purple-600 text-sm"></i>
                                ${animal.name}
                                <span class="${genderColor} text-2xl">${genderIcon}</span>
                            </h4>
                            <p class="text-xs text-gray-500 mt-0.5">Animal #${index + 1} • ${animal.weight}kg • ${imageCount} photo(s)</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold border-2 shadow-sm ${healthColors[animal.health_details] || 'bg-gray-100 text-gray-800 border-gray-300'}">
                        ${animal.health_details}
                    </span>
                </div>
                <div class="grid grid-cols-3 gap-3 bg-white bg-opacity-60 p-4 rounded-lg border border-purple-100">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs text-gray-500">Species</span>
                        <span class="text-sm font-bold text-gray-900">${speciesIcon} ${animal.species}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs text-gray-500">Age</span>
                        <span class="text-sm font-bold text-gray-900">${ageDisplay}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs text-gray-500">Gender</span>
                        <span class="text-sm font-bold text-gray-900">${genderIcon} ${animal.gender}</span>
                    </div>
                </div>
            </div>
        `;

        summaryContainer.innerHTML += animalCard;
    });
}

function submitSuccessRescue() {
    // Validate confirmation checkbox
    const confirmCheck = document.getElementById('confirmCheck');
    if (!confirmCheck.checked) {
        showValidationAlert('Please confirm that all information is accurate before submitting');
        confirmCheck.focus();
        return;
    }

    // Use remarks from the first modal
    const remarks = window.tempRescueRemarks || '';
    if (!remarks) {
        showValidationAlert('Rescue remarks are missing. Please restart the process.');
        return;
    }

    // Hide validation alert before submission
    hideValidationAlert();

    // Prepare form data using FormData for file uploads
    const form = document.getElementById('statusForm');
    const formData = new FormData(form);

    // Add status
    formData.append('status', 'Success');

    // Add remarks
    formData.append('remarks', remarks);

    // Add animals data as JSON
    formData.append('animals', JSON.stringify(addedAnimals));

    // Add images for each animal with proper file names
    const timestamp = Date.now();

    Object.keys(animalImagesMap).forEach(index => {
        const images = animalImagesMap[index];
        const animalNumber = parseInt(index) + 1; // 1-indexed for display

        images.forEach((file, fileIndex) => {
            // Extract file extension
            const extension = file.name.split('.').pop().toLowerCase();

            // Generate proper file name: rescue_{rescueID}_animal_{animalNumber}_img_{imageNumber}_{timestamp}.{extension}
            const newFileName = `rescue_${rescueId}_animal_${animalNumber}_img_${fileIndex + 1}_${timestamp}.${extension}`;

            // Create new File object with the proper name
            const renamedFile = new File([file], newFileName, {
                type: file.type,
                lastModified: file.lastModified
            });

            formData.append(`animal_${index}_images[]`, renamedFile);
        });
    });

    // Close modal
    closeSuccessModal();

    // Show loading overlay
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Lower map z-index
    const mapContainer = document.getElementById('map');
    if (mapContainer) {
        mapContainer.style.zIndex = '1';
    }

    // Submit form using AJAX since we have file uploads
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => {
        if (response.ok) {
            // Reload page on success
            window.location.reload();
        } else {
            throw new Error('Submission failed');
        }
    })
    .catch(error => {
        loadingOverlay.classList.add('hidden');
        document.body.style.overflow = 'auto';

        // Show error in modal
        openSuccessModal();
        showValidationAlert('An error occurred while submitting. Please try again.');
        console.error(error);
    });
}
