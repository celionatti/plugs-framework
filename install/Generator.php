<?php

declare(strict_types=1);

class InstallGenerator
{
    private $basePath;
    private $results = [];

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function createFolders(array $folders): array
    {
        foreach ($folders as $folder) {
            $path = $this->basePath . '/' . ltrim($folder, '/');
            if (!file_exists($path)) {
                if (mkdir($path, 0777, true)) {
                    $this->results[] = "Created directory: $folder";
                } else {
                    $this->results[] = "Failed to create directory: $folder";
                }
            } else {
                $this->results[] = "Directory exists: $folder";
            }
        }
        return $this->results;
    }

    public function createFile(string $path, string $content): bool
    {
        $fullPath = $this->basePath . '/' . ltrim($path, '/');
        $dir = dirname($fullPath);

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        if (file_put_contents($fullPath, $content) !== false) {
            $this->results[] = "Created file: $path";
            return true;
        }

        $this->results[] = "Failed to create file: $path";
        return false;
    }

    public function generateEnv(array $dbConfig): string
    {
        return implode("\n", [
            'APP_NAME="ThePlugs Framework"',
            'APP_ENV=local',
            'APP_KEY=',
            'APP_DEBUG=true',
            'APP_URL=' . (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('/install', '', dirname($_SERVER['SCRIPT_NAME'])),
            '',
            'DB_CONNECTION=mysql',
            "DB_HOST={$dbConfig['host']}",
            'DB_PORT=3306',
            "DB_DATABASE={$dbConfig['name']}",
            "DB_USERNAME={$dbConfig['user']}",
            "DB_PASSWORD=\"{$dbConfig['pass']}\"",
        ]);
    }

    public function generateDatabaseConfig(): string
    {
        return <<<'PHP'
<?php

return [
    'default' => env('DB_CONNECTION', 'mysql'),
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
    ],
];
PHP;
    }

    public function generateThePlug(): string
    {
        return <<<'PHP'
#!/usr/bin/env php
<?php

declare(strict_types=1);

/*
 |----------------------------------------------------------------------
 | Define Constants for the Console
 |----------------------------------------------------------------------
 */

use Plugs\Console\ConsoleKernel;
use Plugs\Console\ConsolePlugs;

define('BASE_PATH', __DIR__ . '/');
define('VENDOR_PATH', BASE_PATH . 'vendor/');

require VENDOR_PATH . 'autoload.php';

$kernel = new ConsoleKernel();
$app = new ConsolePlugs($kernel);
exit($app->run($argv));
PHP;
    }

    public function getResults(): array
    {
        return $this->results;
    }
}
