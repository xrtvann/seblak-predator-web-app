/**
 * SweetAlert Utility Functions
 * Seblak Predator - Restaurant Management System
 * 
 * This file contains reusable SweetAlert functions for consistent notifications
 * across the application. Make sure SweetAlert2 is loaded before using these functions.
 * 
 * CDN: https://cdn.jsdelivr.net/npm/sweetalert2@11
 */

// Check if SweetAlert2 is loaded
if (typeof Swal === 'undefined') {
    console.error('SweetAlert2 is not loaded. Please include SweetAlert2 library before using these functions.');
}

/**
 * Show success alert
 * @param {string} title - Alert title
 * @param {string} message - Alert message
 * @param {function} callback - Optional callback function
 */
function showSuccess(title = 'Berhasil!', message = 'Operasi berhasil diselesaikan.', callback = null) {
    Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#28a745',
        timer: 3000,
        timerProgressBar: true,
        allowOutsideClick: false,
        customClass: {
            popup: 'animated fadeIn'
        }
    }).then((result) => {
        if (callback && typeof callback === 'function') {
            callback(result);
        }
    });
}

/**
 * Show error alert
 * @param {string} title - Alert title
 * @param {string} message - Alert message
 * @param {function} callback - Optional callback function
 */
function showError(title = 'Error!', message = 'Terjadi kesalahan. Silakan coba lagi.', callback = null) {
    Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#dc3545',
        allowOutsideClick: false,
        customClass: {
            popup: 'animated fadeIn'
        }
    }).then((result) => {
        if (callback && typeof callback === 'function') {
            callback(result);
        }
    });
}

/**
 * Show warning alert
 * @param {string} title - Alert title
 * @param {string} message - Alert message
 * @param {function} callback - Optional callback function
 */
function showWarning(title = 'Peringatan!', message = 'Silakan periksa input Anda dan coba lagi.', callback = null) {
    Swal.fire({
        icon: 'warning',
        title: title,
        text: message,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#ffc107',
        timer: 4000,
        timerProgressBar: true,
        allowOutsideClick: false,
        customClass: {
            popup: 'animated fadeIn'
        }
    }).then((result) => {
        if (callback && typeof callback === 'function') {
            callback(result);
        }
    });
}

/**
 * Show info alert
 * @param {string} title - Alert title
 * @param {string} message - Alert message
 * @param {function} callback - Optional callback function
 */
function showInfo(title = 'Informasi', message = 'Berikut adalah informasi penting.', callback = null) {
    Swal.fire({
        icon: 'info',
        title: title,
        text: message,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#17a2b8',
        timer: 4000,
        timerProgressBar: true,
        allowOutsideClick: false,
        customClass: {
            popup: 'animated fadeIn'
        }
    }).then((result) => {
        if (callback && typeof callback === 'function') {
            callback(result);
        }
    });
}

/**
 * Show confirmation alert
 * @param {string} title - Alert title
 * @param {string} message - Alert message
 * @param {function} onConfirm - Function to execute on confirm
 * @param {function} onCancel - Optional function to execute on cancel
 * @param {string} confirmText - Custom confirm button text
 * @param {string} cancelText - Custom cancel button text
 */
function showConfirmation(
    title = 'Apakah Anda yakin?', 
    message = 'Tindakan ini tidak dapat dibatalkan!', 
    onConfirm = null, 
    onCancel = null,
    confirmText = 'Ya, Lanjutkan!',
    cancelText = 'Batal'
) {
    Swal.fire({
        icon: 'question',
        title: title,
        text: message,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        allowOutsideClick: false,
        focusCancel: true,
        customClass: {
            popup: 'animated fadeIn'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            if (onConfirm && typeof onConfirm === 'function') {
                onConfirm(result);
            }
        } else if (result.isDismissed) {
            if (onCancel && typeof onCancel === 'function') {
                onCancel(result);
            }
        }
    });
}

/**
 * Show delete confirmation alert
 * @param {string} itemName - Name of item to delete
 * @param {function} onConfirm - Function to execute on confirm
 * @param {function} onCancel - Optional function to execute on cancel
 */
function showDeleteConfirmation(itemName = 'item ini', onConfirm = null, onCancel = null) {
    showConfirmation(
        'Konfirmasi Hapus',
        `Apakah Anda yakin ingin menghapus "${itemName}"? Tindakan ini tidak dapat dibatalkan!`,
        onConfirm,
        onCancel,
        'Ya, Hapus!',
        'Batal'
    );
}

