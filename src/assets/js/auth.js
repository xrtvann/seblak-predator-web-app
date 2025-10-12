/**
 * Authentication JavaScript Module
 * Handles authentication related functionality (register, login, forgot password)
 * 
 * @author Seblak Predator
 * @version 1.0.0
 */

// Initialize appropriate handler based on current page
document.addEventListener('DOMContentLoaded', function () {
    // Check if we're on forgot password page
    const forgotPasswordElements = [
        'emailForm', 'otpForm', 'passwordForm',
        'emailStep', 'otpStep', 'passwordStep', 'successStep',
        'step1', 'step2', 'step3', 'step4'
    ];

    let isForgotPasswordPage = forgotPasswordElements.every(id => document.getElementById(id));

    if (isForgotPasswordPage) {
        try {
            new ForgotPasswordManager();
        } catch (error) {
            console.error('Error initializing ForgotPasswordManager:', error);
        }
    }

    // Check if we're on register page
    const registerPasswordField = document.getElementById('floatingInput4');
    const togglePassword = document.getElementById('togglePassword');
    
    if (registerPasswordField && togglePassword) {
        try {
            new RegisterPasswordManager();
        } catch (error) {
            console.error('Error initializing RegisterPasswordManager:', error);
        }
    }
});

/**
 * Forgot Password Manager Class
 * Manages the entire forgot password flow
 */
class ForgotPasswordManager {
    constructor() {
        this.currentStep = 1;
        this.userEmail = '';
        this.otpCountdown = null;
        this.resendCountdown = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.showStep(1);
    }

    setupEventListeners() {
        try {
            // Email Form
            const emailForm = document.getElementById('emailForm');
            if (emailForm) {
                emailForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleEmailSubmit();
                });
            }

