<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['install_ready'])) {
    header('Location: ?step=welcome');
    exit;
}

$basePath = dirname(__DIR__, 2);
$installPath = dirname(__DIR__);

// 1. Prepare Lock File Details and Project Name
// Parse .env manually since we might not have the full framework loaded
$envFile = $basePath . '/.env';
$appName = 'ThePlugs Framework';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;
        list($name, $value) = explode('=', $line, 2);
        if (trim($name) === 'APP_NAME') {
            $appName = trim($value, '"\'');
            break;
        }
    }
}

$lockData = [
    'installed_at' => date('Y-m-d H:i:s'),
    'app_name' => $appName,
    'env' => 'local',
    'version' => '3.0.0', // You might want to pull this from a version file
    'php_version' => PHP_VERSION,
    'server_os' => PHP_OS,
    'installer_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
];

// 2. Create Lock File
if (file_put_contents($basePath . '/plugs.lock', json_encode($lockData, JSON_PRETTY_PRINT))) {

    // 3. Delete Install Directory
    // Function to recursively delete a directory
    function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    // Attempt to delete the install folder
    // Note: On Windows, this might fail to delete the currently executing script or directory strictly.
    // If we can't delete it, we'll try to rename it as a fallback or just leave it (user wanted deletion).

    // Try to delete everything inside first
    /* 
       We schedule the deletion or attempt it. 
       Since we are running FROM inside the directory, fully deleting it might be tricky on some setups.
       However, usually unlink() works on the file itself in PHP on many configs, 
       but the directory containing the running script might be locked.

       Let's try a best-effort approach.
    */

    // Clear session first
    session_destroy();

    try {
        // We can't easily delete the directory we are currently serving specific files from if the webserver holds a lock.
        // But we will try.
        deleteDirectory($installPath);
    } catch (Exception $e) {
        // Silently fail if we can't delete, or maybe log it.
    }

    // Redirect to public index (constructed carefully)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $publicPath = '/';
    // $publicPath = '/public/index.php';

    // Attempt Renaming
    // Convert App Name to slug-like folder name
    $newFolderName = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $appName)));
    $currentFolderName = basename($basePath);

    $renameSuccess = false;
    // Only rename if it's different and not "framework" or generic if you want constraint
    if ($newFolderName !== strtolower($currentFolderName) && !empty($newFolderName)) {
        $parentDir = dirname($basePath);
        $newPath = $parentDir . '/' . $newFolderName;

        // Try to rename. This often fails on Windows/Apache due to locks
        if (!file_exists($newPath)) {
            if (@rename($basePath, $newPath)) {
                $renameSuccess = true;
                // Since we renamed the folder our script is currently in, 
                // generating the URL relative to the old path might fail or be weird.
                // We need to point to the new path.
                // Typically, localhost/framework/... becomes localhost/newname/...

                // This is a naive replacement assuming standard layout
                $currentUri = $_SERVER['REQUEST_URI'];
                $newUri = str_replace($currentFolderName, $newFolderName, $currentUri);
                // We want to jump to public index
                $redirectUrl = $protocol . $host . '/' . $newFolderName . $publicPath;
                header("Location: $redirectUrl");
                exit;
            }
        }
    }

    header('Location: /');
    exit;
} else {
    die("Failed to create lock file. Please check permissions for $basePath");
}
