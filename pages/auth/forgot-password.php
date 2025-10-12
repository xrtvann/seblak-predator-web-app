<?php
/**
 * Forgot Password Page
 * Secure password reset with OTP verification
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/session.php';

// Generate CSRF token for security
$csrf_token = generateCSRFToken();
?>
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
                <div class="card mt-2">
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
                                <!-- CSRF Token for security -->
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />

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
                                    <small class="form-text text-muted d-block mt-1">Minimal 8 karakter</small>
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

    <!-- Theme Configuration -->
    <script>
        layout_change('light');
        font_change('Roboto');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change('preset-1');
    </script>

    <!-- Authentication Module -->
    <script src="../../src/assets/js/auth.js"></script>
</body>

</html>