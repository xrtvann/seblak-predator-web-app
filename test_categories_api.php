<?php
// Test categories API directly
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['status'] = 'all';
$_GET['per_page'] = '10';

// Capture output
ob_start();
include 'api/menu/categories.php';
$output = ob_get_clean();

echo "API Output:\n";
echo $output;
?>