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

  <form action="../../handler/auth.php" method="POST">
    <div class="auth-main">
      <div class="auth-wrapper v3">
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

              <!-- Success Message -->
              <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <i class="ti ti-check-circle me-2"></i>Registrasi berhasil! Silakan login dengan akun Anda.
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

              <input type="hidden" name="action" value="login">

              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInput" name="field_username" placeholder="Username"
                  required />
                <label for="floatingInput"> Username</label>
              </div>
              <div class="form-floating mb-3">
                <input type="password" class="form-control" id="floatingInput1" name="field_password"
                  placeholder="Password" required />
                <label for="floatingInput1">Password</label>
              </div>
              <div class="d-flex mt-1 justify-content-between">
                <div class="form-check">
                  <input class="form-check-input input-primary" type="checkbox" name="remember_me" id="customCheckc1" />
                  <label class="form-check-label text-muted" for="customCheckc1">Remember me</label>
                </div>
                <a href="#">
                  <h5 class="text-primary">Forgot Password?</h5>
                </a>
              </div>
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-danger">Sign In</button>
              </div>
              <div class="saprator mt-3">
                <span>or</span>
              </div>
              <div class="d-grid">
                <button type="button" class="btn mt-2 bg-light-primary bg-light text-muted">
                  <img src="../../dist/assets/images/authentication/google-icon.svg" alt="image" />Sign In With Google
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

  <script>
    // Auto dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function () {
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

      // Focus on username input
      const usernameInput = document.getElementById('floatingInput');
      if (usernameInput) {
        usernameInput.focus();
      }
    });
  </script>


</body>
<!-- [Body] end -->

</html>