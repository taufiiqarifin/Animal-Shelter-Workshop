# Multi-Step Adoption Flow Implementation

## Overview

This document describes the implementation of the multi-step modal flow for the adoption process in the Animal Shelter Workshop application.

## Features Implemented

### 1. Multi-Step Modal Design

A modern, user-friendly 3-step modal flow based on the Step-by-step.png template:

**Step 1: Booking Details**
- View booking status and appointment information
- Review animals in the booking
- See user information
- Important reminders for the appointment
- Option to cancel booking

**Step 2: Select Animals**
- Visual selection interface with checkboxes
- Display animal photos, details, and health records
- Real-time fee calculation per animal
- Fee breakdown showing:
  - Base fee (species-specific)
  - Medical records fee (count × RM 10)
  - Vaccination fee (count × RM 20)
  - Total per animal
- Selection summary showing count and estimated total
- Validation: At least one animal must be selected

**Step 3: Confirm & Pay**
- Review selected animals with complete fee breakdown
- Display grand total
- Payment gateway information (ToyyibPay)
- Terms and conditions with checkbox
- Submit button to proceed to payment

### 2. Visual Design

**Left Sidebar:**
- Step indicators with progress tracking
- Active step highlighting
- Completed step checkmarks
- Progress bar (33% → 66% → 100%)
- Purple gradient theme matching the application

**Right Content Area:**
- Dynamic step title and subtitle
- Scrollable content area
- Clean, modern card-based layout
- Responsive design (mobile-friendly)

**Navigation:**
- Back button (hidden on step 1)
- Next button (hidden on step 3)
- Submit button on step 3
- Escape key to close modal
- Click outside to close

### 3. File Structure

#### Blade Components

```
resources/views/booking-adoption/
├── main.blade.php (updated to use new modal)
└── partials/
    ├── booking-modal-steps.blade.php (main multi-step modal)
    ├── step1-details.blade.php (booking details view)
    ├── step2-select.blade.php (animal selection)
    ├── step3-confirm.blade.php (confirmation & payment)
    └── cancel-modal.blade.php (cancel confirmation)
```

#### JavaScript

```
public/js/booking-modal.js (new file)
```

All modal logic extracted to a single, reusable JavaScript file.

### 4. JavaScript Functions

**Modal Control:**
- `openBookingModal(bookingId)` - Open modal, initialize to step 1
- `closeBookingModal(bookingId)` - Close modal, reset state
- `nextStep(bookingId)` - Navigate to next step with validation
- `previousStep(bookingId)` - Navigate to previous step

**Animal Selection:**
- `updateSelectionSummary(bookingId)` - Update count and total in step 2
- `populateStep3(bookingId)` - Generate step 3 content from selections

**Utilities:**
- `updateStepDisplay(bookingId)` - Update UI for current step
- `showAlert(type, message)` - Display toast notifications
- `openCancelModal(bookingId)` - Open cancel confirmation
- `closeCancelModal(bookingId)` - Close cancel confirmation

**Event Listeners:**
- Animal checkbox changes → Update summary
- Form submissions → Show loading spinners
- Escape key → Close modals
- Click outside → Close modals

### 5. Backend Integration

**No Changes Required!** The existing `BookingAdoptionController::confirm()` method already handles:

```php
// Validation
$validated = $request->validate([
    'animal_ids' => 'required|array|min:1',
    'animal_ids.*' => 'required|exists:shafiqah.animal,id',
    'total_fee' => 'required|numeric|min:0',
    'agree_terms' => 'required|accepted',
]);

// Update booking status
$booking->update([
    'totalFee' => $validated['total_fee'],
    'status' => 'Confirmed',
]);

// Redirect to payment gateway
return $this->createBill($booking, $validated['total_fee'], $selectedAnimals);
```

The multi-step modal form submits exactly these fields from Step 3.

### 6. Payment Flow

```
User clicks "View Details" button
    ↓
Step 1: Review booking details
    ↓
Click "Next"
    ↓
Step 2: Select animals to adopt
    ↓
Click "Next" (validates selection)
    ↓
Step 3: Review fees, accept terms
    ↓
Click "Proceed to Payment"
    ↓
Form submits to BookingAdoptionController::confirm()
    ↓
Booking status updated to "Confirmed"
    ↓
Redirect to ToyyibPay payment gateway
    ↓
User completes payment
    ↓
Return to booking page with payment status modal
    ↓
Success: Booking marked "Completed", animals marked "Adopted"
Failure: Booking remains "Confirmed", can retry payment
```

### 7. Fee Calculation

**Fee Structure (from controller):**
```php
$speciesBaseFees = [
    'dog' => 20,
    'cat' => 10,
];
$medicalRate = 10;
$vaccinationRate = 20;

// Per animal:
$animalTotal = $baseFee + ($medicalCount × 10) + ($vaccinationCount × 20);
```

**Example:**
- Dog with 2 medical records and 3 vaccinations:
  - Base: RM 20
  - Medical: RM 20 (2 × 10)
  - Vaccination: RM 60 (3 × 20)
  - **Total: RM 100**

