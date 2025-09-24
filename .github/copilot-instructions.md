## Architecture Overview

This is a **single-file PHP application** built on top of the Berry Bootstrap admin template. The project uses a simple routing system within `index.php` to handle multiple pages, rather than using a traditional MVC framework.

**Key Architecture Patterns:**
- **Single Entry Point**: `index.php` serves as both the router and the main application file
- **Query-Based Routing**: Pages are determined by `$_GET['page']` parameter (e.g., `index.php?page=menu`)
- **Inline Page Mapping**: Page titles and routes are defined in associative arrays at the top of `index.php`
- **Static Asset Pipeline**: SCSS from `src/assets/scss/` is compiled to `dist/assets/css/` via Gulp

**Directory Structure:**
- `index.php`: Main application file containing all HTML, PHP logic, and routing
- `dist/assets/`: Compiled CSS, JavaScript, images, and fonts (production assets)
- `src/assets/`: Source SCSS files and development assets
- `src/html/`: Static HTML templates (reference/prototype files)
- `gulpfile.js`: Asset compilation configuration

## Critical Developer Workflows

**Frontend Asset Development:**
```bash
npm install          # Install Gulp and build dependencies
gulp                 # Compile SCSS to CSS and watch for changes
gulp build-prod      # Production build with minification
```

**Page Development Pattern:**
1. Add new page route to `$PageTitle` array in `index.php`
2. Add navigation link with `href="index.php?page=pagename"` format
3. Add content section with conditional PHP: `<?php if ($page === 'pagename'): ?>`
4. Include active menu logic: `<?php echo ($page === 'pagename') ? 'active' : ''; ?>`

## Code Conventions

**Active Menu State Management:**
- Navigation items use PHP ternary operators for active state
- Pattern: `<li class="pc-item <?php echo ($page === 'menu') ? 'active' : ''; ?>">`
- Dashboard uses `$page === '/'` for default/home state
- All menu links follow `index.php?page=route` format for consistency

**Asset References:**
- All production assets must reference `dist/assets/` path
- Primary stylesheet: `dist/assets/css/style.css`
- Theme presets: `dist/assets/css/style-preset.css`
- Icons: Multiple icon libraries loaded from `dist/assets/fonts/`

**Page Title Management:**
- Centralized in `$PageTitle` array mapping routes to display names
- Automatic fallback to "Seblak Predator" for undefined routes
- HTML title format: `<?= htmlspecialchars($title) ?>`
