<?php
// Test the kategori page data loading
require_once 'config/koneksi.php';

echo "<h2>Testing Kategori Page Data Loading</h2>";

// Test database connection
if (!$koneksi) {
    echo "<p style='color: red;'>Database connection failed: " . mysqli_connect_error() . "</p>";
    exit;
}

echo "<p style='color: green;'>✓ Database connection successful</p>";

// Test categories query
$categoriesQuery = "SELECT c.*, 
    (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.is_active = TRUE) as product_count
    FROM categories c 
    WHERE c.is_active = TRUE 
    ORDER BY c.created_at DESC";

$categoriesResult = mysqli_query($koneksi, $categoriesQuery);

if (!$categoriesResult) {
    echo "<p style='color: red;'>Query failed: " . mysqli_error($koneksi) . "</p>";
    exit;
}

echo "<p style='color: green;'>✓ Categories query successful</p>";

$categories = [];
while ($row = mysqli_fetch_assoc($categoriesResult)) {
    $categories[] = $row;
}

echo "<h3>Categories Found: " . count($categories) . "</h3>";

if (count($categories) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Product Count</th><th>Created</th></tr>";

    foreach ($categories as $category) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($category['id']) . "</td>";
        echo "<td>" . htmlspecialchars($category['name']) . "</td>";
        echo "<td>" . htmlspecialchars($category['type']) . "</td>";
        echo "<td>" . htmlspecialchars($category['product_count']) . "</td>";
        echo "<td>" . htmlspecialchars($category['created_at']) . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    // Calculate statistics
    $totalCategories = count($categories);
    $activeProductCategories = count(array_filter($categories, function ($cat) {
        return $cat['type'] === 'product'; }));
    $activeToppingCategories = count(array_filter($categories, function ($cat) {
        return $cat['type'] === 'topping'; }));
    $totalProducts = array_sum(array_column($categories, 'product_count'));

    echo "<h3>Statistics:</h3>";
    echo "<ul>";
    echo "<li>Total Categories: $totalCategories</li>";
    echo "<li>Product Categories: $activeProductCategories</li>";
    echo "<li>Topping Categories: $activeToppingCategories</li>";
    echo "<li>Total Products: $totalProducts</li>";
    echo "</ul>";

} else {
    echo "<p>No categories found in database.</p>";
}

echo "<p><a href='index.php?page=kategori'>→ Go to Kategori Page</a></p>";
?>