### 8. User Experience Improvements

**Loading States:**
- Loading spinners on form submissions
- Disabled buttons during processing
- Clear visual feedback

**Validation:**
- Client-side validation before moving to next step
- Toast notifications for errors
- Server-side validation as backup

**Accessibility:**
- Keyboard navigation (Escape to close)
- Clear visual indicators
- Proper ARIA labels
- Focus management

**Responsive Design:**
- Mobile-friendly layout
- Stacked columns on small screens
- Touch-friendly buttons
- Scrollable content areas

### 9. Payment Status Modal

**Unchanged** - The existing payment status modal (lines 418-557 in main.blade.php) continues to work:

- Displays after returning from ToyyibPay
- Shows success or failure status
- Lists adopted animals
- Displays payment details (billcode, reference number)
- Shows next steps for successful adoptions

### 10. Cancel Booking Flow

Enhanced cancel confirmation modal with:
- Clear warning message
- List of consequences
- Alternative suggestion (reschedule)
- Two-button confirmation (Keep / Cancel)
- Loading state during cancellation
- Form submission to `BookingAdoptionController::cancel()`

## Files Modified

1. **resources/views/booking-adoption/main.blade.php**
   - Updated "View Details" button to call `openBookingModal()`
   - Changed modal include to use new component
   - Removed old JavaScript code
   - Added script tag for booking-modal.js

## Files Created

1. **resources/views/booking-adoption/partials/booking-modal-steps.blade.php**
   - Main multi-step modal structure
   - Left sidebar with step indicators
   - Right content area with step switching
   - Navigation buttons

2. **resources/views/booking-adoption/partials/step1-details.blade.php**
   - Booking details view
   - Status badge
   - Appointment information
   - Animals list
   - User information
   - Important reminders
   - Cancel button

3. **resources/views/booking-adoption/partials/step2-select.blade.php**
   - Animal selection grid
   - Checkbox interface
   - Fee breakdown per animal
   - Real-time selection summary
   - Visual feedback for selected animals

4. **resources/views/booking-adoption/partials/step3-confirm.blade.php**
   - Selected animals summary
   - Complete fee breakdown
   - Payment information
   - Terms and conditions
   - Form submission to backend

5. **resources/views/booking-adoption/partials/cancel-modal.blade.php**
   - Cancel confirmation modal
   - Warning messages
   - Alternative suggestions
   - Form submission

6. **public/js/booking-modal.js**
   - All modal JavaScript logic
   - Event listeners
   - Step navigation
   - Fee calculations
   - Form handling

## Testing Checklist

- [ ] Open booking modal from "View Details" button
- [ ] Navigate through all 3 steps
- [ ] Go back to previous steps
- [ ] Select/deselect animals in step 2
- [ ] Verify fee calculations are correct
- [ ] Try to proceed without selecting animals (should show error)
- [ ] Verify step 3 shows correct selected animals
- [ ] Check fee breakdown in step 3
- [ ] Accept terms and submit
- [ ] Verify redirect to ToyyibPay
- [ ] Complete payment (success scenario)
- [ ] Verify booking marked as "Completed"
- [ ] Verify payment status modal displays
- [ ] Test cancel booking flow
- [ ] Close modal with Escape key
- [ ] Close modal by clicking outside
- [ ] Test on mobile devices
- [ ] Test with multiple bookings on same page

## Compatibility

- **Laravel Version:** 11.x
- **PHP Version:** 8.2+
- **Browsers:** Modern browsers (Chrome, Firefox, Safari, Edge)
- **Mobile:** iOS Safari, Android Chrome
- **Database:** Works with existing distributed database architecture

## Maintenance Notes

### Adding More Steps

To add a fourth step:

1. Update step indicators in `booking-modal-steps.blade.php`
2. Create `step4-newstep.blade.php` component
3. Update `updateStepDisplay()` in `booking-modal.js`
4. Update progress calculation (step / 4 × 100)

### Modifying Fee Structure

Update both:
1. `BookingAdoptionController.php` (server-side)
2. `show-modal.blade.php` PHP variables (for initial calculation)
3. JavaScript uses data attributes from server-rendered values

### Changing Payment Gateway

Modify:
1. `BookingAdoptionController::createBill()` for new gateway
2. Step 3 payment information text
3. Payment status callback handling

## Known Limitations

1. **Browser Compatibility:** IE11 not supported (uses modern JavaScript)
2. **Offline Mode:** Payment requires internet connection
3. **Session Timeout:** Long delays may cause session expiration
4. **File Size:** Large images may slow modal rendering

## Future Enhancements

Possible improvements:
- Add step 2.5 for custom remarks per animal
- Implement "Save for later" functionality
- Add email preview before payment
- Include estimated pickup date
- Support for partial payments
- Bulk booking discount calculations
- Payment plan options
- Gift adoption certificates

## Support

For issues or questions:
- Check browser console for JavaScript errors
- Verify database connections are online
- Check Laravel logs for backend errors
- Review payment gateway logs for transaction issues

---

**Implementation Date:** December 2025
**Author:** Claude AI Assistant
**Version:** 1.0
