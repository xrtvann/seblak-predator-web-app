<!doctype html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>SignUp | Seblak Predator</title>
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

          <div class="card mt-5">
            <div class="card-body">
              <a href="#" class="d-flex justify-content-center mt-3">
                <img src="../../dist/assets/images/logo-150.png" alt="image" class="img-fluid" />
              </a>
              <div class="row">
                <div class="d-flex justify-content-center">
                  <div class="auth-header">
                    <h2 class="text-red-500 mt-5"><b>Sign up</b></h2>
                    <p class="f-16 mt-2">Enter your credentials to continue</p>
                  </div>
                </div>
              </div>

              <h5 class="my-4 d-flex justify-content-center">Sign Up with Email address</h5>

              <div class="form-floating mb-3">
                <input type="hidden" name="action" value="register">
                <input type="text" class="form-control" id="floatingInput" name="field_name" required />
                <label for="floatingInput">Fullname</label>
              </div>
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInput2" name="field_username" required />
                <label for="floatingInput2">Username</label>
              </div>
              <div class="form-floating mb-3">
                <input type="password" class="form-control" id="floatingInput3" name="field_password" required />
                <label for="floatingInput3">Password</label>
              </div>
              <div class="form-check mt-3s">
                <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" checked="" />
                <label class="form-check-label" for="customCheckc1">
                  <span class="h5 mb-0">Agree with <span>Terms & Condition.</span></span>
                </label>
              </div>
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-danger p-2">Sign Up</button>
              </div>
              <div class="saprator mt-3">
                <span>or</span>
              </div>
              <button type="button" class="btn mt-2 bg-light-primary bg-light text-muted" style="width: 100%">
                <img src="../../dist/assets/images/authentication/google-icon.svg" alt="image" />Sign Up With Google
              </button>

              <hr />
              <h5 class="d-flex justify-content-center">Already have an account?<a href="login.php" class="ms-2">Sign
                  In</a></h5>
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


</body>
<!-- [Body] end -->

</html>