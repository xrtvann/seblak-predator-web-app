<?php
require_once 'config/koneksi.php';

echo "Database connection test:\n";
if ($koneksi) {
    echo "Connected successfully\n";

    // Test products table
    $query = 'SELECT COUNT(*) as total FROM products';
    $result = mysqli_query($koneksi, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo 'Total products: ' . $row['total'] . "\n";
    } else {
        echo 'Database error: ' . mysqli_error($koneksi) . "\n";
    }

    // Test categories table
    $query = 'SELECT COUNT(*) as total FROM categories';
    $result = mysqli_query($koneksi, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo 'Total categories: ' . $row['total'] . "\n";
    } else {
        echo 'Database error: ' . mysqli_error($koneksi) . "\n";
    }

} else {
    echo "Connection failed\n";
}
?>