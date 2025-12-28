# Loading Indicators Implementation

## Overview

Comprehensive loading indicators have been added throughout the adoption flow to provide clear visual feedback during all asynchronous operations.

## 🎯 Loading Indicators Locations

### 1. **Global Loading Overlay** ✨

**Component:** `resources/views/booking-adoption/partials/loading-overlay.blade.php`

**Features:**
- Full-screen semi-transparent overlay
- Animated multi-ring spinner
- Customizable title and message
- Pulsing center icon
- Animated progress dots
- Smooth fade-in/fade-out animations

**Design:**
```
┌─────────────────────────────┐
│     [Animated Spinner]       │
│                              │
│   Processing...              │
│   Please wait while we       │
│   process your request       │
│                              │
│        • • •                 │
└─────────────────────────────┘
```

**Used For:**
- ✅ Calculating fees (Step 2 → Step 3)
- ✅ Cancelling bookings
- ✅ Preparing payment redirect
- ✅ Any long-running operation

### 2. **Button Loading Spinners** 🔄

**Inline Spinners on Buttons:**

All submit buttons show inline spinners when clicked:

**Cancel Booking Button:**
```html
[Spinner] Cancelling...
```

**Proceed to Payment Button:**
```html
[Spinner] Processing...
```

**Design:**
- Spinner replaces button content
- Button disabled during operation
- Prevents double-submission
- Clear visual feedback

### 3. **Step Navigation Loading** 📊

**When Moving from Step 2 → Step 3:**

```javascript
showLoading(
    'Calculating Fees...',
    'Preparing your adoption summary'
);
```

**Duration:** 500ms (gives smooth transition feel)

**What Happens:**
1. User clicks "Next" on Step 2
2. Validates animal selection
3. Shows loading overlay
4. Calculates all fees
5. Populates Step 3 content
6. Hides loading overlay
7. Displays Step 3

## 🎨 Visual Design

### Loading Overlay Design

