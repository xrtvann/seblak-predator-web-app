## Architecture Overview

This is a **single-file PHP application** built on top of the Berry Bootstrap admin template for a restaurant management system ("Seblak Predator"). The project uses a simple routing system within `index.php` to handle multiple pages, rather than using a traditional MVC framework.

**Key Architecture Patterns:**
- **Single Entry Point**: `index.php` serves as both the router and the main application file
- **Query-Based Routing**: Pages are determined by `$_GET['page']` parameter (e.g., `index.php?page=menu`)
- **Security-First Routing**: Uses whitelist validation via `$allowed_pages` array and `in_array()` check
- **Modular Page System**: Individual pages stored in `dist/dashboard/pages/*.php` and included dynamically
- **Static Asset Pipeline**: SCSS from `src/assets/scss/` is compiled to `dist/assets/css/` via Gulp

**Directory Structure:**
- `index.php`: Main application file containing all HTML, PHP logic, and routing
- `dist/dashboard/pages/`: Individual page components (dashboard.php, menu.php, kategori.php, transaksi.php, user.php)
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
1. Add new page route to both `$PageTitle` and `$allowed_pages` arrays in `index.php`
2. Create corresponding PHP file in `dist/dashboard/pages/`
3. Add navigation link with `href="index.php?page=pagename"` format
4. Include active menu logic: `<?php echo ($page === 'pagename') ? 'active' : ''; ?>`
5. Use consistent breadcrumb structure from existing pages

## Code Conventions

**Routing & Security:**
- Always add new pages to `$allowed_pages` whitelist array for security
- Use `in_array($page, $allowed_pages)` pattern for route validation
- Page files must match exactly with route names (e.g., `menu` route → `menu.php` file)

**Active Menu State Management:**
- Navigation items use PHP ternary operators for active state
- Pattern: `<li class="pc-item <?php echo ($page === 'menu') ? 'active' : ''; ?>">`
- Dashboard uses `$page === 'dashboard'` (not '/' anymore)
- All menu links follow `index.php?page=route` format for consistency

**Page Structure Patterns:**
- Start with breadcrumb section using consistent HTML structure
- Use Berry Bootstrap card components for content sections
- Statistics cards follow specific pattern with `avtar` icons and color classes
- Table views use `table-responsive` wrapper with `table-hover` class
- Dual view components (table/card) use Bootstrap Pills navigation with `tab-pane` content

**Asset References:**
- All production assets must reference `dist/assets/` path
- Primary stylesheet: `dist/assets/css/style.css`
- Theme presets: `dist/assets/css/style-preset.css`
- Icons: Multiple icon libraries (Tabler, Feather, FontAwesome, Material, Phosphor)
- Image handling: Use Unsplash URLs with proper sizing parameters

**Restaurant-Specific Features:**
- Menu items support dual view (table + card layouts) with tab switching
- Use food/beverage appropriate imagery from Unsplash
- Statistics cards display relevant metrics (menu count, transactions, revenue, customers)
- Color coding: Primary for food items, Info for beverages, Success for active status
