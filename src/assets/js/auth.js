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
            // Simulate API call
            await this.delay(2000);

            // For demo purposes, assume success
            document.getElementById('emailDisplay').textContent = email;
            this.showStep(2);
            this.startOtpCountdown();
            this.startResendCountdown();
            this.showAlert('success', 'Kode OTP telah dikirim ke email Anda');
        } catch (error) {
            this.showAlert('error', 'Gagal mengirim kode OTP. Silakan coba lagi.');
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
            // Simulate API call
            await this.delay(1500);

            // For demo purposes, assume success
            this.showStep(3);
            this.showAlert('success', 'Kode OTP berhasil diverifikasi');
        } catch (error) {
            this.showAlert('error', 'Kode OTP tidak valid atau sudah expired');
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

        this.setButtonLoading('resetPasswordBtn', true);

        try {
            // Simulate API call
            await this.delay(2000);

            // For demo purposes, assume success
            this.showStep(4);
        } catch (error) {
            this.showAlert('error', 'Gagal mereset password. Silakan coba lagi.');
        } finally {
            this.setButtonLoading('resetPasswordBtn', false);
        }
    }

    async resendOtp() {
        const resendBtn = document.getElementById('resendOtpBtn');
        resendBtn.disabled = true;

        try {
            // Simulate API call
            await this.delay(1000);

            this.showAlert('success', 'Kode OTP baru telah dikirim');
            this.startOtpCountdown();
            this.startResendCountdown();
        } catch (error) {
            this.showAlert('error', 'Gagal mengirim ulang kode OTP');
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
