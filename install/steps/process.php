<?php
session_start();
require_once dirname(__DIR__) . '/Generator.php';

if (!isset($_SESSION['db'])) {
    header('Location: ?step=welcome');
    exit;
}

$basePath = dirname(__DIR__, 2);
$generator = new InstallGenerator($basePath);

// 1. Create Directories (Extend this list as needed)
$directories = [
    'storage/cache',
    'storage/logs',
    'storage/session',
    'storage/views',
    'storage/framework',
    'storage/uploads',
    'bootstrap',
    'config',
    'app',
    'routes',
    'database',
    'database/migrations',
    'resources',
    'resources/views',
    'resources/views/layouts',
    'public'
];

$generator->createFolders($directories);

// 2. Create Files
$generator->createFile('.env', $generator->generateEnv($_SESSION['db']));
$generator->createFile('config/app.php', $generator->generateAppConfig());
$generator->createFile('config/auth.php', $generator->generateAuthConfig());
$generator->createFile('config/database.php', $generator->generateDatabaseConfig());
$generator->createFile('config/hash.php', $generator->generateHashConfig());
$generator->createFile('config/mail.php', $generator->generateMailConfig());
$generator->createFile('config/middleware.php', $generator->generateMiddlewareConfig());
$generator->createFile('config/security.php', $generator->generateSecurityConfig());
$generator->createFile('config/services.php', $generator->generateServicesConfig());
$generator->createFile('theplug', $generator->generateThePlug());

// 5. Setup Database & Admin User
try {
    $db = $_SESSION['db'];
    $dsn = "mysql:host={$db['host']};dbname={$db['name']}";
    $pdo = new PDO($dsn, $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Insert Admin
    if (isset($_SESSION['admin'])) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([
            $_SESSION['admin']['name'],
            $_SESSION['admin']['email'],
            $_SESSION['admin']['password']
        ]);
        $_SESSION['install_results'][] = "Created admin user: {$_SESSION['admin']['email']}";
    }
} catch (PDOException $e) {
    $_SESSION['install_results'][] = "Database setup warning: " . $e->getMessage();
}

// 6. Store results
$_SESSION['install_results'] = array_merge($generator->getResults(), $_SESSION['install_results'] ?? []);

// 4. Mark as ready for activation (but don't lock yet)
$_SESSION['install_ready'] = true;

// Redirect to finish
header('Location: ?step=finish');
exit;
