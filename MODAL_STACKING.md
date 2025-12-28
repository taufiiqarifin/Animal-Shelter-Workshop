# Modal Stacking & Z-Index Management

## Overview

The adoption flow uses **layered modals** where confirmation dialogs can appear on top of the main booking modal. This document explains how modal stacking is managed to prevent conflicts.

## 🏗️ Modal Hierarchy

```
┌─────────────────────────────────────────┐
│  Layer 4 (z-100): Loading Overlay       │  ← Always on top
├─────────────────────────────────────────┤
│  Layer 3 (z-60):  Cancel Modal          │  ← Confirmation dialog
├─────────────────────────────────────────┤
│  Layer 2 (z-50):  Booking Modal         │  ← Main multi-step modal
├─────────────────────────────────────────┤
│  Layer 1 (z-50):  Payment Status Modal  │  ← After payment
└─────────────────────────────────────────┘
```

## 🎯 User Flow with Stacked Modals

### Scenario: User Cancels a Booking

```
1. User clicks "View Details"
   └── Booking Modal opens (z-50)
       └── Shows Step 1 (Booking Details)

2. User clicks "Cancel Booking" button
   └── Cancel Modal opens ON TOP (z-60)
       └── Booking Modal stays open underneath
       └── User sees confirmation dialog

3. User clicks "No, Keep Booking"
   └── Cancel Modal closes
       └── Booking Modal remains open
       └── User returns to Step 1

4. User clicks X to close booking
   └── Booking Modal closes
       └── Back to bookings page
```

## ✅ Fixed Issues

### Problem 1: Click Outside Closes All Modals

**Before Fix:**
```javascript
// Clicking outside would close BOTH modals
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-backdrop')) {
        e.target.classList.add('hidden'); // Closes any modal
    }
});
```

**After Fix:**
```javascript
// Now only closes the TOP-MOST modal
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-backdrop')) {
        const visibleModals = Array.from(document.querySelectorAll('.modal-backdrop:not(.hidden)'));

        // Find modal with highest z-index
        const topModal = visibleModals.reduce((top, current) => {
            const topZ = parseInt(window.getComputedStyle(top).zIndex) || 0;
            const currentZ = parseInt(window.getComputedStyle(current).zIndex) || 0;
            return currentZ > topZ ? current : top;
        });

        // Only close if clicked modal is the top-most one
        if (e.target === topModal) {
            topModal.classList.add('hidden');
        }
    }
});
```

### Problem 2: Escape Key Closes All Modals

**Before Fix:**
```javascript
// Pressing ESC would close BOTH modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-backdrop:not(.hidden)').forEach(modal => {
            modal.classList.add('hidden'); // Closes all!
        });
    }
});
```

**After Fix:**
```javascript
// Now only closes the TOP-MOST modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const visibleModals = Array.from(document.querySelectorAll('.modal-backdrop:not(.hidden)'));

        // Find modal with highest z-index
        const topModal = visibleModals.reduce((top, current) => {
            const topZ = parseInt(window.getComputedStyle(top).zIndex) || 0;
            const currentZ = parseInt(window.getComputedStyle(current).zIndex) || 0;
            return currentZ > topZ ? current : top;
        });

        // Close only the top-most modal
        topModal.classList.add('hidden');
    }
});
```

### Problem 3: Body Scroll Restoration

**Issue:** When closing the top modal, body scroll was restored even though a modal was still open underneath.

**Fix:**
```javascript
function closeCancelModal(bookingId) {
    const modal = document.getElementById(`cancelConfirmModal-${bookingId}`);
    modal.classList.add('hidden');

    // Check if any other modals are still open
    const remainingModals = document.querySelectorAll('.modal-backdrop:not(.hidden)');

    // Only restore scroll if NO modals are open
    if (remainingModals.length === 0) {
        document.body.style.overflow = 'auto';
    }
}
```

## 🎨 Visual Representation

### Modal Stack (Cancel Flow)

```
┌──────────────────────────────────────────────┐
│ ░░░░░░░░░░░░ Loading Overlay ░░░░░░░░░░░░░░ │ z-100
│ ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░ │
│                                              │
│    ┌────────────────────────────────┐       │
│    │  Cancel Confirmation Modal     │       │ z-60
│    │  ┌──────────────────────────┐  │       │
│    │  │ ⚠️ Cancel Booking        │  │       │
│    │  │                          │  │       │
│    │  │ Are you sure?            │  │       │
│    │  │                          │  │       │
│    │  │ [No] [Yes, Cancel]       │  │       │
│    │  └──────────────────────────┘  │       │
│    └────────────────────────────────┘       │
│    │                                 │       │
│    │  Booking Modal (underneath)     │       │ z-50
│    │  ┌──────────────────────────┐  │       │
│    │  │ Step 1: Booking Details  │  │       │
│    │  │                          │  │       │
│    │  │ [Cancel Booking] ← Clicked│  │      │
│    │  └──────────────────────────┘  │       │
│    └─────────────────────────────────┘      │
└──────────────────────────────────────────────┘
```

## 🔧 Implementation Details

### Z-Index Values

| Element | Z-Index | Purpose |
|---------|---------|---------|
| Loading Overlay | 100 | Always appears on top |
| Cancel Modal | 60 | Appears above booking modal |
| Booking Modal | 50 | Main content modal |
| Payment Status Modal | 50 | Separate from booking flow |

### Modal Classes

All modals use the `.modal-backdrop` class for unified handling:

```html
<div class="modal-backdrop hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[50]">
    <!-- Modal content -->
</div>
```

### Preventing Backdrop Clicks

The inner content uses `onclick="event.stopPropagation()"` to prevent closing when clicking inside:

```html
<div class="bg-white rounded-2xl" onclick="event.stopPropagation()">
    <!-- Clicking here won't close the modal -->
</div>
```

## 🧪 Testing Scenarios

### Test 1: Cancel Flow
```
✓ Open booking modal
✓ Click "Cancel Booking"
✓ Cancel modal appears on top
✓ Click outside cancel modal → Only cancel modal closes
✓ Booking modal still open
✓ Press ESC → Booking modal closes
```

### Test 2: Escape Key
```
✓ Open booking modal
✓ Click "Cancel Booking"
✓ Press ESC once → Only cancel modal closes
✓ Press ESC again → Booking modal closes
```

### Test 3: Multiple Modals
```
✓ Open booking modal (z-50)
✓ Click "Cancel Booking" (z-60 opens)
✓ Loading appears during cancellation (z-100 on top)
✓ All three layers visible
✓ Loading is on top of everything
```

### Test 4: Scroll Lock
```
✓ Open booking modal → Body scroll locked
✓ Open cancel modal → Body scroll still locked
✓ Close cancel modal → Body scroll still locked (booking open)
✓ Close booking modal → Body scroll unlocked
```

## 📋 Modal State Management

### Global State

```javascript
// Current step tracking
const currentSteps = {};

// Example: currentSteps[123] = 2 (booking #123 is on step 2)
```

### Modal Visibility Check

```javascript
// Get all visible modals
const visibleModals = document.querySelectorAll('.modal-backdrop:not(.hidden)');

// Check if any modals are open
const hasOpenModals = visibleModals.length > 0;

// Get top-most modal
const topModal = Array.from(visibleModals).reduce((top, current) => {
    const topZ = parseInt(window.getComputedStyle(top).zIndex) || 0;
    const currentZ = parseInt(window.getComputedStyle(current).zIndex) || 0;
    return currentZ > topZ ? current : top;
});
```

## 🎯 Best Practices

### DO ✅

- **Use explicit z-index values** for clear hierarchy
- **Check for open modals** before restoring scroll
- **Close only top-most modal** on ESC or click outside
- **Use event.stopPropagation()** on modal content
- **Test nested modal scenarios** thoroughly

### DON'T ❌

- **Don't use same z-index** for different modal types
- **Don't restore scroll** if modals are still open
- **Don't close all modals** on ESC (only top one)
- **Don't forget backdrop click prevention** on inner content
- **Don't assume only one modal** will be open

## 🐛 Common Issues

### Issue: Both Modals Close on Click Outside

**Cause:** Event bubbling closes all modals with `modal-backdrop` class

**Solution:** Check z-index and only close top-most modal

### Issue: Can't Close Cancel Modal

**Cause:** Click event propagating to inner content

**Solution:** Add `onclick="event.stopPropagation()"` to modal content

### Issue: Body Scroll Unlocks Prematurely

**Cause:** Scroll restored when closing top modal while bottom modal is open

**Solution:** Check `remainingModals.length` before restoring scroll

### Issue: Loading Overlay Appears Behind Modal

**Cause:** Z-index not high enough

**Solution:** Set loading overlay to z-100 (highest)

## 🔍 Debugging

### Check Open Modals

```javascript
// In browser console
document.querySelectorAll('.modal-backdrop:not(.hidden)');
// Returns: NodeList of all visible modals
```

### Check Z-Index

```javascript
// In browser console
const modal = document.getElementById('bookingModal-123');
window.getComputedStyle(modal).zIndex;
// Returns: "50"
```

### Check Body Scroll Lock

```javascript
// In browser console
document.body.style.overflow;
// Returns: "hidden" (locked) or "" (unlocked)
```

## 📱 Mobile Considerations

**Touch Events:**
- Touch outside still works (uses click event)
- Swipe down doesn't close modal (intentional)
- Pinch zoom disabled on modal content

**Keyboard:**
- Mobile keyboards don't trigger ESC key
- Use close button (X) on mobile
- Back button on Android should close top modal (browser dependent)

## 🚀 Performance

**Modal Rendering:**
- Modals are pre-rendered (not created dynamically)
- Show/hide with CSS classes (no DOM manipulation)
- Smooth transitions with CSS animations

**Event Listeners:**
- Single global listener for click outside
- Single global listener for ESC key
- Form-specific listeners for submissions

**Z-Index Calculation:**
- Calculated only when needed (on modal interaction)
- Cached result used for same action
- Minimal DOM queries

## 🎨 Customization

### Adding a New Modal Layer

```html
<!-- New modal with z-70 (between cancel and loading) -->
<div class="modal-backdrop hidden fixed inset-0 bg-black bg-opacity-50 z-[70]">
    <div class="bg-white rounded-2xl" onclick="event.stopPropagation()">
        <!-- Content -->
    </div>
</div>
```

### Changing Z-Index Hierarchy

Update in both places:
1. **Blade templates** (class="z-[XX]")
2. **Documentation** (this file)

```
New hierarchy:
Loading: z-100
New Layer: z-70
Cancel: z-60
Booking: z-50
```

## 📚 Related Files

- `resources/views/booking-adoption/partials/booking-modal-steps.blade.php`
- `resources/views/booking-adoption/partials/cancel-modal.blade.php`
- `resources/views/booking-adoption/partials/loading-overlay.blade.php`
- `public/js/booking-modal.js`

## 🔗 See Also

- [ADOPTION_FLOW_IMPLEMENTATION.md](ADOPTION_FLOW_IMPLEMENTATION.md) - Complete adoption flow
- [LOADING_INDICATORS.md](LOADING_INDICATORS.md) - Loading overlay details

---

**Last Updated:** December 2025
**Version:** 1.0
**Author:** Claude AI Assistant
