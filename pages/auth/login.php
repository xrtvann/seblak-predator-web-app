<?php
// Initialize secure session
require_once '../../config/session.php';
require_once '../../services/WebAuthService.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
  header('Location: ../../index.php?page=dashboard');
  exit();
}

// Initialize auth service and check rate limiting
$auth_service = new WebAuthService($koneksi);
$rate_limit_status = ['is_limited' => false, 'remaining_seconds' => 0];
$username = ''; // Initialize username variable

// Check rate limiting - priority order: URL params > POST data > GET data
if (isset($_GET['rate_limited']) && $_GET['rate_limited'] == '1') {
  // Rate limiting info from form submission redirect
  $username = $_GET['username'] ?? ''; // Get username from redirect
  $rate_limit_status = [
    'is_limited' => true,
    'remaining_seconds' => (int) ($_GET['remaining_seconds'] ?? 0),
    'remaining_time_text' => $_GET['remaining_time'] ?? '0:00',
    'lockout_until_timestamp' => (int) ($_GET['lockout_until_timestamp'] ?? 0) // ✅ FIX: Get timestamp from URL!
  ];
} elseif (isset($_POST['field_username']) || isset($_GET['check_user'])) {
  // Check rate limiting based on username
  $username = $_POST['field_username'] ?? $_GET['check_user'] ?? '';
  $rate_limit_status = $auth_service->getLoginRateLimit($username);
}

