<?php
/**
 * Clear PHP OPcache
 * Access this file via browser to clear opcache
 */

header('Content-Type: application/json');

$result = [
    'opcache_reset' => false,
    'message' => ''
];

if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        $result['opcache_reset'] = true;
        $result['message'] = 'OPcache cleared successfully!';
    } else {
        $result['message'] = 'Failed to clear OPcache';
    }
} else {
    $result['message'] = 'OPcache not enabled';
}

echo json_encode($result, JSON_PRETTY_PRINT);
