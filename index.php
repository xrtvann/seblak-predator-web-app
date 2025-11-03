<?php
// Initialize secure session and authentication
require_once 'config/session.php';
require_once 'services/WebAuthService.php';
require_once 'services/SessionEncryption.php';

// Initialize authentication service
$auth_service = new WebAuthService($koneksi);

// Check remember me cookie for auto-login
$auth_service->checkRememberMe();

// Get current user data first
$current_user = getCurrentSessionUser();

// Check if user is logged in for protected pages
$protected_pages = ['dashboard', 'dasar-seblak', 'topping', 'kategori', 'transaksi', 'expenses', 'user', 'role'];
$current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// If user is not logged in and trying to access protected page, redirect to login
if (in_array($current_page, $protected_pages) && !isLoggedIn()) {
  header('Location: pages/auth/login.php');
  exit();
}

// If user is logged in but is a customer, redirect to access denied page
if (isLoggedIn() && $current_user && $current_user['role_name'] === 'customer') {
  // Redirect to dedicated customer access denied page
  include 'pages/auth/access-denied-customer.php';
  exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$PageTitle = [
  'dashboard' => 'Dashboard',
  'dasar-seblak' => 'Dasar Seblak',
  'topping' => 'Topping',
  'kategori' => 'Kategori',
  'transaksi' => 'Transaksi',
  'expenses' => 'Pengeluaran',
  'user' => 'User',
  'role' => 'Role'
];
$title = isset($PageTitle[$page]) ? $PageTitle[$page] : 'Seblak Predator';

// Validate page to prevent unauthorized access
$allowed_pages = ['dashboard', 'dasar-seblak', 'topping', 'kategori', 'transaksi', 'expenses', 'user', 'role'];
if (!in_array($page, $allowed_pages)) {
  $page = 'dashboard';
}

// Check role-based page access
if (isLoggedIn() && !canAccessPage($page)) {
  // Redirect to first accessible page or show access denied
  $accessible_pages = getAccessiblePages();
  if (!empty($accessible_pages)) {
    header('Location: index.php?page=' . $accessible_pages[0]);
    exit();
  } else {
    http_response_code(403);
    include 'pages/auth/access-denied-permissions.php';
    exit();
  }
}
?>

<!doctype html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title><?= htmlspecialchars($title) ?></title>
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
  <link rel="icon" href="dist/assets/images/favicon.png" type="image/x-icon" />
  <!-- [Google Font] Family -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">
  <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
    id="main-font-link" /> -->
  <!-- [phosphor Icons] https://phosphoricons.com/ -->
  <link rel="stylesheet" href="dist/assets/fonts/phosphor/duotone/style.css" />
  <!-- [Tabler Icons] https://tablericons.com -->
  <link rel="stylesheet" href="dist/assets/fonts/tabler-icons.min.css" />
  <!-- [Feather Icons] https://feathericons.com -->
  <link rel="stylesheet" href="dist/assets/fonts/feather.css" />
  <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
  <link rel="stylesheet" href="dist/assets/fonts/fontawesome.css" />
  <!-- [Material Icons] https://fonts.google.com/icons -->
  <link rel="stylesheet" href="dist/assets/fonts/material.css" />
  <!-- [Template CSS Files] -->
  <link rel="stylesheet" href="dist/assets/css/style.css" id="main-style-link" />
  <link rel="stylesheet" href="dist/assets/css/style-preset.css" />

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
  <!-- [ Sidebar Menu ] start -->
  <nav class="pc-sidebar">
    <div class="navbar-wrapper">
      <div class="m-header">
        <a href="/" class="b-brand text-black d-flex align-items-md-center">
          <!-- ========   Change your logo from here   ============ -->
          <div class="icon">
            <div
              class="bg-red-500 rounded-lg flex align-items-center justify-content-center flex-shrink-0 px-2 py-2 rounded">
              <i class="fas fa-pepper-hot text-white text-xl" aria-hidden="true"></i>
            </div>
          </div>

          <div class="d-flex flex-column ms-3">
            <span class="text-2xl fw-bold">Seblak</span>
            <span class="b-sub-title">Predator</span>
          </div>
        </a>
      </div>
      <div class="navbar-content">
        <ul class="pc-navbar">
          <?php
          // Get accessible pages for current user
          $accessible_pages = getAccessiblePages();
          ?>

          <?php if (in_array('dashboard', $accessible_pages)): ?>
            <li class="pc-item <?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
              <a href="index.php?page=dashboard" class="pc-link"><span class="pc-micon"><i
                    class="ti ti-dashboard"></i></span><span class="pc-mtext">Dashboard</span></a>
            </li>
          <?php endif; ?>

          <?php if (in_array('dasar-seblak', $accessible_pages) || in_array('topping', $accessible_pages) || in_array('kategori', $accessible_pages)): ?>
            <li class="pc-item pc-caption">
              <label>Produk</label>
            </li>
          <?php endif; ?>

          <?php if (in_array('dasar-seblak', $accessible_pages)): ?>
            <li class="pc-item <?php echo ($page === 'dasar-seblak') ? 'active' : ''; ?>">
              <a href="index.php?page=dasar-seblak" class="pc-link">
                <span class="pc-micon"><i class="ti ti-soup"></i></span>
                <span class="pc-mtext">Dasar Seblak</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if (in_array('topping', $accessible_pages)): ?>
            <li class="pc-item <?php echo ($page === 'topping') ? 'active' : ''; ?>">
              <a href="index.php?page=topping" class="pc-link">
                <span class="pc-micon"><i class="ti ti-meat"></i></span>
                <span class="pc-mtext">Topping</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if (in_array('kategori', $accessible_pages)): ?>
            <li class="pc-item <?php echo ($page === 'kategori') ? 'active' : ''; ?>">
              <a href="index.php?page=kategori" class="pc-link">
                <span class="pc-micon"><i class="ti ti-stack"></i></span>
                <span class="pc-mtext">Kategori</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if (in_array('transaksi', $accessible_pages) || in_array('expenses', $accessible_pages)): ?>
            <li class="pc-item pc-caption">
              <label>Transaksi</label>
            </li>
            <li class="pc-item <?php echo ($page === 'transaksi') ? 'active' : ''; ?>">
              <a href="?page=transaksi" class="pc-link">
                <span class="pc-micon"><i class="ti ti-receipt"></i></span>
                <span class="pc-mtext">Transaksi</span>
              </a>
            </li>
            <?php if (in_array('expenses', $accessible_pages)): ?>
              <li class="pc-item <?php echo ($page === 'expenses') ? 'active' : ''; ?>">
                <a href="?page=expenses" class="pc-link">
                  <span class="pc-micon"><i class="ti ti-cash"></i></span>
                  <span class="pc-mtext">Pengeluaran</span>
                </a>
              </li>
            <?php endif; ?>
          <?php endif; ?>

          <?php if (in_array('user', $accessible_pages) || in_array('role', $accessible_pages)): ?>
            <li class="pc-item pc-caption">
              <label>Manajemen User</label>
            </li>
          <?php endif; ?>

          <?php if (in_array('user', $accessible_pages)): ?>
            <li class="pc-item <?php echo ($page === 'user') ? 'active' : ''; ?>">
              <a href="?page=user" class="pc-link">
                <span class="pc-micon"><i class="ti ti-users"></i></span>
                <span class="pc-mtext">User</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if (in_array('role', $accessible_pages)): ?>
            <li class="pc-item <?php echo ($page === 'role') ? 'active' : ''; ?>">
              <a href="?page=role" class="pc-link">
                <span class="pc-micon"><i class="ti ti-shield-lock"></i></span>
                <span class="pc-mtext">Role</span>
              </a>
            </li>
          <?php endif; ?>
        </ul>
        <div class="w-100 text-center">
          <div class="badge theme-version badge rounded-pill bg-light text-dark f-12"></div>
        </div>
      </div>
    </div>
  </nav>
  <!-- [ Sidebar Menu ] end -->
  <!-- [ Header Topbar ] start -->
  <header class="pc-header">
    <div class="header-wrapper"><!-- [Mobile Media Block] start -->
      <div class="me-auto pc-mob-drp">
        <ul class="list-unstyled">
          <li class="pc-h-item header-mobile-collapse">
            <a href="#" class="pc-head-link head-link-secondary ms-0" id="sidebar-hide">
              <i class="ti ti-menu-2"></i>
            </a>
          </li>
          <li class="pc-h-item pc-sidebar-popup">
            <a href="#" class="pc-head-link head-link-secondary ms-0" id="mobile-collapse">
              <i class="ti ti-menu-2"></i>
            </a>
          </li>
          <li class="dropdown pc-h-item d-inline-flex d-md-none">
            <a class="pc-head-link head-link-secondary dropdown-toggle arrow-none m-0" data-bs-toggle="dropdown"
              href="#" role="button" aria-haspopup="false" aria-expanded="false">
              <i class="ti ti-search"></i>
            </a>
            <div class="dropdown-menu pc-h-dropdown drp-search">
              <form class="px-3">
                <div class="mb-0 d-flex align-items-center">
                  <i data-feather="search"></i>
                  <input type="search" class="form-control border-0 shadow-none" placeholder="Search here. . ." />
                </div>
              </form>
            </div>
          </li>
          <li class="pc-h-item d-none d-md-inline-flex">
            <form class="header-search">
              <i data-feather="search" class="icon-search"></i>
              <input type="search" class="form-control" placeholder="Search here. . ." />
              <button class="btn btn-light-secondary btn-search"><i class="ti ti-adjustments-horizontal"></i></button>
            </form>
          </li>
        </ul>
      </div>
      <!-- [Mobile Media Block end] -->
      <div class="ms-auto">
        <ul class="list-unstyled">
          <li class="dropdown pc-h-item">
            <a class="pc-head-link head-link-secondary dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
              href="#" role="button" aria-haspopup="false" aria-expanded="false">
              <i class="ti ti-bell"></i>
            </a>
            <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
              <div class="dropdown-header">
                <a href="#!" class="link-primary float-end text-decoration-underline">Mark as all read</a>
                <h5>
                  All Notification
                  <span class="badge bg-warning rounded-pill ms-1">01</span>
                </h5>
              </div>
              <div class="dropdown-header px-0 text-wrap header-notification-scroll position-relative"
                style="max-height: calc(100vh - 215px)">
                <div class="list-group list-group-flush w-100">
                  <dv class="list-group-item list-group-item-action">
                    <div class="d-flex">
                      <div class="flex-shrink-0">
                        <div class="user-avtar bg-light-success"><i class="ti ti-building-store"></i></div>
                      </div>
                      <div class="flex-grow-1 ms-1">
                        <span class="float-end text-muted">3 min ago</span>
                        <h5>Store Verification Done</h5>
                        <p class="text-body fs-6">We have successfully received your request.</p>
                        <div class="badge rounded-pill bg-light-danger">Unread</div>
                      </div>
                    </div>
                  </dv>
                  <div class="list-group-item list-group-item-action">
                    <div class="d-flex">
                      <div class="flex-shrink-0">
                        <img src="dist/assets/images/user/avatar-3.jpg" alt="user-image" class="user-avtar" />
                      </div>
                      <div class="flex-grow-1 ms-1">
                        <span class="float-end text-muted">10 min ago</span>
                        <h5>Joseph William</h5>
                        <p class="text-body fs-6">It is a long established fact that a reader will be distracted</p>
                        <div class="badge rounded-pill bg-light-success">Confirmation of Account</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="dropdown-divider"></div>
              <div class="text-center py-2">
                <a href="#!" class="link-primary">Mark as all read</a>
              </div>
            </div>
          </li>
          <li class="dropdown pc-h-item">
            <a class="pc-head-link head-link-secondary dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
              href="#" role="button" aria-haspopup="false" aria-expanded="false">
              <span>
                <i class="ti ti-user"></i>
              </span>
            </a>
            <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
              <div class="dropdown-header">
                <h4>
                  Selamat datang,
                  <span class="small text-muted"><?= htmlspecialchars($current_user['name'] ?? 'User') ?></span>
                </h4>
                <p class="text-muted">@<?= htmlspecialchars($current_user['username'] ?? 'unknown') ?> •
                  <?= htmlspecialchars($current_user['role_name'] ?? 'Customer') ?>
                </p>
                <hr />
                <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 280px)">
                  <div class="upgradeplan-block bg-light-primary rounded">
                    <h5><i class="ti ti-crown me-2"></i>Seblak Predator</h5>
                    <p class="text-muted">Sistem Manajemen Restoran</p>
                    <small class="text-muted">Login:
                      <?= date('H:i, d M Y', $_SESSION['login_time'] ?? time()) ?></small>
                  </div>
                  <hr />
                  <a href="index.php?page=user" class="dropdown-item">
                    <i class="ti ti-settings"></i>
                    <span>Pengaturan Akun</span>
                  </a>
                  <a href="index.php?page=dashboard" class="dropdown-item">
                    <i class="ti ti-dashboard"></i>
                    <span>Dashboard</span>
                  </a>
                  <div class="dropdown-divider"></div>
                  <a href="#" class="dropdown-item text-danger" onclick="handleLogout()">
                    <i class="ti ti-logout"></i>
                    <span>Logout</span>
                  </a>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </header>
  <!-- [ Header ] end -->



  <!-- [ Main Content ] start -->
  <div class="pc-container ">
    <div class="pc-content">
      <!-- [ Main Content ] start -->
      <?php
      // Daftar halaman yang diizinkan
      $allowed_pages = ['dashboard', 'dasar-seblak', 'topping', 'kategori', 'transaksi', 'expenses', 'user', 'role'];

      if (in_array($page, $allowed_pages)) {
        include("dist/dashboard/pages/" . $page . ".php");
      } else {
        echo '<div class="container"><div class="alert alert-danger">Halaman tidak ditemukan.</div></div>';
      }
      ?>
      <!-- [ Main Content ] end -->
    </div>
  </div>
  <!-- [ Main Content ] end -->

  <!-- Required Js -->
  <script src="dist/assets/js/plugins/popper.min.js"></script>
  <script src="dist/assets/js/plugins/simplebar.min.js"></script>
  <script src="dist/assets/js/plugins/bootstrap.min.js"></script>

  <!-- SweetAlert2 CDN -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Custom Alert Functions -->
  <script src="src/assets/js/sweetalert/alert.js"></script>
  <script src="dist/assets/js/script.js"></script>
  <script src="dist/assets/js/theme.js"></script>
  <script src="dist/assets/js/plugins/feather.min.js"></script>


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



  <!-- [Page Specific JS] start -->
  <!-- Apex Chart -->
  <!-- [Page Specific JS] end -->

  <!-- Global SweetAlert Functions -->
  <script>
    function handleLogout() {
      showConfirmation(
        'Konfirmasi Logout',
        'Apakah Anda yakin ingin keluar dari sistem?',
        () => {
          showLoading('Logging out...', 'Sedang memproses logout...');

          // Create a form with CSRF token for secure logout
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = 'handler/logout.php';

          const csrfInput = document.createElement('input');
          csrfInput.type = 'hidden';
          csrfInput.name = 'csrf_token';
          csrfInput.value = '<?php echo generateCSRFToken(); ?>';

          form.appendChild(csrfInput);
          document.body.appendChild(form);
          form.submit();
        },
        null,
        'Ya, Logout',
        'Batal'
      );
    }
  </script>
</body>
<!-- [Body] end -->

</html>