// Get flash messages
$flash_messages = getFlashMessages();
?>
<!doctype html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>Login | Seblak Predator</title>
  <!-- [Meta] -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description"
    content="Berry is trending dashboard template made using Bootstrap 5 design framework. Berry is available in Bootstrap, React, CodeIgniter, Angular,  and .net Technologies." />
  <meta name="keywords"
    content="Bootstrap admin template, Dashboard UI Kit, Dashboard Template, Backend Panel, react dashboard, angular dashboard" />
  <meta name="author" content="codedthemes" />

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

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body>
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->

  <!-- Flash Messages -->
  <?php if (!empty($flash_messages)): ?>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
      <?php foreach ($flash_messages as $message): ?>
        <div
          class="alert alert-<?php echo $message['type'] === 'error' ? 'danger' : $message['type']; ?> alert-dismissible fade show"
          role="alert">
          <?php echo htmlspecialchars($message['message']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form action="../../handler/auth.php" method="POST" id="loginForm">
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
    <div class="auth-main"">
      <div class=" auth-wrapper v3">
      <div class="auth-form">
        <div class="card my-5">
          <div class="card-body">
            <a href="#" class="d-flex justify-content-center">
              <img src="../../dist/assets/images/logo-150.png" alt="image" class="" />
            </a>
            <div class="row">
              <div class="d-flex justify-content-center">
                <div class="auth-header">
                  <h2 class="text-red-500 mt-5"><b>Hi, Welcome Back</b></h2>
                  <p class="f-16 mt-2">Enter your credentials to continue</p>
                </div>
              </div>
            </div>

            <!-- Error Messages -->
            <?php if (isset($_GET['error'])): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php
                switch ($_GET['error']) {
                  case 'empty_fields':
                    echo '<i class="ti ti-alert-circle me-2"></i>Username dan password harus diisi!';
                    break;
                  case 'invalid_credentials':
                    echo '<i class="ti ti-x-circle me-2"></i>Username atau password salah!';
                    break;
                  case 'db_error':
                    echo '<i class="ti ti-database-off me-2"></i>Terjadi kesalahan sistem. Silakan coba lagi.';
                    break;
                  default:
                    echo '<i class="ti ti-alert-triangle me-2"></i>Terjadi kesalahan. Silakan coba lagi.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <!-- Success Messages -->
            <?php if (isset($_GET['success'])): ?>
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                switch ($_GET['success']) {
                  case '1':
                    echo '<i class="ti ti-check-circle me-2"></i>Registrasi berhasil! Silakan login dengan akun Anda.';
                    break;
                  case 'password_reset':
                    echo '<i class="ti ti-shield-check me-2"></i>Password berhasil direset! Silakan login dengan password baru Anda.';
                    break;
                  default:
                    echo '<i class="ti ti-check-circle me-2"></i>Operasi berhasil! Silakan login.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <!-- Logout Message -->
            <?php if (isset($_GET['logout']) && $_GET['logout'] == '1'): ?>
              <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="ti ti-info-circle me-2"></i>Anda telah berhasil logout. Silakan login kembali.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <!-- Rate Limiting Alert -->
            <?php if ($rate_limit_status['is_limited']): ?>
              <div class="alert alert-warning alert-dismissible fade show" role="alert" id="rateLimitAlert"
                data-no-auto-dismiss="true">
                <i class="ti ti-clock me-2"></i>
                <strong>Terlalu banyak percobaan login gagal!</strong><br>
                Akun telah diblokir untuk keamanan. Silakan coba lagi dalam:
                <strong id="countdownTimer"><?= $rate_limit_status['remaining_time_text'] ?></strong>
                <div class="mt-2">
                  <small class="text-muted">
                    <i class="ti ti-shield-check me-1"></i>
                    Sistem keamanan akan membuka akses otomatis setelah waktu habis.
                  </small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
              <!-- Hidden input to store lockout end time -->
              <input type="hidden" id="lockoutUntilTimestamp"
                value="<?= $rate_limit_status['lockout_until_timestamp'] ?? 0 ?>">
            <?php endif; ?>

            <input type="hidden" name="action" value="login">

            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="floatingInput" name="field_username" placeholder="Username"
                required <?= $rate_limit_status['is_limited'] ? 'disabled' : '' ?> />
              <label for="floatingInput"> Username</label>
            </div>
            <div class="form-floating mb-3">
              <input type="password" class="form-control" id="floatingInput1" name="field_password"
                placeholder="Password" required <?= $rate_limit_status['is_limited'] ? 'disabled' : '' ?> />
              <label for="floatingInput1">Password</label>
            </div>
            <div class="d-flex mt-1 justify-content-between">
              <div class="form-check">
                <input class="form-check-input input-primary" type="checkbox" name="remember_me" id="customCheckc1"
                  <?= $rate_limit_status['is_limited'] ? 'disabled' : '' ?> />
                <label class="form-check-label text-muted" for="customCheckc1">Remember me</label>
              </div>
              <a href="forgot-password.php">
                <h5 class="text-primary">Forgot Password?</h5>
              </a>
            </div>
            <div class="d-grid mt-4">
              <button type="submit" class="btn btn-danger" id="loginButton" <?= $rate_limit_status['is_limited'] ? 'disabled' : '' ?>>
                <?= $rate_limit_status['is_limited'] ? 'Sedang Diblokir...' : 'Sign In' ?>
              </button>
            </div>


            <hr />
            <h5 class="d-flex justify-content-center">Don't have an account?<a href="register.php" class="ms-2">Sign
                Up</a></h5>
          </div>
        </div>
      </div>
    </div>
    </div>
  </form>
  <!-- [ Main Content ] end -->
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

  <!-- Rate Limiting Countdown Script -->
  <script>
    <?php if ($rate_limit_status['is_limited']): ?>
      // Get lockout end timestamp from server
      const lockoutUntilTimestamp = <?= $rate_limit_status['lockout_until_timestamp'] ?? 0 ?>;
      let countdownInterval = null;

      function enableForm() {
        // Enable all form inputs
        const usernameInput = document.querySelector('input[name="field_username"]');
        const passwordInput = document.querySelector('input[name="field_password"]');
        const rememberCheckbox = document.querySelector('input[name="remember_me"]');
        const loginButton = document.getElementById('loginButton');

        if (usernameInput) usernameInput.disabled = false;
        if (passwordInput) passwordInput.disabled = false;
        if (rememberCheckbox) rememberCheckbox.disabled = false;
        if (loginButton) {
          loginButton.disabled = false;
          loginButton.textContent = 'Sign In';
        }

        // Hide alert
        const alertElement = document.getElementById('rateLimitAlert');
        if (alertElement) {
          alertElement.style.display = 'none';
        }

        console.log('[Rate Limit] Form enabled, lockout expired');
      }

      function updateCountdown() {
        // Calculate remaining time based on server timestamp
        const now = Math.floor(Date.now() / 1000); // Current time in seconds
        const remainingSeconds = lockoutUntilTimestamp - now;

        if (remainingSeconds <= 0) {
          // Time's up - enable form and clear storage
          clearInterval(countdownInterval);

          // Clear ALL rate limit storage
          sessionStorage.removeItem('rate_limit_lockout_until');
          sessionStorage.removeItem('rate_limit_username');
          sessionStorage.removeItem('reload_in_progress');

          // Enable form immediately
          enableForm();

          console.log('[Rate Limit] Lockout has ended, form enabled');

          return;
        }

        const minutes = Math.floor(remainingSeconds / 60);
        const seconds = remainingSeconds % 60;
        const timeText = `${minutes}:${seconds.toString().padStart(2, '0')}`;

        const timerElement = document.getElementById('countdownTimer');
        if (timerElement) {
          timerElement.textContent = timeText;
        }
      }

      // Update countdown every second
      updateCountdown(); // Initial update
      countdownInterval = setInterval(updateCountdown, 1000);

      // Store lockout info in sessionStorage for persistence across refresh
      sessionStorage.setItem('rate_limit_lockout_until', lockoutUntilTimestamp);
      sessionStorage.setItem('rate_limit_username', '<?= htmlspecialchars($username ?? '', ENT_QUOTES) ?>');

      // Add visual countdown effect
      const alertElement = document.getElementById('rateLimitAlert');
      if (alertElement) {
        alertElement.style.animation = 'pulse 2s infinite';
      }
    <?php else: ?>
      // Check if we had a previous lockout
      // BUT only if we're NOT currently showing a rate limit alert from server
      const currentAlert = document.getElementById('rateLimitAlert');

      if (!currentAlert) {
        const storedLockoutUntil = sessionStorage.getItem('rate_limit_lockout_until');
        const storedUsername = sessionStorage.getItem('rate_limit_username');

        if (storedLockoutUntil && storedUsername) {
          const now = Math.floor(Date.now() / 1000);
          const lockoutTimestamp = parseInt(storedLockoutUntil);

          if (now < lockoutTimestamp) {
            // Still locked out - redirect with username parameter to show the alert
            const currentUrl = new URL(window.location.href);

            // Only reload if we don't already have the check_user parameter
            if (!currentUrl.searchParams.has('check_user')) {
              currentUrl.searchParams.set('check_user', storedUsername);
              window.location.href = currentUrl.toString();
            }
            // If we already have check_user but no alert showing, means PHP didn't detect rate limit
            // This can happen if lockout expired on server but not cleared in sessionStorage yet
            else {
              sessionStorage.removeItem('rate_limit_lockout_until');
              sessionStorage.removeItem('rate_limit_username');
            }
          } else {
            // Lockout expired - clear storage
            sessionStorage.removeItem('rate_limit_lockout_until');
            sessionStorage.removeItem('rate_limit_username');
          }
        }
      }
    <?php endif; ?>

    // Add CSS for pulse animation
    const style = document.createElement('style');
    style.textContent = `
      @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
      }
      
      .form-control:disabled,
      .form-check-input:disabled,
      .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
      }
      
      #rateLimitAlert {
        border-left: 4px solid #ff6b35;
        background: linear-gradient(45deg, #fff3cd, #fef7e0);
      }
      
      #countdownTimer {
        font-family: 'Courier New', monospace;
        font-size: 1.1em;
        color: #d63384;
        font-weight: bold;
      }
    `;
    document.head.appendChild(style);
  </script>

  <script>
    // Auto dismiss alerts after 5 seconds (except rate limit alert)
    document.addEventListener('DOMContentLoaded', function () {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(function (alert) {
        // Skip auto-dismiss for rate limit alert
        if (alert.getAttribute('data-no-auto-dismiss') === 'true') {
          return;
        }

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

      // Focus on username input (if not rate limited)
      const usernameInput = document.getElementById('floatingInput');
      if (usernameInput && !usernameInput.disabled) {
        usernameInput.focus();
      }
    });
  </script>


</body>
<!-- [Body] end -->

</html>