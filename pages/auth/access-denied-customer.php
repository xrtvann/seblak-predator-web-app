<?php
// Access denied page for customer role
// This page is shown when customers try to access the dashboard

// Note: This file is included from index.php, so paths are relative to root directory
// No need to require session.php again as it's already loaded in index.php

// User data is already available from the including file
// $current_user variable is passed from index.php context
?>
<!doctype html>
<html lang="en">

<head>
    <title>Access Denied | Seblak Predator</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Access denied - Customer accounts cannot access dashboard" />
    <meta name="author" content="Seblak Predator" />

    <!-- [Favicon] icon -->
    <link rel="icon" href="dist/assets/images/favicon.svg" type="image/x-icon" />
    <!-- [Google Font] Family -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
        id="main-font-link" />
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
                        <a href="#" class="d-flex justify-content-center">
                            <img src="dist/assets/images/logo-150.png" alt="Seblak Predator Logo" class="" />
                        </a>
                        <div class="row">
                            <div class="d-flex justify-content-center">
                                <div class="auth-header">
                                    <h2 class="text-danger mt-5"><b>Access Denied</b></h2>
                                    <p class="f-16 mt-2">Customer accounts cannot access dashboard</p>
                                </div>
                            </div>
                        </div>

                        <!-- Main Error Alert -->
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ti ti-ban me-2"></i>
                            <strong>Dashboard Access Restricted!</strong><br>
                            Maaf, akun customer tidak memiliki akses ke dashboard admin.
                            Fitur ini khusus untuk administrator dan staff.
                        </div>

                        <!-- User Information -->
                        <div class="alert alert-info" role="alert">
                            <i class="ti ti-user-circle me-2"></i>
                            <strong>Logged in as:</strong> <?= htmlspecialchars($current_user['name'] ?? 'User') ?><br>
                            <small class="text-muted">Role:
                                <?= htmlspecialchars($current_user['role_name'] ?? 'customer') ?> (Dashboard access not
                                available)</small>
                        </div>

                        <!-- Action Button -->
                        <div class="d-grid mt-4">
                            <a href="handler/logout.php" class="btn btn-danger">
                                <i class="ti ti-logout me-2"></i>Logout & Login as Admin
                            </a>
                        </div>

                        <hr />

                        <!-- Help Information -->
                        <h5 class="d-flex justify-content-center text-muted">
                            <i class="ti ti-help-circle me-2"></i>
                            Butuh bantuan? Hubungi administrator untuk informasi lebih lanjut.
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Js -->
    <script src="dist/assets/js/plugins/popper.min.js"></script>
    <script src="dist/assets/js/plugins/simplebar.min.js"></script>
    <script src="dist/assets/js/plugins/bootstrap.min.js"></script>
    <script src="dist/assets/js/icon/custom-font.js"></script>
    <script src="dist/assets/js/script.js"></script>
    <script src="dist/assets/js/theme.js"></script>
    <script src="dist/assets/js/plugins/feather.min.js"></script>

    <!-- Theme Configuration -->
    <script>
        layout_change("light");
        font_change("Roboto");
        change_box_container("false");
        layout_caption_change("true");
        layout_rtl_change("false");
        preset_change("preset-1");
    </script>

    <!-- Auto dismiss alerts after 5 seconds -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const alerts = document.querySelectorAll('.alert-dismissible');
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
                }, 8000); // Auto-dismiss after 8 seconds
            });
        });
    </script>
</body>

</html>