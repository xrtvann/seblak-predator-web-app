<!doctype html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>Reset Password | Seblak Predator</title>
    <!-- [Meta] -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Reset password untuk Seblak Predator - Restaurant Management System" />
    <meta name="keywords" content="forgot password, reset password, seblak predator, restaurant management" />
    <meta name="author" content="Seblak Predator" />

    <!-- [Favicon] icon -->
    <link rel="icon" href="../../dist/assets/images/favicon.svg" type="image/x-icon" />
    <!-- [Google Font] Family -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
        id="main-font-link" />
    <!-- [phosphor Icons] https://phosphoricons.com/ -->
    <link rel="stylesheet" href="../../dist/assets/fonts/phosphor/duotone/style.css" />
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="../../dist/assets/fonts/tabler-icons.min.css" />
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="../../dist/assets/fonts/feather.css" />
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="../../dist/assets/fonts/fontawesome.css" />
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="../../dist/assets/fonts/material.css" />
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="../../dist/assets/css/style.css" id="main-style-link" />
    <link rel="stylesheet" href="../../dist/assets/css/style-preset.css" />

    <style>
        /* Reset Password Custom Styles */
        .step-progress {
            margin-bottom: 2rem;
        }

        .step-item {
            position: relative;
            flex: 1;
            text-align: center;
        }

        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }

        .step-item.active:not(:last-child)::after,
        .step-item.completed:not(:last-child)::after {
            background: #dc2626;
        }

        .step-icon {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            z-index: 2;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .step-item.active .step-icon {
            background: #dc2626;
            color: white;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.25);
        }

        .step-item.completed .step-icon {
            background: #16a34a;
            color: white;
        }

        .step-title {
            font-size: 12px;
            font-weight: 500;
            color: #6c757d;
            margin-top: 5px;
        }

        .step-item.active .step-title {
            color: #dc2626;
            font-weight: 600;
        }

        .step-item.completed .step-title {
            color: #16a34a;
        }

        .form-container {
            transition: all 0.5s ease;
        }

        .otp-input {
            font-size: 24px;
            text-align: center;
            letter-spacing: 10px;
            padding: 15px;
        }

        .strength-meter {
            height: 8px;
            border-radius: 4px;
            margin-top: 8px;
        }

        .btn-loading {
            position: relative;
        }

        .btn-loading:disabled {
            pointer-events: none;
        }

        .btn-loading .spinner {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .fade-transition {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }

        .fade-transition.show {
            opacity: 1;
            transform: translateY(0);
        }

        .success-animation {
            animation: successPulse 0.6s ease-in-out;
        }

        @keyframes successPulse {
            0% {
                transform: scale(0.95);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Match login.php button style */
        .btn-primary {
            background-color: #dc2626;
            border-color: #dc2626;
        }

        .btn-primary:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
        }

        .text-red-500 {
            color: #dc2626 !important;
        }

        /* Auto dismiss animation */
        .alert {
            transition: opacity 0.5s ease-in-out;
        }
    </style>
</head>
<!-- [Head] end -->

<body>
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <div class="auth-main">
        <div class="auth-wrapper v3">
            <div class="auth-form">
                <div class="card my-5">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="text-center mb-4">
                            <a href="#" class="d-flex justify-content-center">
                                <img src="../../dist/assets/images/logo-150.png" alt="Seblak Predator"
                                    class="img-fluid mb-3" />
                            </a>
                            <div class="auth-header">
                                <h2 class="text-red-500 mb-1"><b>Reset Password</b></h2>
                                <p class="f-16 text-muted">Ikuti langkah berikut untuk mereset password Anda</p>
                            </div>
                        </div>

                        <!-- Progress Steps -->
                        <div class="step-progress mb-4">
                            <div class="d-flex justify-content-between">
                                <div class="step-item active" id="step1">
                                    <div class="step-icon">
                                        <i class="ti ti-mail"></i>
                                    </div>
                                    <div class="step-title">Email</div>
                                </div>
                                <div class="step-item" id="step2">
                                    <div class="step-icon">
                                        <i class="ti ti-shield-check"></i>
                                    </div>
                                    <div class="step-title">Verifikasi</div>
                                </div>
                                <div class="step-item" id="step3">
                                    <div class="step-icon">
                                        <i class="ti ti-lock"></i>
                                    </div>
                                    <div class="step-title">Password Baru</div>
                                </div>
                                <div class="step-item" id="step4">
                                    <div class="step-icon">
                                        <i class="ti ti-check"></i>
                                    </div>
                                    <div class="step-title">Selesai</div>
                                </div>
                            </div>
                        </div>

                        <!-- Alert Messages -->
                        <div id="alertContainer"></div>

                        <!-- Step 1: Email Input -->
                        <div id="emailStep" class="form-container fade-transition show">
                            <form id="emailForm">
                                <div class="text-center mb-4">
                                    <div class="mb-3">
                                        <div class="bg-light-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width: 80px; height: 80px;">
                                            <i class="ti ti-mail text-primary" style="font-size: 32px;"></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-2">Masukkan Email Anda</h5>
                                    <p class="text-muted">Kami akan mengirim kode OTP ke email yang terdaftar</p>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Alamat Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ti ti-mail"></i>
                                        </span>
                                        <input type="email" id="emailInput" class="form-control"
                                            placeholder="contoh@email.com" required />
                                    </div>
                                    <small class="form-text text-muted">Pastikan email yang dimasukkan sudah
                                        terdaftar</small>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-danger btn-loading" id="sendOtpBtn">
                                        <span class="btn-text">
                                            <i class="ti ti-send me-2"></i>
                                            Kirim Kode OTP
                                        </span>
                                        <div class="spinner d-none">
                                            <div class="spinner-border spinner-border-sm text-white" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Step 2: OTP Verification -->
                        <div id="otpStep" class="form-container fade-transition d-none">
                            <form id="otpForm">
                                <div class="text-center mb-4">
                                    <div class="mb-3">
                                        <div class="bg-light-success rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width: 80px; height: 80px;">
                                            <i class="ti ti-shield-check text-success" style="font-size: 32px;"></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-2">Verifikasi Kode OTP</h5>
                                    <p class="text-muted">
                                        Masukkan kode 6 digit yang telah dikirim ke:<br>
                                        <strong id="emailDisplay"></strong>
                                    </p>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Kode OTP</label>
                                    <input type="text" id="otpInput" class="form-control otp-input" placeholder="000000"
                                        maxlength="6" pattern="[0-9]{6}" required />
                                    <small class="form-text text-muted">
                                        <i class="ti ti-clock me-1"></i>
                                        Kode akan expired dalam <span id="countdown">15:00</span>
                                    </small>
                                </div>

                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-success btn-loading" id="verifyOtpBtn">
                                        <span class="btn-text">
                                            <i class="ti ti-check me-2"></i>
                                            Verifikasi Kode
                                        </span>
                                        <div class="spinner d-none">
                                            <div class="spinner-border spinner-border-sm text-white" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </button>
                                </div>

                                <div class="text-center">
                                    <p class="mb-2">Tidak menerima kode?</p>
                                    <button type="button" class="btn btn-link p-0" id="resendOtpBtn">
                                        <i class="ti ti-refresh me-1"></i>
                                        Kirim Ulang (<span id="resendCountdown">60</span>s)
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Step 3: New Password -->
                        <div id="passwordStep" class="form-container fade-transition d-none">
                            <form id="passwordForm">
                                <div class="text-center mb-4">
                                    <div class="mb-3">
                                        <div class="bg-light-warning rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width: 80px; height: 80px;">
                                            <i class="ti ti-lock text-warning" style="font-size: 32px;"></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-2">Buat Password Baru</h5>
                                    <p class="text-muted">Masukkan password baru yang aman untuk akun Anda</p>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" id="newPassword" class="form-control"
                                            placeholder="Masukkan password baru" minlength="8" required />
                                        <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                            <i class="ti ti-eye" id="toggleNewIcon"></i>
                                        </button>
                                    </div>
                                    <div class="strength-meter mt-2">
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar" id="strengthBar" role="progressbar"
                                                style="width: 0%"></div>
                                        </div>
                                        <small id="strengthText" class="form-text text-muted">Masukkan password</small>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <div class="input-group">
                                        <input type="password" id="confirmPassword" class="form-control"
                                            placeholder="Ulangi password baru" minlength="8" required />
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="toggleConfirmPassword">
                                            <i class="ti ti-eye" id="toggleConfirmIcon"></i>
                                        </button>
                                    </div>
                                    <small id="matchText" class="form-text"></small>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-warning btn-loading" id="resetPasswordBtn"
                                        disabled>
                                        <span class="btn-text">
                                            <i class="ti ti-device-floppy me-2"></i>
                                            Reset Password
                                        </span>
                                        <div class="spinner d-none">
                                            <div class="spinner-border spinner-border-sm text-white" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Step 4: Success -->
                        <div id="successStep" class="form-container fade-transition d-none">
                            <div class="text-center success-animation">
                                <div class="mb-4">
                                    <div class="bg-light-success rounded-circle d-inline-flex align-items-center justify-content-center"
                                        style="width: 100px; height: 100px;">
                                        <i class="ti ti-check text-success" style="font-size: 48px;"></i>
                                    </div>
                                </div>
                                <h4 class="text-success mb-3">Password Berhasil Direset!</h4>
                                <p class="text-muted mb-4">
                                    Password Anda telah berhasil diperbarui.<br>
                                    Sekarang Anda dapat login menggunakan password baru.
                                </p>
                                <div class="d-grid">
                                    <a href="login.php" class="btn btn-success">
                                        <i class="ti ti-login me-2"></i>
                                        Login Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Back to Login -->
                        <div class="text-center mt-4" id="backToLogin">
                            <p class="mb-0">
                                <a href="login.php" class="link-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>
                                    Kembali ke Login
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Js -->
    <script src="../../dist/assets/js/plugins/popper.min.js"></script>
    <script src="../../dist/assets/js/plugins/simplebar.min.js"></script>
    <script src="../../dist/assets/js/plugins/bootstrap.min.js"></script>
    <script src="../../dist/assets/js/icon/custom-font.js"></script>
    <script src="../../dist/assets/js/script.js"></script>
    <script src="../../dist/assets/js/theme.js"></script>
    <script src="../../dist/assets/js/plugins/feather.min.js"></script>

    <script>
        layout_change('light');
    </script>

    <script>
        font_change('Roboto');
    </script>

    <script>
        change_box_container('false');
    </script>

    <script>
        layout_caption_change('true');
    </script>

    <script>
        layout_rtl_change('false');
    </script>

    <script>
        preset_change('preset-1');
    </script>

    <script>
        // Debug log untuk memastikan script dimuat
        console.log('Forgot Password Script Loading...');

        // Pastikan DOM sudah loaded sebelum inisialisasi
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM loaded, initializing ForgotPasswordManager...');

            // Cek apakah semua element yang dibutuhkan ada
            const requiredElements = [
                'emailForm', 'otpForm', 'passwordForm',
                'emailStep', 'otpStep', 'passwordStep', 'successStep',
                'step1', 'step2', 'step3', 'step4'
            ];

            let allElementsExist = true;
            requiredElements.forEach(id => {
                if (!document.getElementById(id)) {
                    console.error(`Element with ID '${id}' not found!`);
                    allElementsExist = false;
                }
            });

            if (!allElementsExist) {
                console.error('Some required elements are missing!');
                return;
            }

            try {
                new ForgotPasswordManager();
                console.log('ForgotPasswordManager initialized successfully!');
            } catch (error) {
                console.error('Error initializing ForgotPasswordManager:', error);
            }
        });

        class ForgotPasswordManager {
            constructor() {
                console.log('ForgotPasswordManager constructor called');
                this.currentStep = 1;
                this.userEmail = '';
                this.otpCountdown = null;
                this.resendCountdown = null;
                this.init();
            }

            init() {
                console.log('Initializing ForgotPasswordManager...');
                this.setupEventListeners();
                this.showStep(1);
                console.log('ForgotPasswordManager initialization complete');
            }

            setupEventListeners() {
                console.log('Setting up event listeners...');

                try {
                    // Email Form
                    const emailForm = document.getElementById('emailForm');
                    if (emailForm) {
                        emailForm.addEventListener('submit', (e) => {
                            e.preventDefault();
                            this.handleEmailSubmit();
                        });
                        console.log('Email form listener added');
                    }

                    // OTP Form
                    const otpForm = document.getElementById('otpForm');
                    if (otpForm) {
                        otpForm.addEventListener('submit', (e) => {
                            e.preventDefault();
                            this.handleOtpSubmit();
                        });
                        console.log('OTP form listener added');
                    }

                    // Password Form
                    const passwordForm = document.getElementById('passwordForm');
                    if (passwordForm) {
                        passwordForm.addEventListener('submit', (e) => {
                            e.preventDefault();
                            this.handlePasswordSubmit();
                        });
                        console.log('Password form listener added');
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
                        console.log('OTP input listener added');
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
                        console.log('Resend OTP listener added');
                    }

                    console.log('All event listeners set up successfully');
                } catch (error) {
                    console.error('Error setting up event listeners:', error);
                }
            }

            setupPasswordToggles() {
                console.log('Setting up password toggles...');
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
                    console.log('Password toggles set up successfully');
                } catch (error) {
                    console.error('Error setting up password toggles:', error);
                }
            }

            setupPasswordValidation() {
                console.log('Setting up password validation...');
                try {
                    const newPassword = document.getElementById('newPassword');
                    const confirmPassword = document.getElementById('confirmPassword');
                    const strengthBar = document.getElementById('strengthBar');
                    const strengthText = document.getElementById('strengthText');
                    const matchText = document.getElementById('matchText');
                    const submitBtn = document.getElementById('resetPasswordBtn');

                    if (!newPassword || !confirmPassword || !strengthBar || !strengthText || !matchText || !submitBtn) {
                        console.error('Some password validation elements not found');
                        return;
                    }

                    const checkPasswordStrength = (password) => {
                        let strength = 0;
                        let strengthLabel = '';
                        let strengthClass = '';

                        if (password.length >= 8) strength++;
                        if (/[a-z]/.test(password)) strength++;
                        if (/[A-Z]/.test(password)) strength++;
                        if (/[0-9]/.test(password)) strength++;
                        if (/[^A-Za-z0-9]/.test(password)) strength++;

                        switch (strength) {
                            case 0:
                            case 1:
                                strengthLabel = 'Sangat Lemah';
                                strengthClass = 'bg-danger';
                                break;
                            case 2:
                                strengthLabel = 'Lemah';
                                strengthClass = 'bg-warning';
                                break;
                            case 3:
                                strengthLabel = 'Sedang';
                                strengthClass = 'bg-info';
                                break;
                            case 4:
                                strengthLabel = 'Kuat';
                                strengthClass = 'bg-primary';
                                break;
                            case 5:
                                strengthLabel = 'Sangat Kuat';
                                strengthClass = 'bg-success';
                                break;
                        }

                        strengthBar.style.width = (strength * 20) + '%';
                        strengthBar.className = 'progress-bar ' + strengthClass;
                        strengthText.textContent = strengthLabel;
                        strengthText.className = 'form-text ' + strengthClass.replace('bg-', 'text-');

                        return strength >= 3;
                    };

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
                            const isStrong = checkPasswordStrength(newPassword.value);
                            const isMatch = checkPasswordMatch();
                            submitBtn.disabled = !(isStrong && isMatch && newPassword.value.length >= 8);
                        } catch (error) {
                            console.error('Error in form validation:', error);
                        }
                    };

                    newPassword.addEventListener('input', validateForm);
                    confirmPassword.addEventListener('input', validateForm);

                    console.log('Password validation set up successfully');
                } catch (error) {
                    console.error('Error setting up password validation:', error);
                }
            }

            showStep(step) {
                console.log(`Showing step ${step}`);
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
                    console.log(`Step ${step} shown successfully`);
                } catch (error) {
                    console.error('Error showing step:', error);
                }
            }

            updateProgress(activeStep) {
                console.log(`Updating progress to step ${activeStep}`);
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
                    console.log('Progress updated successfully');
                } catch (error) {
                    console.error('Error updating progress:', error);
                }
            }

            showAlert(type, message) {
                console.log(`Showing ${type} alert: ${message}`);
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
                        console.log('Alert shown successfully');
                    } else {
                        console.error('Alert container not found');
                    }
                } catch (error) {
                    console.error('Error showing alert:', error);
                }
            }

            setButtonLoading(buttonId, loading) {
                console.log(`Setting button ${buttonId} loading: ${loading}`);
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
                            console.log(`Button ${buttonId} loading state updated`);
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

        // Auto dismiss alerts after 5 seconds (like login.php)
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
    </script>
</body>

</html>