**Colors:**
- Background: Black with 60% opacity
- Card: White with rounded corners
- Primary: Purple (#7C3AED)
- Accent: Purple variations

**Animations:**
- Outer ring spinner: 1s rotation
- Inner pulse: 2s fade in/out
- Progress dots: 1.4s bounce (staggered)
- Card entrance: Scale + fade

**Layout:**
```
Fixed Overlay (z-index: 100)
    └── White Card (max-width: 28rem)
        ├── Spinner Section (24x24)
        │   ├── Static ring (purple-200)
        │   ├── Rotating ring (purple-600)
        │   └── Pulsing center with icon
        ├── Title (text-2xl, bold)
        ├── Message (text-gray-600)
        └── Animated dots (3 dots bouncing)
```

### Button Spinner Design

**Standard Button Spinner:**
```html
<svg class="animate-spin h-5 w-5">
    <circle opacity="0.25" stroke="currentColor"/>
    <path opacity="0.75" fill="currentColor"/>
</svg>
```

**Sizes:**
- Small buttons: h-4 w-4
- Medium buttons: h-5 w-5
- Large buttons: h-6 w-6

## 📁 Files Added/Modified

### New Files

1. **`resources/views/booking-adoption/partials/loading-overlay.blade.php`**
   - Global loading overlay component
   - Self-contained with styles
   - Reusable across pages

2. **`resources/views/booking-adoption/partials/inline-spinner.blade.php`**
   - Reusable inline spinner component
   - Configurable size and color
   - Can be included anywhere

### Modified Files

1. **`public/js/booking-modal.js`**
   - Added `showLoading(title, message)` function
   - Added `hideLoading()` function
   - Updated `nextStep()` to show loading when moving to step 3
   - Updated cancel form handler to show loading
   - Updated confirm form handler to show loading

2. **`resources/views/booking-adoption/main.blade.php`**
   - Included loading overlay component
   - Loading overlay available on all booking pages

## 🔧 JavaScript Functions

### showLoading(title, message)

**Purpose:** Display the global loading overlay

**Parameters:**
- `title` (string): Main loading message (default: "Processing...")
- `message` (string): Detailed message (default: "Please wait...")

**Example:**
```javascript
showLoading('Calculating Fees...', 'Preparing your adoption summary');
```

### hideLoading()

**Purpose:** Hide the global loading overlay

**Animation:** 300ms fade-out before removing from DOM

**Example:**
```javascript
setTimeout(() => {
    hideLoading();
}, 500);
```

### Usage in Form Submissions

**Cancel Booking:**
```javascript
form.addEventListener('submit', function(e) {
    showLoading('Cancelling Booking...', 'Please wait while we cancel your booking');

    // Update button
    submitBtn.innerHTML = `
        <svg class="animate-spin h-5 w-5">...</svg>
        <span>Cancelling...</span>
    `;

    return true; // Allow form to submit
});
```

**Confirm Adoption:**
```javascript
form.addEventListener('submit', function(e) {
    showLoading('Preparing Payment...', 'Redirecting you to our secure payment gateway');

    // Update button
    submitBtn.innerHTML = `
        <svg class="animate-spin h-6 w-6">...</svg>
        <span>Processing...</span>
    `;

    return true; // Allow form to submit
});
```

## 🎭 Animation Specifications

### Spinner Rotation
```css
@keyframes spin {
    to { transform: rotate(360deg); }
}
.animate-spin { animation: spin 1s linear infinite; }
```

### Pulse Animation
```css
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
.animate-pulse { animation: pulse 2s ease-in-out infinite; }
```

### Bounce Animation
```css
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}
.animate-bounce { animation: bounce 1.4s ease-in-out infinite; }
```

### Fade In Animation
```css
.loading-overlay.show #loadingContent {
    transform: scale(1);
    opacity: 1;
}
```

## 📊 Loading States Timeline

### Complete User Journey with Loading

```
1. User clicks "View Details"
   └── Modal opens instantly

2. User navigates to Step 2
   └── Instant transition

3. User selects animals
   └── Real-time fee updates (no loading needed)

4. User clicks "Next" (Step 2 → Step 3)
   └── [LOADING: "Calculating Fees..." - 500ms]
   └── Step 3 appears with populated data

5. User clicks "Proceed to Payment"
   └── [LOADING: "Preparing Payment..." - until redirect]
   └── Button shows spinner
   └── Form submits to backend
   └── Redirect to ToyyibPay

6. User returns from payment
   └── Payment status modal appears

7. User clicks "Cancel Booking"
   └── Cancel confirmation modal appears

8. User confirms cancellation
   └── [LOADING: "Cancelling Booking..." - until completion]
   └── Button shows spinner
   └── Form submits
   └── Redirect to booking page
```

## 🎯 Loading Messages

### Message Examples

| Action | Title | Message |
|--------|-------|---------|
| Calculate Fees | "Calculating Fees..." | "Preparing your adoption summary" |
| Cancel Booking | "Cancelling Booking..." | "Please wait while we cancel your booking" |
| Confirm Payment | "Preparing Payment..." | "Redirecting you to our secure payment gateway" |
| Processing | "Processing..." | "Please wait while we process your request" |
| Saving | "Saving Changes..." | "Your changes are being saved" |

### Customization

To use custom messages:
```javascript
showLoading('Custom Title', 'Custom detailed message');
```

## 🔍 Z-Index Hierarchy

```
Payment Status Modal:    z-50
Booking Modal:           z-50
Cancel Modal:            z-60 (appears above booking modal)
Loading Overlay:         z-100 (appears above everything)
```

## 💡 Best Practices

### When to Show Loading

✅ **DO show loading for:**
- Form submissions (> 500ms)
- Step transitions with data processing
- Payment redirects
- Cancellation confirmations
- Multi-step calculations

❌ **DON'T show loading for:**
- Instant UI updates
- Simple step navigation
- Checkbox selections
- Modal open/close
- Client-side validations

### Loading Duration Guidelines

- **< 500ms:** No loading needed (instant feel)
- **500ms - 2s:** Show button spinner only
- **2s - 5s:** Show button spinner + overlay
- **> 5s:** Show overlay with detailed message

### Preventing Loading Overlap

```javascript
// Always hide previous loading before showing new one
hideLoading();
setTimeout(() => {
    showLoading('New Action...', 'New message');
}, 50);
```

## 🚀 Performance

**Optimizations:**
- CSS animations (GPU-accelerated)
- Minimal DOM manipulation
- Efficient z-index stacking
- Delayed removal for smooth transitions
- No JavaScript-based animations (except timing)

**Browser Compatibility:**
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## 🐛 Troubleshooting

### Loading Won't Hide

**Cause:** JavaScript error preventing hideLoading() call

**Solution:**
```javascript
// Ensure hideLoading is called in finally block
try {
    // ... operation
} catch (error) {
    console.error(error);
} finally {
    hideLoading();
}
```

### Loading Appears Behind Modal

**Cause:** Z-index conflict

**Solution:** Loading overlay has z-100, highest in the stack

### Loading Not Centered

**Cause:** Parent container has positioning

**Solution:** Loading overlay uses `fixed` positioning (ignores parents)

## 📱 Responsive Design

**Mobile (< 768px):**
- Loading card: max-width 90% of screen
- Spinner: Same size
- Text: Slightly smaller (responsive)
- Padding: Reduced for small screens

**Tablet (768px - 1024px):**
- Loading card: max-width 28rem
- Standard sizing

**Desktop (> 1024px):**
- Loading card: max-width 28rem
- Full animations

## 🎨 Theming

To customize colors, edit `loading-overlay.blade.php`:

```css
/* Primary color (spinner rings) */
border-t-purple-600 → border-t-blue-600

/* Background color */
bg-purple-100 → bg-blue-100

/* Dots color */
bg-purple-600 → bg-blue-600
```

## 📋 Testing Checklist

- [ ] Loading appears when clicking "Next" from Step 2
- [ ] Loading shows correct message for fee calculation
- [ ] Loading disappears after Step 3 loads
- [ ] Cancel booking shows loading overlay
- [ ] Cancel button shows inline spinner
- [ ] Payment submission shows loading
- [ ] Payment button shows inline spinner
- [ ] Loading hides on page navigation
- [ ] Loading centers on all screen sizes
- [ ] Loading appears above all modals
- [ ] Multiple loadings don't stack
- [ ] Loading animations are smooth
- [ ] No console errors

---

**Last Updated:** December 2025
**Version:** 1.0
**Author:** Claude AI Assistant
