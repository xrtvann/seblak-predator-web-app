<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    // Handle different request methods
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        handlePostRequest();
    } else {
        handleGetRequest();
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

function handleGetRequest()
{
    $lines = isset($_GET['lines']) ? intval($_GET['lines']) : 50;
    $lines = max(10, min(1000, $lines)); // Limit between 10 and 1000 lines

    $logs = readLogs($lines);

    echo json_encode([
        'success' => true,
        'logs' => $logs,
        'total' => count($logs),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function handlePostRequest()
{
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['action'])) {
        throw new Exception('Invalid request');
    }

    switch ($input['action']) {
        case 'clear':
            clearLogs();
            echo json_encode([
                'success' => true,
                'message' => 'Logs cleared successfully'
            ]);
            break;

        default:
            throw new Exception('Unknown action: ' . $input['action']);
    }
}

function readLogs($lines)
{
    $logFiles = findLogFiles();
    $allLogs = [];

    foreach ($logFiles as $logFile) {
        if (file_exists($logFile) && is_readable($logFile)) {
            $logs = parseLogFile($logFile, $lines);
            $allLogs = array_merge($allLogs, $logs);
        }
    }

    // Sort by timestamp (newest first)
    usort($allLogs, function ($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });

    return array_slice($allLogs, 0, $lines);
}

function findLogFiles()
{
    $possibleLogFiles = [
        // PHP error log
        ini_get('error_log'),

        // Common Laragon/XAMPP locations
        'C:/laragon/bin/apache/httpd-2.4.54/logs/error.log',
        'C:/laragon/bin/apache/httpd-2.4.53/logs/error.log',
        'C:/laragon/logs/apache_error.log',
        'C:/xampp/apache/logs/error.log',

        // Local project logs
        __DIR__ . '/logs/error.log',
        __DIR__ . '/../logs/error.log',
        __DIR__ . '/../../logs/error.log',

        // System logs (Linux/Mac)
        '/var/log/apache2/error.log',
        '/var/log/httpd/error_log',
        '/usr/local/var/log/apache2/error.log',

        // Windows Event Logs (if accessible)
        'C:/Windows/temp/php-errors.log'
    ];

    // Filter to only existing, readable files
    return array_filter($possibleLogFiles, function ($file) {
        return $file && file_exists($file) && is_readable($file);
    });
}

function parseLogFile($logFile, $maxLines)
{
    $logs = [];

    try {
        // Read last N lines efficiently
        $lines = tailFile($logFile, $maxLines * 2); // Read more to account for multi-line entries

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line))
                continue;

            $parsedLog = parseLogLine($line);
            if ($parsedLog) {
                $logs[] = $parsedLog;
            }
        }

    } catch (Exception $e) {
        // If file reading fails, try alternative method
        error_log("Failed to read log file $logFile: " . $e->getMessage());
    }

    return $logs;
}

function tailFile($file, $lines)
{
    $handle = fopen($file, "r");
    if (!$handle) {
        throw new Exception("Cannot open file: $file");
    }

    $linecounter = $lines;
    $pos = -2;
    $beginning = false;
    $text = [];

    // Seek to end of file
    fseek($handle, 0, SEEK_END);
    $filesize = ftell($handle);

    if ($filesize == 0) {
        fclose($handle);
        return [];
    }

    while ($linecounter > 0) {
        if (fseek($handle, $pos, SEEK_END) == -1) {
            $beginning = true;
            break;
        }

        $char = fgetc($handle);
        if ($char == "\n") {
            $linecounter--;
        }
        $pos--;
    }

    if ($beginning) {
        fseek($handle, 0);
    }

    while (!feof($handle)) {
        $text[] = fgets($handle);
    }

    fclose($handle);
    return $text;
}

function parseLogLine($line)
{
    // Try to extract timestamp and message from common log formats

    // Apache/PHP error log format: [timestamp] message
    if (preg_match('/^\[(.*?)\]\s+(.+)$/', $line, $matches)) {
        return [
            'timestamp' => formatTimestamp($matches[1]),
            'content' => trim($matches[2]),
            'raw' => $line
        ];
    }

    // Alternative format: timestamp message
    if (preg_match('/^(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})\s+(.+)$/', $line, $matches)) {
        return [
            'timestamp' => $matches[1],
            'content' => trim($matches[2]),
            'raw' => $line
        ];
    }

    // Syslog format: Month Day Time Host Process: Message
    if (preg_match('/^(\w{3}\s+\d{1,2}\s+\d{2}:\d{2}:\d{2})\s+\S+\s+(.+)$/', $line, $matches)) {
        return [
            'timestamp' => date('Y-m-d H:i:s', strtotime($matches[1])),
            'content' => trim($matches[2]),
            'raw' => $line
        ];
    }

    // Fallback: treat entire line as content with current timestamp
    return [
        'timestamp' => date('Y-m-d H:i:s'),
        'content' => $line,
        'raw' => $line
    ];
}

function formatTimestamp($timestamp)
{
    // Try to convert various timestamp formats to standard format
    $formats = [
        'D M d H:i:s Y',           // Apache format: Wed Oct 07 14:30:45 2025
        'Y-m-d H:i:s',             // MySQL format: 2025-10-07 14:30:45
        'd-M-Y H:i:s',             // Alternative: 07-Oct-2025 14:30:45
        'M d H:i:s',               // Syslog: Oct 07 14:30:45
        'c',                       // ISO 8601: 2025-10-07T14:30:45+00:00
    ];

    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, trim($timestamp));
        if ($date !== false) {
            return $date->format('Y-m-d H:i:s');
        }
    }

    // If all else fails, try strtotime
    $time = strtotime($timestamp);
    if ($time !== false) {
        return date('Y-m-d H:i:s', $time);
    }

    // Last resort: return as-is
    return $timestamp;
}

function clearLogs()
{
    $logFiles = findLogFiles();
    $cleared = 0;

    foreach ($logFiles as $logFile) {
        if (is_writable($logFile)) {
            if (file_put_contents($logFile, '') !== false) {
                $cleared++;
            }
        }
    }

    if ($cleared === 0) {
        throw new Exception('No log files could be cleared (check permissions)');
    }

    return $cleared;
}
?>