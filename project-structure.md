# Seblak Predator Project Structure

This document provides a complete representation of the project structure for reference in future code generation and improvements.

```
seblak-predator/
├── .babelrc
├── .gitignore
├── composer.json
├── composer.lock
├── generate_keys.php
├── gulpfile.js
├── index.php
├── package-lock.json
├── package.json
├── phpunit.xml
├── README.md
├── TODO.md
├── yarn.lock
├── api/
│   ├── check-image.php
│   ├── expense-categories.php
│   ├── expenses.php
│   ├── helpers.php
│   ├── orders.php
│   ├── roles.php
│   ├── Seblak_Predator_API.postman_collection.json
│   ├── Seblak_Predator_Environment.postman_environment.json
│   ├── users.php
│   ├── auth/
│   │   ├── JWTHelper.php
│   │   ├── login.php
│   │   ├── logout.php
│   │   ├── middleware.php
│   │   ├── profile.php
│   │   ├── refresh.php
│   │   └── validate.php
│   ├── menu/
│   │   ├── categories.php
│   │   └── products.php
│   ├── midtrans/
│   │   ├── config.php
│   │   ├── create-transaction.php
│   │   └── README.md
│   ├── sync/
│   │   ├── categories.php
│   │   └── products.php
│   └── upload/
│       └── image.php
├── config/
│   ├── config.php
│   ├── database.php
│   ├── env.php
│   ├── koneksi.php
│   └── session.php
├── docs/
│   ├── KATEGORI_DELETED_ITEMS_FEATURE.md
│   └── system_flowchart.md
├── handler/
│   ├── auth.php
│   ├── forgot_password.php
│   └── logout.php
├── pages/
│   └── auth/
│       ├── access-denied-customer.php
│       ├── access-denied-permissions.php
│       ├── forgot-password.php
│       ├── login.php
│       └── register.php
├── services/
│   ├── DevelopmentEmailService.php
│   ├── EmailService.php
│   ├── SessionEncryption.php
│   ├── SimpleDevelopmentEmailService.php
│   ├── SimpleEmailService.php
│   └── WebAuthService.php
├── sql/
│   ├── create_transactions.sql
│   ├── jwt_auth_schema.sql
│   ├── setup_roles.sql
│   └── transactions_schema.sql
├── src/
│   ├── assets/
│   │   ├── fonts/
│   │   │   ├── feather.css
│   │   │   ├── fontawesome.css
│   │   │   ├── material.css
│   │   │   ├── tabler-icons.min.css
│   │   │   ├── feather/
│   │   │   │   ├── feather.eot
│   │   │   │   ├── feather.svg
│   │   │   │   ├── feather.ttf
│   │   │   │   └── feather.woff
│   │   │   ├── fontawesome/
│   │   │   │   ├── fa-brands-400.eot
│   │   │   │   ├── fa-brands-400.svg
│   │   │   │   ├── fa-brands-400.ttf
│   │   │   │   ├── fa-brands-400.woff
│   │   │   │   ├── fa-brands-400.woff2
│   │   │   │   ├── fa-regular-400.eot
│   │   │   │   ├── fa-regular-400.svg
│   │   │   │   ├── fa-regular-400.ttf
│   │   │   │   ├── fa-regular-400.woff
│   │   │   │   ├── fa-regular-400.woff2
│   │   │   │   ├── fa-solid-900.eot
│   │   │   │   ├── fa-solid-900.svg
│   │   │   │   ├── fa-solid-900.ttf
│   │   │   │   ├── fa-solid-900.woff
│   │   │   │   └── fa-solid-900.woff2
│   │   │   ├── material/
│   │   │   │   └── material.woff2
│   │   │   ├── phosphor/
│   │   │   │   └── duotone/
│   │   │   └── tabler/
│   │   │       ├── tabler-icons.eot
│   │   │       ├── tabler-icons.svg
│   │   │       ├── tabler-icons.ttf
│   │   │       ├── tabler-icons.woff
│   │   │       └── tabler-icons.woff2
│   │   ├── images/
│   │   │   ├── favicon.svg
│   │   │   ├── logo-dark.svg
│   │   │   ├── logo-white.svg
│   │   │   ├── logo.svg
│   │   │   ├── authentication/
│   │   │   │   └── google-icon.svg
│   │   │   └── user/
│   │   │       ├── avatar-1.jpg
│   │   │       ├── avatar-2.jpg
│   │   │       ├── avatar-3.jpg
│   │   │       ├── avatar-4.jpg
│   │   │       ├── avatar-5.jpg
│   │   │       ├── avatar-6.jpg
│   │   │       ├── avatar-7.jpg
│   │   │       └── avatar-10.jpg
│   │   ├── js/
│   │   │   ├── auth.js
│   │   │   ├── forgot-password-backend.js
│   │   │   ├── script.js
│   │   │   ├── theme.js
│   │   │   ├── fonts/
│   │   │   ├── pages/
│   │   │   └── sweetalert/
│   │   ├── json/
│   │   │   └── README.md
│   │   └── scss/
│   │       ├── landing.scss
│   │       ├── style-preset.scss
│   │       ├── style.scss
│   │       ├── settings/
│   │       └── themes/
│   └── html/
│       ├── index.html
│       ├── admins/
│       │   └── README.md
│       ├── application/
│       │   └── README.md
│       ├── chart/
│       │   └── README.md
│       ├── dashboard/
│       │   └── index.html
│       ├── elements/
│       │   ├── bc_color.html
│       │   ├── bc_typography.html
│       │   └── icon-tabler.html
│       ├── forms/
│       │   └── README.md
│       ├── layouts/
│       │   ├── breadcrumb.html
│       │   ├── footer-block.html
│       │   ├── footer-js.html
│       │   ├── head-css.html
│       │   ├── head-page-meta.html
│       │   ├── header-content.html
│       │   ├── layout-vertical.html
│       │   ├── loader.html
│       │   ├── menu-list.html
│       │   ├── sidebar.html
│       │   └── topbar.html
│       ├── other/
│       │   └── sample-page.html
│       ├── pages/
│       │   ├── login-v3.html
│       │   └── register-v3.html
│       ├── table/
│       │   └── README.md
│       └── widget/
│           └── README.md
├── tests/
│   ├── LoginTest.php
│   └── _output/
└── uploads/
    ├── .htaccess
    └── menu-images/
        ├── .gitkeep
        ├── menu_68e5a1ec781ca_1759879660.jpg
        ├── menu_68e5a3d67e669_1759880150.png
        ├── menu_68e5a9e99b77e_1759881705.png
        ├── menu_68e5a4063c542_1759880198.png
        ├── menu_68e5a39171925_1759880081.png
        ├── menu_68e5b7fc8445a_1759885308.png
        ├── menu_68e5b600529ca_1759884800.png
        ├── menu_68e70a39413d6_1759971897.png
        ├── menu_68e70cbe1c858_1759972542.png
        ├── menu_68e709f817f62_1759971832.png
        └── menu_68e7066800e06_1759970920.jpg
```

## Project Overview

- **Root Directory**: `seblak-predator/`
- **Main Technologies**: PHP (backend), JavaScript (frontend), SCSS (styling), HTML templates
- **Key Directories**:
  - `api/`: API endpoints for authentication, menu, orders, etc.
  - `config/`: Configuration files for database, environment, etc.
  - `src/`: Source assets (fonts, images, JS, SCSS, HTML templates)
  - `services/`: Email and authentication services
  - `sql/`: Database schemas and setup scripts
  - `tests/`: PHPUnit tests
  - `uploads/`: Uploaded images (menu images)
  - `docs/`: Documentation files

This structure can be used as a reference for maintaining consistency in future code additions and improvements.
