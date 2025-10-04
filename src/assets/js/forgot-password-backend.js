/**
 * Forgot Password Integration Script
 * Connects frontend with secure backend API
 * Includes CSRF protection and error handling
 * 
 * @author Seblak Predator
 * @version 2.0.0
 */

// Get CSRF token from page
const CSRF_TOKEN = document.getElementById('csrf_token')?.value || '';
const API_ENDPOINT = '../../handler/forgot_password.php';

/**
 * Make secure API call with CSRF protection
 */
async function makeSecureAPICall(action, data = {}) {
    try {
        const formData = new FormData();
        formData.append('action', action);
        formData.append('csrf_token', CSRF_TOKEN);
        
        // Add additional data
        for (const [key, value] of Object.entries(data)) {
            formData.append(key, value);
        }
        
        const response = await fetch(API_ENDPOINT, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin' // Include cookies for session
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        return result;
        
    } catch (error) {
        console.error('API Call Error:', error);
        return {
            success: false,
            message: 'Terjadi kesalahan koneksi. Silakan coba lagi.'
        };
    }
}

/**
 * Update Forgot Password Manager to use real backend
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

            // Password match validation
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
                }
            });

            // Show current step
            const currentStepElement = document.getElementById(steps[step - 1]);
            if (currentStepElement) {
                currentStepElement.classList.remove('d-none');
                setTimeout(() => {
                    currentStepElement.classList.add('show');
                }, 50);
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
                }
            }
        } catch (error) {
            console.error('Error updating progress:', error);
        }
    }

    showAlert(type, message) {
        try {
            const alertContainer = document.getElementById('alertContainer');
            if (alertContainer) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const icon = type === 'success' ? 'ti-check-circle' : 'ti-alert-circle';

                alertContainer.innerHTML = `
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        <i class="ti ${icon} me-2"></i>${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
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
                }
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
            // Call backend API
            const result = await makeSecureAPICall('send_otp', { email });

            if (result.success) {
                document.getElementById('emailDisplay').textContent = email;
                this.showStep(2);
                this.startOtpCountdown();
                this.startResendCountdown();
                this.showAlert('success', result.message);
            } else {
                this.showAlert('error', result.message);
            }
        } catch (error) {
            this.showAlert('error', 'Terjadi kesalahan. Silakan coba lagi.');
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
            // Call backend API
            const result = await makeSecureAPICall('verify_otp', { otp });

            if (result.success) {
                this.showStep(3);
                this.showAlert('success', result.message);
            } else {
                this.showAlert('error', result.message);
            }
        } catch (error) {
            this.showAlert('error', 'Terjadi kesalahan. Silakan coba lagi.');
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
            this.showAlert('error', 'Password minimal 8 karakter');
            return;
        }

        this.setButtonLoading('resetPasswordBtn', true);

        try {
            // Call backend API
            const result = await makeSecureAPICall('reset_password', {
                new_password: newPassword,
                confirm_password: confirmPassword
            });

            if (result.success) {
                this.showStep(4);
                // Auto redirect to login after 5 seconds
                setTimeout(() => {
                    window.location.href = 'login.php?password_reset=success';
                }, 5000);
            } else {
                this.showAlert('error', result.message);
            }
        } catch (error) {
            this.showAlert('error', 'Terjadi kesalahan. Silakan coba lagi.');
        } finally {
            this.setButtonLoading('resetPasswordBtn', false);
        }
    }

    async resendOtp() {
        const resendBtn = document.getElementById('resendOtpBtn');
        resendBtn.disabled = true;

        try {
            // Call backend API
            const result = await makeSecureAPICall('resend_otp');

            if (result.success) {
                this.showAlert('success', result.message);
                this.startOtpCountdown();
                this.startResendCountdown();
            } else {
                this.showAlert('error', result.message);
                resendBtn.disabled = false;
            }
        } catch (error) {
            this.showAlert('error', 'Terjadi kesalahan. Silakan coba lagi.');
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
}

// Initialize on page load
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
});