/**
 * Show loading alert
 * @param {string} title - Loading title
 * @param {string} message - Loading message
 */
function showLoading(title = 'Mohon tunggu...', message = 'Sedang memproses permintaan Anda.') {
    Swal.fire({
        title: title,
        text: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
        customClass: {
            popup: 'animated fadeIn'
        }
    });
}

/**
 * Close/hide current alert
 */
function hideAlert() {
    Swal.close();
}

/**
 * Show custom toast notification
 * @param {string} type - Toast type (success, error, warning, info)
 * @param {string} message - Toast message
 * @param {number} timer - Auto close timer in milliseconds
 */
function showToast(type = 'success', message = 'Operasi selesai!', timer = 3000) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: timer,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        },
        customClass: {
            popup: 'animated slideInRight'
        }
    });

    Toast.fire({
        icon: type,
        title: message
    });
}

/**
 * Show form validation errors
 * @param {Array} errors - Array of error messages
 * @param {string} title - Alert title
 */
function showValidationErrors(errors = [], title = 'Kesalahan Validasi') {
    let errorList = '';
    
    if (Array.isArray(errors)) {
        errorList = errors.map(error => `• ${error}`).join('<br>');
    } else if (typeof errors === 'object') {
        errorList = Object.values(errors).map(error => `• ${error}`).join('<br>');
    } else {
        errorList = errors.toString();
    }

    Swal.fire({
        icon: 'error',
        title: title,
        html: errorList,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#dc3545',
        allowOutsideClick: false,
        customClass: {
            popup: 'animated fadeIn'
        }
    });
}

/**
 * Show input prompt
 * @param {string} title - Prompt title
 * @param {string} placeholder - Input placeholder
 * @param {string} inputType - Input type (text, email, password, etc.)
 * @param {function} onConfirm - Function to execute with input value
 * @param {string} confirmText - Confirm button text
 */
function showInputPrompt(
    title = 'Masukkan nilai:', 
    placeholder = 'Ketik di sini...', 
    inputType = 'text',
    onConfirm = null,
    confirmText = 'Kirim'
) {
    Swal.fire({
        title: title,
        input: inputType,
        inputPlaceholder: placeholder,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: 'Batal',
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        allowOutsideClick: false,
        inputValidator: (value) => {
            if (!value) {
                return 'Silakan masukkan nilai!';
            }
        },
        customClass: {
            popup: 'animated fadeIn'
        }
    }).then((result) => {
        if (result.isConfirmed && onConfirm && typeof onConfirm === 'function') {
            onConfirm(result.value);
        }
    });
}

/**
 * Show custom HTML alert
 * @param {string} title - Alert title
 * @param {string} html - HTML content
 * @param {string} icon - Alert icon
 * @param {function} callback - Optional callback function
 */
function showCustomAlert(title = 'Peringatan Kustom', html = '', icon = 'info', callback = null) {
    Swal.fire({
        title: title,
        html: html,
        icon: icon,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#007bff',
        allowOutsideClick: false,
        customClass: {
            popup: 'animated fadeIn'
        }
    }).then((result) => {
        if (callback && typeof callback === 'function') {
            callback(result);
        }
    });
}

/**
 * Show progress alert (for file uploads, etc.)
 * @param {string} title - Alert title
 * @param {number} progress - Progress percentage (0-100)
 */
function showProgress(title = 'Mengunggah...', progress = 0) {
    Swal.fire({
        title: title,
        html: `
            <div class="progress mb-3">
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" 
                     style="width: ${progress}%" 
                     aria-valuenow="${progress}" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    ${progress}%
                </div>
            </div>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        customClass: {
            popup: 'animated fadeIn'
        }
    });
}

/**
 * Update progress bar
 * @param {number} progress - Progress percentage (0-100)
 */
function updateProgress(progress = 0) {
    const progressBar = document.querySelector('.swal2-popup .progress-bar');
    if (progressBar) {
        progressBar.style.width = `${progress}%`;
        progressBar.setAttribute('aria-valuenow', progress);
        progressBar.textContent = `${progress}%`;
    }
}

// Export functions for module usage (if using modules)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        showSuccess,
        showError,
        showWarning,
        showInfo,
        showConfirmation,
        showDeleteConfirmation,
        showLoading,
        hideAlert,
        showToast,
        showValidationErrors,
        showInputPrompt,
        showCustomAlert,
        showProgress,
        updateProgress
    };
}