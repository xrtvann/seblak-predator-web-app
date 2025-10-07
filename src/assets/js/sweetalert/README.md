# SweetAlert Implementation Guide

## Overview
This application now uses SweetAlert2 for all notifications and confirmations instead of Bootstrap toasts and modals. All functions are available globally after including the `alert.js` file.

## Quick Usage Examples

### Basic Notifications

```javascript
// Success notification
showSuccess('Success!', 'Data berhasil disimpan');

// Error notification  
showError('Error!', 'Terjadi kesalahan saat menyimpan data');

// Warning notification
showWarning('Warning!', 'Data yang Anda masukkan tidak valid');

// Info notification
showInfo('Information', 'Proses akan dimulai dalam beberapa saat');
```

### Confirmations

```javascript
// Basic confirmation
showConfirmation(
    'Apakah Anda yakin?',
    'Data yang dihapus tidak dapat dikembalikan!',
    () => {
        // Action when confirmed
        console.log('User confirmed');
    },
    () => {
        // Action when cancelled (optional)
        console.log('User cancelled');
    }
);

// Delete confirmation (specialized)
showDeleteConfirmation('Menu Seblak Pedas', () => {
    // Delete action
    deleteItem();
});
```

### Loading States

```javascript
// Show loading
showLoading('Processing...', 'Please wait while we process your request');

// Hide loading
hideAlert();
```

### Toast Notifications

```javascript
// Success toast (top-right corner)
showToast('success', 'Data saved successfully!');

// Error toast
showToast('error', 'Failed to save data');

// Custom timer (default 3000ms)
showToast('info', 'Process completed', 5000);
```

### Input Prompts

```javascript
// Text input
showInputPrompt(
    'Enter new category name:',
    'Type category name...',
    'text',
    (value) => {
        console.log('User entered:', value);
    }
);

// Email input
showInputPrompt(
    'Enter your email:',
    'email@example.com',
    'email',
    (email) => {
        // Process email
    }
);
```

### Validation Errors

```javascript
// Array of errors
const errors = [
    'Name is required',
    'Price must be greater than 0',
    'Category must be selected'
];
showValidationErrors(errors, 'Form Validation Failed');

// Object of errors
const errorObj = {
    name: 'Name is required',
    price: 'Invalid price format',
    category: 'Category not found'
};
showValidationErrors(errorObj);
```

### Progress Bars

```javascript
// Show progress
showProgress('Uploading file...', 0);

// Update progress
updateProgress(25);
updateProgress(50);
updateProgress(75);
updateProgress(100);

// Hide when complete
hideAlert();
```

### Custom HTML Content

```javascript
showCustomAlert(
    'Welcome!',
    '<strong>Welcome to Seblak Predator!</strong><br>Your session will expire in <span class="text-danger">30 minutes</span>.',
    'info'
);
```

## Migration from Old System

### Before (Bootstrap Toast)
```javascript
function showNotification(message, type = 'info') {
    const notification = document.getElementById('notification');
    // ... bootstrap toast code
}
```

### After (SweetAlert)
```javascript
function showNotification(message, type = 'info') {
    switch(type) {
        case 'success':
            showSuccess('Success!', message);
            break;
        case 'error':
            showError('Error!', message);
            break;
        case 'warning':
            showWarning('Warning!', message);
            break;
        default:
            showInfo('Information', message);
    }
}
```

### Before (Bootstrap Modal)
```html
<!-- HTML Modal -->
<div class="modal fade" id="deleteModal">...</div>

<script>
function deleteItem(id, name) {
    document.getElementById('deleteItemName').textContent = name;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
```

### After (SweetAlert)
```javascript
function deleteItem(id, name) {
    showDeleteConfirmation(name, async () => {
        showLoading('Deleting...', 'Please wait...');
        
        try {
            const response = await fetch(`api/delete/${id}`, { method: 'DELETE' });
            const result = await response.json();
            
            hideAlert();
            
            if (result.success) {
                showSuccess('Deleted!', result.message);
            } else {
                showError('Failed!', result.message);
            }
        } catch (error) {
            hideAlert();
            showError('Error!', 'Connection failed');
        }
    });
}
```

## Implementation Status

### ✅ Completed
- [x] SweetAlert2 CDN integrated in `index.php`
- [x] Custom alert functions in `src/assets/js/sweetalert/alert.js`
- [x] Menu page notifications converted to SweetAlert
- [x] Delete confirmations converted to SweetAlert
- [x] Form submission feedback converted to SweetAlert
- [x] Logout confirmation converted to SweetAlert
- [x] Removed old Bootstrap toast and modal HTML

### 🔄 To Be Implemented
- [ ] Category page notifications (if exists)
- [ ] Transaction page notifications (if exists)
- [ ] User page notifications (if exists)
- [ ] Dashboard page notifications (if exists)
- [ ] Authentication page notifications (login, register, forgot password)

## Best Practices

1. **Use appropriate alert types**: Success for successful operations, Error for failures, Warning for potential issues, Info for general information.

2. **Provide clear messages**: Use descriptive titles and messages that help users understand what happened.

3. **Use callbacks for actions**: When showing success alerts after data operations, use callbacks to reload data or redirect users.

4. **Show loading states**: For async operations, show loading alerts and hide them when complete.

5. **Consistent button text**: Use Indonesian language consistently (e.g., "Ya, Hapus", "Batal", "OK").

6. **Handle errors gracefully**: Always wrap API calls in try-catch and show appropriate error messages.

## Examples in Menu Page

The menu page (`dist/dashboard/pages/menu.php`) now demonstrates proper SweetAlert usage:

- **Form submissions**: Loading → Success/Error with callback
- **Delete operations**: Confirmation → Loading → Success/Error  
- **Data loading errors**: Error alerts with descriptive messages
- **Validation errors**: Custom error messages

## Configuration

The alert functions are configured with sensible defaults:
- Success alerts auto-close after 3 seconds
- Error alerts require manual dismissal
- Confirmations focus on cancel button by default
- All alerts are animated with fadeIn effect
- Colors match Bootstrap theme (success: green, error: red, warning: yellow, info: blue)

## Browser Support

SweetAlert2 v11 supports:
- Chrome 65+
- Firefox 53+
- Safari 10.1+
- Edge 79+
- IE not supported (use SweetAlert v1 if IE support needed)