            // OTP Form
            const otpForm = document.getElementById('otpForm');
            if (otpForm) {
                otpForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleOtpSubmit();
                });
            }

            // Password Form
            const passwordForm = document.getElementById('passwordForm');
            if (passwordForm) {
                passwordForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handlePasswordSubmit();
                });
            }

            // OTP Input - Auto submit on 6 digits
            const otpInput = document.getElementById('otpInput');
            if (otpInput) {
                otpInput.addEventListener('input', (e) => {
                    e.target.value = e.target.value.replace(/[^0-9]/g, '');
                    if (e.target.value.length === 6) {
                        setTimeout(() => {
                            document.getElementById('otpForm').dispatchEvent(new Event('submit'));
                        }, 300);
                    }
                });
            }

            // Password visibility toggles
            this.setupPasswordToggles();

            // Password strength checker
            this.setupPasswordValidation();

            // Resend OTP
            const resendBtn = document.getElementById('resendOtpBtn');
            if (resendBtn) {
                resendBtn.addEventListener('click', () => {
                    this.resendOtp();
                });
            }
        } catch (error) {
            console.error('Error setting up event listeners:', error);
        }
    }

    setupPasswordToggles() {
        try {
            const toggles = [
                { toggle: 'toggleNewPassword', input: 'newPassword', icon: 'toggleNewIcon' },
                { toggle: 'toggleConfirmPassword', input: 'confirmPassword', icon: 'toggleConfirmIcon' }
            ];

            toggles.forEach(({ toggle, input, icon }) => {
                const toggleBtn = document.getElementById(toggle);
                if (toggleBtn) {
                    toggleBtn.addEventListener('click', () => {
                        const inputField = document.getElementById(input);
                        const iconElement = document.getElementById(icon);
                        if (inputField && iconElement) {
                            const type = inputField.getAttribute('type') === 'password' ? 'text' : 'password';
                            inputField.setAttribute('type', type);
                            iconElement.className = type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
                        }
                    });
                }
            });
        } catch (error) {
            console.error('Error setting up password toggles:', error);
        }
    }

    setupPasswordValidation() {
        try {
            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');
            const matchText = document.getElementById('matchText');
            const submitBtn = document.getElementById('resetPasswordBtn');

            if (!newPassword || !confirmPassword || !matchText || !submitBtn) {
                console.error('Some password validation elements not found');
                return;
            }

            const checkPasswordMatch = () => {
                const password = newPassword.value;
                const confirm = confirmPassword.value;

                if (confirm === '') {
                    matchText.textContent = '';
                    matchText.className = 'form-text';
                    return false;
                } else if (password === confirm) {
                    matchText.textContent = '✓ Password cocok';
                    matchText.className = 'form-text text-success';
                    return true;
                } else {
                    matchText.textContent = '✗ Password tidak cocok';
                    matchText.className = 'form-text text-danger';
                    return false;
                }
            };

            const validateForm = () => {
                try {
                    const isMatch = checkPasswordMatch();
                    const isValid = newPassword.value.length >= 8;
                    submitBtn.disabled = !(isMatch && isValid);
                } catch (error) {
                    console.error('Error in form validation:', error);
                }
            };

            newPassword.addEventListener('input', validateForm);
            confirmPassword.addEventListener('input', validateForm);
        } catch (error) {
            console.error('Error setting up password validation:', error);
        }
    }

    showStep(step) {
        try {
            // Hide all steps
            const steps = ['emailStep', 'otpStep', 'passwordStep', 'successStep'];
            steps.forEach(stepId => {
                const element = document.getElementById(stepId);
                if (element) {
                    element.classList.remove('show');
                    element.classList.add('d-none');
                } else {
                    console.warn(`Element ${stepId} not found`);
                }
            });

            // Show current step
            const currentStepElement = document.getElementById(steps[step - 1]);
            if (currentStepElement) {
                currentStepElement.classList.remove('d-none');
                setTimeout(() => {
                    currentStepElement.classList.add('show');
                }, 50);
            } else {
                console.error(`Current step element ${steps[step - 1]} not found`);
            }

            // Update progress
            this.updateProgress(step);

            // Hide/show back to login link
            const backToLogin = document.getElementById('backToLogin');
            if (backToLogin) {
                if (step === 4) {
                    backToLogin.classList.add('d-none');
                } else {
                    backToLogin.classList.remove('d-none');
                }
            }

            this.currentStep = step;
        } catch (error) {
            console.error('Error showing step:', error);
        }
    }

    updateProgress(activeStep) {
        try {
            for (let i = 1; i <= 4; i++) {
                const stepElement = document.getElementById(`step${i}`);
                if (stepElement) {
                    stepElement.classList.remove('active', 'completed');

                    if (i < activeStep) {
                        stepElement.classList.add('completed');
                        const icon = stepElement.querySelector('.step-icon i');
                        if (icon) {
                            icon.className = 'ti ti-check';
                        }
                    } else if (i === activeStep) {
                        stepElement.classList.add('active');
                    }
                } else {
                    console.warn(`Step element step${i} not found`);
                }
            }
        } catch (error) {
            console.error('Error updating progress:', error);
        }
    }

    showAlert(type, message, duration = 5000) {
        try {
            const alertContainer = document.getElementById('alertContainer');
            if (alertContainer) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const icon = type === 'success' ? 'ti-check-circle' : 'ti-alert-circle';

                const alertElement = document.createElement('div');
                alertElement.className = `alert ${alertClass} alert-dismissible fade show`;
                alertElement.setAttribute('role', 'alert');
                alertElement.innerHTML = `
                    <i class="ti ${icon} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                // Clear previous alerts
                alertContainer.innerHTML = '';
                alertContainer.appendChild(alertElement);

                // Auto-dismiss after duration (if not error type with registration link)
                if (duration > 0 && !message.includes('Daftar Akun Baru')) {
                    setTimeout(() => {
                        if (alertElement && alertElement.parentNode) {
                            alertElement.classList.remove('show');
                            setTimeout(() => {
                                if (alertElement && alertElement.parentNode) {
                                    alertElement.remove();
                                }
                            }, 150);
                        }
                    }, duration);
                }

                // Scroll to alert if needed
                alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                console.error('Alert container not found');
            }
        } catch (error) {
            console.error('Error showing alert:', error);
        }
    }

    setButtonLoading(buttonId, loading) {
        try {
            const button = document.getElementById(buttonId);
            if (button) {
                const btnText = button.querySelector('.btn-text');
                const spinner = button.querySelector('.spinner');

                if (btnText && spinner) {
                    if (loading) {
                        button.disabled = true;
                        btnText.classList.add('d-none');
                        spinner.classList.remove('d-none');
                    } else {
                        button.disabled = false;
                        btnText.classList.remove('d-none');
                        spinner.classList.add('d-none');
                    }
                } else {
                    console.warn(`Button text or spinner not found for ${buttonId}`);
                }
            } else {
                console.error(`Button ${buttonId} not found`);
            }
        } catch (error) {
            console.error('Error setting button loading:', error);
        }
    }

    async handleEmailSubmit() {
        const email = document.getElementById('emailInput').value;
        this.userEmail = email;

        // Validate email
        if (!this.isValidEmail(email)) {
            this.showAlert('error', 'Format email tidak valid');
            return;
        }

        this.setButtonLoading('sendOtpBtn', true);

        try {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.querySelector('input[name="csrf_token"]')?.value || '';

            // Send request to backend handler
            const formData = new FormData();
            formData.append('action', 'send_otp');
            formData.append('email', email);
            
            // Use our working test handler temporarily until CSRF issues are resolved
            const response = await fetch('../../test_email_handler.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            // Debug logging
            console.log('🔍 Debug: Email verification response:', result);
            console.log('🔍 Debug: Response success:', result.success);
            console.log('🔍 Debug: Response message:', result.message);

            if (result.success) {
                // Email verified and OTP sent successfully - proceed to step 2
                console.log('✅ Debug: Proceeding to step 2');
                document.getElementById('emailDisplay').textContent = email;
                this.showStep(2);
                this.startOtpCountdown();
                this.startResendCountdown();
                
                // Show success message
                let message = result.message || 'Kode OTP telah dikirim ke email Anda';
                
                // In development mode, show OTP directly
                if (result.development_otp) {
                    message += `<br><div class="mt-2 p-2 bg-warning bg-opacity-10 border border-warning rounded">
                        <strong>🔧 MODE DEVELOPMENT:</strong><br>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>📧 Email dikirim ke inbox Anda!</strong><br>
                                <small>Cek Gmail untuk email dengan design indah</small>
                            </div>
                            <div class="col-md-6">
                                <strong>OTP Code:</strong> <span class="fw-bold text-primary fs-5">${result.development_otp}</span><br>
                                <small class="text-muted">OTP akan otomatis terisi dalam 2 detik...</small>
                            </div>
                        </div>
                    </div>`;
                    
                    // Auto-fill OTP after 2 seconds in development
                    setTimeout(() => {
                        const otpInput = document.getElementById('otpInput');
                        if (otpInput) {
                            otpInput.value = result.development_otp;
                            otpInput.dispatchEvent(new Event('input'));
                        }
                    }, 2000);
                } else {
                    // Production mode - show email check notice
                    message += `<br><div class="mt-2 p-2 bg-info bg-opacity-10 border border-info rounded">
                        <strong>📧 MODE PRODUCTION:</strong><br>
                        Silakan cek email inbox Anda (termasuk folder spam) untuk kode OTP
                        <br><small class="text-muted">Email dikirim dari: seblakpredator@gmail.com</small>
                    </div>`;
                }
                
                this.showAlert('success', message, 0); // Don't auto-dismiss
            } else {
                // Email verification failed - stay on step 1
                console.log('❌ Debug: Staying on step 1 - email verification failed');
                this.userEmail = ''; // Clear stored email since it's invalid
                
                // Add visual feedback to email input
                const emailInput = document.getElementById('emailInput');
                emailInput.classList.add('is-invalid');
                
                // Remove invalid class after user starts typing again
                const removeInvalidClass = () => {
                    emailInput.classList.remove('is-invalid');
                    emailInput.removeEventListener('input', removeInvalidClass);
                };
                emailInput.addEventListener('input', removeInvalidClass);
                
                // Handle specific error cases and stay on current step
                if (result.message.includes('tidak terdaftar')) {
                    this.showAlert('error', result.message + ' <br><a href="register.php" class="alert-link text-decoration-underline">📝 Daftar Akun Baru</a>', 0); // Don't auto-dismiss
                } else if (result.retry_after) {
                    const minutes = Math.ceil(result.retry_after / 60);
                    this.showAlert('error', `⏰ Terlalu banyak percobaan. Silakan coba lagi dalam ${minutes} menit.`, 10000);
                } else {
                    this.showAlert('error', result.message || 'Gagal mengirim kode OTP. Silakan coba lagi.');
                }
                
                // Focus back to email input for correction
                setTimeout(() => {
                    emailInput.focus();
                    emailInput.select(); // Select the text for easy replacement
                }, 100);
            }
        } catch (error) {
            console.error('Error sending OTP:', error);
            this.showAlert('error', 'Terjadi kesalahan jaringan. Silakan periksa koneksi internet Anda.');
        } finally {
            this.setButtonLoading('sendOtpBtn', false);
        }
    }

    async handleOtpSubmit() {
        const otp = document.getElementById('otpInput').value;

        if (otp.length !== 6) {
            this.showAlert('error', 'Kode OTP harus 6 digit');
            return;
        }

        this.setButtonLoading('verifyOtpBtn', true);

        try {
            // Send OTP verification request
            const formData = new FormData();
            formData.append('action', 'verify_otp');
            formData.append('otp', otpCode);

            const response = await fetch('../../test_email_handler.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            console.log('🔍 Debug: OTP verification response:', result);

            if (result.success) {
                // OTP verified successfully - proceed to step 3
                console.log('✅ Debug: OTP verified, proceeding to step 3');
                this.showStep(3);
                this.showAlert('success', result.message || 'Kode OTP berhasil diverifikasi');
            } else {
                // OTP verification failed
                console.log('❌ Debug: OTP verification failed');
                this.showAlert('error', result.message || 'Kode OTP tidak valid atau sudah expired');
            }
        } catch (error) {
            console.error('Error verifying OTP:', error);
            this.showAlert('error', 'Terjadi kesalahan jaringan. Silakan coba lagi.');
        } finally {
            this.setButtonLoading('verifyOtpBtn', false);
        }
    }

    async handlePasswordSubmit() {
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (newPassword !== confirmPassword) {
            this.showAlert('error', 'Konfirmasi password tidak cocok');
            return;
        }

        if (newPassword.length < 8) {
            this.showAlert('error', 'Password harus minimal 8 karakter');
            return;
        }

        this.setButtonLoading('resetPasswordBtn', true);

        try {
            // Send password reset request
            const formData = new FormData();
            formData.append('action', 'reset_password');
            formData.append('new_password', newPassword);
            formData.append('confirm_password', confirmPassword);

            const response = await fetch('../../test_email_handler.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            console.log('🔍 Debug: Password reset response:', result);

            if (result.success) {
                // Password reset successful - proceed to step 4
                console.log('✅ Debug: Password reset successful, proceeding to step 4');
                this.showStep(4);
                this.showAlert('success', result.message || 'Password berhasil direset');
            } else {
                // Password reset failed
                console.log('❌ Debug: Password reset failed');
                this.showAlert('error', result.message || 'Gagal mereset password. Silakan coba lagi.');
            }
        } catch (error) {
            console.error('Error resetting password:', error);
            this.showAlert('error', 'Terjadi kesalahan jaringan. Silakan coba lagi.');
        } finally {
            this.setButtonLoading('resetPasswordBtn', false);
        }
    }

    async resendOtp() {
        const resendBtn = document.getElementById('resendOtpBtn');
        resendBtn.disabled = true;

        try {
            // Get CSRF token
            const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';

            // Send request to backend handler
            const formData = new FormData();
            formData.append('action', 'send_otp');
            formData.append('email', this.userEmail);
            formData.append('csrf_token', csrfToken);

            const response = await fetch('../../handler/forgot_password.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('success', 'Kode OTP baru telah dikirim ke email Anda');
                this.startOtpCountdown();
                this.startResendCountdown();
            } else {
                this.showAlert('error', result.message || 'Gagal mengirim ulang kode OTP');
                resendBtn.disabled = false;
            }
        } catch (error) {
            console.error('Error resending OTP:', error);
            this.showAlert('error', 'Terjadi kesalahan jaringan. Silakan coba lagi.');
            resendBtn.disabled = false;
        }
    }

    startOtpCountdown() {
        let timeLeft = 15 * 60; // 15 minutes
        const countdownElement = document.getElementById('countdown');

        if (this.otpCountdown) {
            clearInterval(this.otpCountdown);
        }

        this.otpCountdown = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeft <= 0) {
                clearInterval(this.otpCountdown);
                this.showAlert('error', 'Kode OTP telah expired. Silakan minta kode baru.');
            }
            timeLeft--;
        }, 1000);
    }

    startResendCountdown() {
        let timeLeft = 60; // 60 seconds
        const resendBtn = document.getElementById('resendOtpBtn');
        const countdownElement = document.getElementById('resendCountdown');

        resendBtn.disabled = true;

        if (this.resendCountdown) {
            clearInterval(this.resendCountdown);
        }

        this.resendCountdown = setInterval(() => {
            countdownElement.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(this.resendCountdown);
                resendBtn.disabled = false;
                countdownElement.textContent = '0';
            }
            timeLeft--;
        }, 1000);
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

/**
 * Register Password Manager Class
 * Handles password visibility toggle for register page
 */
class RegisterPasswordManager {
    constructor() {
        this.passwordInput = document.getElementById('floatingInput4');
        this.toggleBtn = document.getElementById('togglePassword');
        this.toggleIcon = document.getElementById('toggleIcon');
        
        if (this.passwordInput && this.toggleBtn && this.toggleIcon) {
            this.setupPasswordToggle();
        }
    }

    setupPasswordToggle() {
        this.toggleBtn.addEventListener('click', () => {
            const type = this.passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            this.passwordInput.setAttribute('type', type);
            this.toggleIcon.className = type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
        });
    }
}

/**
 * Auto dismiss alerts after 5 seconds
 * Matches the behavior from login.php
 */
document.addEventListener('DOMContentLoaded', function () {
    // Setup auto dismiss for alerts
    const setupAlertAutoDismiss = () => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function (alert) {
            setTimeout(function () {
                if (alert && alert.parentNode) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(function () {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 500);
                }
            }, 5000);
        });
    };

    // Setup auto dismiss for initial alerts
    setupAlertAutoDismiss();

    // Setup observer for dynamically added alerts
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer) {
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.type === 'childList') {
                    setupAlertAutoDismiss();
                }
            });
        });
        observer.observe(alertContainer, { childList: true });
    }
});
