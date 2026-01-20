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
            '',
            'MAIL_MAILER=smtp',
            'MAIL_HOST=smtp.mailtrap.io',
            'MAIL_PORT=2525',
            'MAIL_USERNAME=your_mail_username',
            'MAIL_PASSWORD=your_mail_password',
            'MAIL_ENCRYPTION=tls',
            'MAIL_FROM_ADDRESS=hello@example.com',
            'MAIL_FROM_NAME="${APP_NAME}"',
        ]);
    }

    public function generateDatabaseConfig(): string
    {
        return <<<'PHP'
        <?php

        declare(strict_types=1);

        /*
        |--------------------------------------------------------------------------
        | Database Configuration File
        |--------------------------------------------------------------------------
        |
        | This file is used to configure the database connections for the application.
        | You can define multiple connections and set the default connection to be used.
        | Environment variables are used to allow different configurations per environment.
        */

        return [
            /*
            |--------------------------------------------------------------------------
            | Default Database Connection
            |--------------------------------------------------------------------------
            |
            | The default database connection that will be used when no specific
            | connection is requested. This should match one of the connections
            | defined in the "connections" array below.
            */
            'default' => env('DB_CONNECTION', 'mysql'),

            /*
            |--------------------------------------------------------------------------
            | Database Connections
            |--------------------------------------------------------------------------
            |
            | Here you can define all of the database connections supported by your
            | application. Each connection is configured with its driver, host,
            | database name, username, password, and additional driver-specific options.
            |
            | Supported drivers: "mysql", "pgsql", "sqlite"
            */
            'connections' => [
                /*
                |--------------------------------------------------------------------------
                | MySQL Database Connection
                |--------------------------------------------------------------------------
                |
                | Configuration for MySQL/MariaDB database connections.
                | Includes PDO options for error handling, character set, and performance.
                */
                'mysql' => [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST', 'localhost'),
                    'port' => env('DB_PORT', 3306),
                    'database' => env('DB_DATABASE', 'trees'),
                    'username' => env('DB_USERNAME', 'root'),
                    'password' => env('DB_PASSWORD', ''),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                    'engine' => null,
                    'options' => [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ],
                    'timeout' => 5,
                    'persistent' => false,
                    'max_idle_time' => 3600, // 1 hour
                ],

                /*
                |--------------------------------------------------------------------------
                | PostgreSQL Database Connection
                |--------------------------------------------------------------------------
                |
                | Configuration for PostgreSQL database connections.
                | Defaults to PostgreSQL's standard port (5432).
                */
                'pgsql' => [
                    'driver' => 'pgsql',
                    'host' => env('DB_HOST', 'localhost'),
                    'port' => env('DB_PORT', 5432),
                    'database' => env('DB_DATABASE', 'trees'),
                    'username' => env('DB_USERNAME', 'postgres'),
                    'password' => env('DB_PASSWORD', ''),
                    'charset' => 'utf8',
                    'prefix' => '',
                    'schema' => 'public',
                    'sslmode' => 'prefer',
                ],

                /*
                |--------------------------------------------------------------------------
                | SQLite Database Connection
                |--------------------------------------------------------------------------
                |
                | Configuration for SQLite database connections.
                | The database file is stored in the storage directory by default.
                */
                'sqlite' => [
                    'driver' => 'sqlite',
                    'database' => env('DB_DATABASE', storage_path('database/database.sqlite')),
                    'prefix' => '',
                    'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
                ],
            ],
        ];
        PHP;
    }

    public function generateAppConfig(): string
    {
        return <<<'PHP'
        <?php

        declare(strict_types=1);

        /*
        |--------------------------------------------------------------------------
        | Application Configuration File
        |--------------------------------------------------------------------------
        |
        | This file is used to configure various settings for the application,
        | such as the application name, environment, debug mode, and paths.
        | Environment variables take precedence over default values.
        */

        return [
            /*
            |--------------------------------------------------------------------------
            | Application Name
            |--------------------------------------------------------------------------
            |
            | The name of your application. This value is used when the framework
            | needs to place the application's name in a notification or any other
            | location as required by the application or its packages.
            */
            'name' => env('APP_NAME', 'Plugs Framework'),

            /*
            |--------------------------------------------------------------------------
            | Application Environment
            |--------------------------------------------------------------------------
            |
            | This value determines the "environment" your application is currently
            | running in. This may determine how you prefer to configure various
            | services your application utilizes. Options: "local", "production", etc.
            */
            'env' => env('APP_ENV', 'production'),

            /*
            |--------------------------------------------------------------------------
            | Application Debug Mode
            |--------------------------------------------------------------------------
            |
            | When your application is in debug mode, detailed error messages with
            | stack traces will be shown. If disabled, a simple generic error page
            | is shown. It's recommended to set this to false in production.
            */
            'debug' => (bool) env('APP_DEBUG', false),

            /*
            |--------------------------------------------------------------------------
            | Application URL
            |--------------------------------------------------------------------------
            |
            | This URL is used by the console to properly generate URLs when using
            | the Artisan command line tool. You should set this to the root of
            | your application so that it is used when running Artisan tasks.
            */
            'url' => env('APP_URL', 'http://localhost'),

            /*
            |--------------------------------------------------------------------------
            | Application Timezone
            |--------------------------------------------------------------------------
            |
            | Here you may specify the default timezone for your application, which
            | will be used by the PHP date and date-time functions. We have gone
            | ahead and set this to a sensible default for you out of the box.
            */
            'timezone' => env('APP_TIMEZONE', 'UTC'),

            /*
            |--------------------------------------------------------------------------
            | Application Encryption Key
            |--------------------------------------------------------------------------
            |
            | This key is used by the Illuminate encrypter service and should be set
            | to a random, 32 character string, otherwise these encrypted strings
            | will not be safe. Please do this before deploying an application!
            */
            'key' => env('APP_KEY', null),

            /*
            |--------------------------------------------------------------------------
            | Application Paths
            |--------------------------------------------------------------------------
            |
            | Here you may specify various paths used by your application for
            | views, caching, logging, and storage. These paths are relative
            | to the base path of your application.
            */
            'paths' => [
                /*
                |--------------------------------------------------------------------------
                | Views Path
                |--------------------------------------------------------------------------
                |
                | The path where your application's view templates are stored.
                */
                'views' => base_path('views'),

                /*
                |--------------------------------------------------------------------------
                | Cache Path
                |--------------------------------------------------------------------------
                |
                | The path where your application's cached files (like compiled views)
                | are stored.
                */
                'cache' => base_path('storage/cache'),

                /*
                |--------------------------------------------------------------------------
                | Logs Path
                |--------------------------------------------------------------------------
                |
                | The path where your application's log files are stored.
                */
                'logs' => base_path('storage/logs'),

                /*
                |--------------------------------------------------------------------------
                | Storage Path
                |--------------------------------------------------------------------------
                |
                | The base path for all storage operations (files, cache, sessions, etc).
                */
                'storage' => base_path('storage'),
            ],

            /*
            |--------------------------------------------------------------------------
            | Required Files
            |--------------------------------------------------------------------------
            |
            | Here you may specify files that should be automatically loaded by
            | the application. These files typically contain helper functions
            | or other bootstrap code that needs to be available globally.
            */
            'required_files' => [
                /*
                |--------------------------------------------------------------------------
                | Functions File
                |--------------------------------------------------------------------------
                |
                | A file containing global helper functions used throughout the application.
                */
                'function' => base_path('utils/function.php')
            ],
        ];
        PHP;
    }

    public function generateAuthConfig(): string
    {
        return <<<'PHP'
        <?php

        declare(strict_types=1);

        /*
        |--------------------------------------------------------------------------
        | Authentication Configuration File
        |--------------------------------------------------------------------------
        |
        | This file contains authentication-related settings for the application,
        | including user model configuration, password hashing, session management,
        | and OAuth provider settings for social authentication.
        */

        return [
            /*
            |--------------------------------------------------------------------------
            | User Model Configuration
            |--------------------------------------------------------------------------
            |
            | Configure the user model and table structure for authentication.
            | Set your custom user model class if you have extended the default.
            */
            'user_model' => null, // Your custom user model class name (e.g., App\Models\User)
            'table' => 'users',
            'primary_key' => 'id',
            'email_column' => 'email',
            'password_column' => 'password',
            'remember_token_column' => null, // Column name for "remember me" tokens, or null to disable
            'last_login_column' => 'last_login_at', // Column to track last login timestamp

            /*
            |--------------------------------------------------------------------------
            | Password Hashing Configuration
            |--------------------------------------------------------------------------
            |
            | Configure the password hashing algorithm and cost factor.
            | PASSWORD_BCRYPT is recommended as it's secure and widely supported.
            | Higher cost values increase security but require more processing time.
            */
            'password_algo' => PASSWORD_BCRYPT,
            'password_cost' => 12,

            /*
            |--------------------------------------------------------------------------
            | Session Configuration
            |--------------------------------------------------------------------------
            |
            | Configure session keys and remember me functionality.
            | These settings control how user authentication state is maintained
            | across requests using session cookies.
            */
            'session_key' => 'auth_user_id',
            'remember_token_name' => 'remember_token',
            'remember_days' => 30,

            /*
            |--------------------------------------------------------------------------
            | OAuth Provider Configuration
            |--------------------------------------------------------------------------
            |
            | Configure your OAuth providers for social authentication.
            | You'll need to create apps with each provider and obtain
            | client credentials from their respective developer portals.
            |
            | Important: Always keep client secrets secure and never commit them
            | to version control. Use environment variables for sensitive data.
            */
            'oauth' => [
                'google' => [
                    'client_id' => env('GOOGLE_CLIENT_ID', ''),
                    'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
                    // Get credentials from: https://console.cloud.google.com/apis/credentials
                ],

                'facebook' => [
                    'client_id' => env('FACEBOOK_CLIENT_ID', ''),
                    'client_secret' => env('FACEBOOK_CLIENT_SECRET', ''),
                    // Get credentials from: https://developers.facebook.com/apps/
                ],

                'github' => [
                    'client_id' => env('GITHUB_CLIENT_ID', ''),
                    'client_secret' => env('GITHUB_CLIENT_SECRET', ''),
                    // Get credentials from: https://github.com/settings/developers
                ],

                'discord' => [
                    'client_id' => env('DISCORD_CLIENT_ID', ''),
                    'client_secret' => env('DISCORD_CLIENT_SECRET', ''),
                    // Get credentials from: https://discord.com/developers/applications
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Database Table Names
            |--------------------------------------------------------------------------
            |
            | Specify the table names for OAuth accounts and remember tokens.
            | These tables will be used to store social authentication data
            | and persistent login sessions.
            */
            'oauth_table' => 'oauth_accounts',
            'remember_tokens_table' => 'remember_tokens',

            /*
            |--------------------------------------------------------------------------
            | Timestamps Configuration
            |--------------------------------------------------------------------------
            |
            | Configure whether to use timestamps and specify column names
            | for created_at and updated_at fields in user-related tables.
            */
            'use_timestamps' => true,
            'created_at_column' => 'created_at',
            'updated_at_column' => 'updated_at',

            /*
            |--------------------------------------------------------------------------
            | Email Verification Configuration
            |--------------------------------------------------------------------------
            |
            | Configure email verification settings for user registration.
            | When enabled, users must verify their email address before
            | being able to fully access the application.
            */
            'email_verification' => [
                'enabled' => false, // Set to true to require email verification
                'token_length' => 6, // Length of verification token (numeric)
                'expiry_hours' => 24, // Hours until verification token expires
                'send_welcome_email' => true, // Send welcome email after verification
            ],
        ];
        PHP;
    }

    public function generateHashConfig(): string
    {
        return <<<'PHP'
        <?php

        declare(strict_types=1);

        /*
        |--------------------------------------------------------------------------
        | Hash Configuration File
        |--------------------------------------------------------------------------
        |
        | This file is used to configure the hashing options for the application.
        | You may specify the default hash driver as well as options for each
        | supported driver. These settings affect password hashing and verification.
        */

        return [
            /*
            |--------------------------------------------------------------------------
            | Default Hash Driver
            |--------------------------------------------------------------------------
            |
            | This option controls the default hash driver that will be used to hash
            | passwords for your application. The driver determines which hashing
            | algorithm will be used when creating new password hashes.
            |
            | Supported drivers: "bcrypt", "argon", "argon2id"
            |
            | Note: argon2id is recommended as it provides the best security
            | against both side-channel and GPU-based attacks.
            */
            'driver' => env('HASH_DRIVER', 'argon2id'),

            /*
            |--------------------------------------------------------------------------
            | Bcrypt Options
            |--------------------------------------------------------------------------
            |
            | Here you may specify the configuration options that should be used when
            | passwords are hashed using the Bcrypt algorithm. The "rounds" option
            | determines the work factor - higher values increase security but also
            | increase hashing time.
            |
            | Recommended: 10-12 for general use, higher for sensitive applications
            | Default: 12 (takes ~0.3 seconds on modern hardware)
            */
            'bcrypt' => [
                'rounds' => (int) env('BCRYPT_ROUNDS', 12),
            ],

            /*
            |--------------------------------------------------------------------------
            | Argon2 Options
            |--------------------------------------------------------------------------
            |
            | Here you may specify the configuration options that should be used when
            | passwords are hashed using the Argon2 algorithm. Argon2 is the winner
            | of the Password Hashing Competition and is recommended for new applications.
            |
            | Options:
            | - memory: Memory cost in kilobytes (default: 65536 = 64MB)
            | - time: Time cost (number of iterations, default: 4)
            | - threads: Degree of parallelism (default: 3)
            |
            | These values balance security and performance. Adjust based on your
            | server's capabilities and security requirements.
            */
            'argon' => [
                'memory' => (int) env('ARGON_MEMORY', 65536),
                'time' => (int) env('ARGON_TIME', 4),
                'threads' => (int) env('ARGON_THREADS', 3),
            ],

            /*
            |--------------------------------------------------------------------------
            | Argon2id Options
            |--------------------------------------------------------------------------
            |
            | Argon2id is a hybrid version that combines Argon2i (immune to
            | side-channel attacks) and Argon2d (immune to GPU cracking attacks).
            | It is the recommended algorithm for password hashing as it provides
            | protection against both types of attacks.
            |
            | The default values are tuned for reasonable security on modern hardware.
            | You can increase these values for higher security at the cost of
            | slower password verification.
            */
            'argon2id' => [
                'memory' => (int) env('ARGON2ID_MEMORY', 65536),
                'time' => (int) env('ARGON2ID_TIME', 4),
                'threads' => (int) env('ARGON2ID_THREADS', 3),
            ],

            /*
            |--------------------------------------------------------------------------
            | Verification Options
            |--------------------------------------------------------------------------
            |
            | Configure how password verification behaves. The auto-rehash feature
            | automatically updates password hashes when a user logs in if the
            | current hash uses outdated or weaker parameters.
            |
            | This is useful for migrating to stronger algorithms without forcing
            | all users to reset their passwords at once.
            */
            'verify' => [
                'auto_rehash' => (bool) env('HASH_AUTO_REHASH', false),
            ],

            /*
            |--------------------------------------------------------------------------
            | Algorithm Recommendations
            |--------------------------------------------------------------------------
            |
            | For most applications, argon2id with default settings is recommended.
            | For legacy systems or compatibility reasons, bcrypt with 12 rounds
            | is also secure.
            |
            | Minimum recommendations:
            | - Bcrypt: 10+ rounds
            | - Argon2: 64MB memory, 3 iterations, 3 threads
            | - Argon2id: Same as Argon2 (preferred over plain Argon2)
            */
        ];
        PHP;
    }

    public function generateMailConfig(): string
    {
        return <<<'PHP'
        <?php

        declare(strict_types=1);

        /*
        |--------------------------------------------------------------------------
        | Mail Configuration File
        |--------------------------------------------------------------------------
        |
        | This file is used to configure the mail service for your application.
        | It supports various mail drivers including SMTP, Mailgun, SendGrid, and more.
        | Environment variables are used to keep sensitive credentials secure.
        */

        return [
            /*
            |--------------------------------------------------------------------------
            | Default Mail Driver
            |--------------------------------------------------------------------------
            |
            | This option controls the default mail driver that will be used to send
            | emails from your application. Supported drivers include:
            |
            | - "smtp": Simple Mail Transfer Protocol (most common)
            | - "sendmail": Sendmail program
            | - "mailgun": Mailgun API
            | - "ses": Amazon Simple Email Service
            | - "postmark": Postmark API
            | - "log": Write emails to log files (for development)
            | - "array": Store emails in memory (for testing)
            |
            | For development, consider using "log" or "array" to prevent actual email sending.
            */
            'driver' => env('MAIL_DRIVER', 'smtp'),

            /*
            |--------------------------------------------------------------------------
            | SMTP Host Address
            |--------------------------------------------------------------------------
            |
            | The SMTP server hostname or IP address for sending mail.
            | Common examples:
            | - Gmail: smtp.gmail.com
            | - Outlook: smtp.office365.com
            | - Mailtrap: sandbox.smtp.mailtrap.io (for testing)
            | - Custom SMTP: your-smtp-server.com
            */
            'host' => env('MAIL_HOST', 'sandbox.smtp.mailtrap.io'),

            /*
            |--------------------------------------------------------------------------
            | SMTP Host Port
            |--------------------------------------------------------------------------
            |
            | The port number for the SMTP server. Common ports:
            | - 25: Non-encrypted SMTP (not recommended)
            | - 465: SSL encrypted SMTP
            | - 587: TLS encrypted SMTP (recommended)
            | - 2525: Alternative port for Mailtrap and some providers
            */
            'port' => (int) env('MAIL_PORT', 2525),

            /*
            |--------------------------------------------------------------------------
            | SMTP Authentication Username
            |--------------------------------------------------------------------------
            |
            | The username for authenticating with the SMTP server.
            | For services like Gmail, this is typically your email address.
            | Leave empty if your SMTP server doesn't require authentication.
            */
            'username' => env('MAIL_USERNAME', ''),

            /*
            |--------------------------------------------------------------------------
            | SMTP Authentication Password
            |--------------------------------------------------------------------------
            |
            | The password for authenticating with the SMTP server.
            | For Gmail, you may need to use an "App Password" if you have
            | 2-factor authentication enabled on your account.
            |
            | Security Note: Never commit actual passwords to version control.
            | Always use environment variables for sensitive data.
            */
            'password' => env('MAIL_PASSWORD', ''),

            /*
            |--------------------------------------------------------------------------
            | SMTP Encryption Protocol
            |--------------------------------------------------------------------------
            |
            | The encryption protocol to use for the SMTP connection.
            | Options:
            | - "tls": Transport Layer Security (recommended)
            | - "ssl": Secure Sockets Layer
            | - "": No encryption (not recommended for production)
            |
            | Note: Use "tls" for port 587 and "ssl" for port 465.
            */
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),

            /*
            |--------------------------------------------------------------------------
            | Global "From" Address
            |--------------------------------------------------------------------------
            |
            | You may wish for all emails sent by your application to be sent from
            | the same address. Here, you may specify a name and address that is
            | used globally for all emails that are sent by your application.
            |
            | Important: Use a valid email address that you have access to,
            | as this affects deliverability and reply-to functionality.
            */
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
                'name' => env('MAIL_FROM_NAME', 'My Application'),
            ],

            /*
            |--------------------------------------------------------------------------
            | Additional Configuration Options
            |--------------------------------------------------------------------------
            |
            | Some SMTP servers may require additional options. You can add them
            | to the 'options' array below if needed for your specific setup.
            |
            | Common options:
            | - 'verify_peer' => false (for self-signed certificates)
            | - 'allow_self_signed' => true (for development environments)
            */
            'options' => [
                // Add any additional SMTP options here
            ],

            /*
            |--------------------------------------------------------------------------
            | Development Recommendations
            |--------------------------------------------------------------------------
            |
            | For local development:
            | 1. Use Mailtrap (sandbox.smtp.mailtrap.io) to test email sending
            | 2. Set MAIL_DRIVER to "log" to write emails to storage/logs/mail.log
            | 3. Set MAIL_DRIVER to "array" to collect emails in memory for testing
            |
            | For production:
            | 1. Use a reliable email service (SendGrid, Mailgun, Amazon SES, etc.)
            | 2. Always use TLS encryption (MAIL_ENCRYPTION="tls")
            | 3. Verify your domain with your email provider for better deliverability
            */
        ];
        PHP;
    }

    public function generateMiddlewareConfig(): string
    {
        return <<<'PHP'
        <?php

        declare(strict_types=1);

        /*
        |--------------------------------------------------------------------------
        | Middleware Configuration File
        |--------------------------------------------------------------------------
        |
        | This file is used to configure middleware aliases and global middleware
        | for your application. Middleware provides a convenient mechanism for
        | filtering HTTP requests entering your application.
        */

        return [
            /*
            |--------------------------------------------------------------------------
            | Middleware Aliases
            |--------------------------------------------------------------------------
            |
            | Here you can define aliases for your middleware classes.
            | This allows you to use short names like 'auth' instead of the
            | full class name when applying middleware to routes or controllers.
            |
            | Usage in routes:
            | Route::get('/profile', [ProfileController::class, 'show'])->middleware('auth');
            |
            | Usage in controllers:
            | $this->middleware('auth')->only(['index', 'show']);
            */
            'aliases' => [
                // 'auth' => App\Middlewares\AuthMiddleware::class,
                // 'guest' => App\Middlewares\GuestMiddleware::class,
                // 'admin' => App\Middlewares\AdminMiddleware::class,
                // 'verified' => App\Middlewares\VerifiedMiddleware::class,
                // 'throttle' => App\Middlewares\ThrottleRequestsMiddleware::class,
                // 'cors' => App\Middlewares\CorsMiddleware::class,
                // 'json' => App\Middlewares\JsonResponseMiddleware::class,
                // 'cache' => App\Middlewares\CacheMiddleware::class,
            ],
        ];
        PHP;
    }

    public function generateSecurityConfig(): string
    {
        return <<<'PHP'
        <?php

        declare(strict_types=1);

        /*
        |--------------------------------------------------------------------------
        | Security Configuration File
        |--------------------------------------------------------------------------
        |
        | This file contains security-related settings for the application,
        | including CSRF protection, security headers, content security policy,
        | rate limiting, CORS, session security, and advanced threat protection.
        |
        | WARNING: These settings directly affect your application's security.
        | Review and test thoroughly before deploying to production.
        */

        return [
            /*
            |--------------------------------------------------------------------------
            | CSRF Protection Configuration
            |--------------------------------------------------------------------------
            |
            | Cross-Site Request Forgery (CSRF) protection prevents unauthorized
            | commands from being transmitted from a user that the web application
            | trusts. This is essential for protecting state-changing operations.
            */
            'csrf' => [
                // Enable or disable CSRF protection globally
                'enabled' => env('CSRF_ENABLED', true),

                /*
                |--------------------------------------------------------------------------
                | Excluded Routes
                |--------------------------------------------------------------------------
                |
                | Regular expressions that match routes which should be excluded
                | from CSRF protection. Typically used for APIs, webhooks, and
                | public endpoints that don't require session-based authentication.
                */
                'except' => [
                    '#^/api/#',          // All API routes
                    '#^/webhook/#',      // Webhook endpoints
                    '#^/public/upload$#', // Public file upload endpoint
                ],

                /*
                |--------------------------------------------------------------------------
                | Token Handling
                |--------------------------------------------------------------------------
                |
                | Control how CSRF tokens are managed and validated.
                */
                'add_token_to_request' => true,     // Add token to request attributes
                'consume_request_tokens' => true,   // Use one-time tokens (more secure)
                'log_failures' => true,             // Log CSRF validation failures

                /*
                |--------------------------------------------------------------------------
                | Custom Error Handler
                |--------------------------------------------------------------------------
                |
                | Define a custom response when CSRF validation fails.
                | This function receives the request and should return a response.
                */
                'error_handler' => function ($request) {
                    return response()->json([
                        'error' => 'Invalid security token',
                        'message' => 'The form submission has expired. Please refresh and try again.'
                    ], 419); // 419: Authentication Timeout
                },

                /*
                |--------------------------------------------------------------------------
                | CSRF Token Configuration
                |--------------------------------------------------------------------------
                |
                | Advanced configuration for CSRF token generation and validation.
                */
                'csrf_config' => [
                    'token_lifetime' => 3600,       // Token validity in seconds (1 hour)
                    'use_per_request_tokens' => true, // Generate unique tokens per request
                    'strict_mode' => true,          // Strict validation (recommended)
                ]
            ],

            /*
            |--------------------------------------------------------------------------
            | Security Headers Configuration
            |--------------------------------------------------------------------------
            |
            | HTTP security headers that help protect against various attacks
            | like clickjacking, MIME sniffing, and cross-site scripting.
            |
            | Note: Some headers may need adjustment based on your application's needs.
            */
            'headers' => [
                // Prevent page from being displayed in an iframe
                'X-Frame-Options' => 'SAMEORIGIN',

                // Prevent browser from guessing MIME types
                'X-Content-Type-Options' => 'nosniff',

                // Enable XSS protection in older browsers
                'X-XSS-Protection' => '1; mode=block',

                // Control how much referrer information is sent
                'Referrer-Policy' => 'strict-origin-when-cross-origin',

                // Control which browser features can be used
                'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',

                // Additional recommended headers (optional):
                // 'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
                // 'Content-Security-Policy' => "default-src 'self'",
            ],

            /*
            |--------------------------------------------------------------------------
            | Content Security Policy (CSP)
            |--------------------------------------------------------------------------
            |
            | CSP helps prevent XSS attacks by specifying which dynamic resources
            | are allowed to load. It's a powerful security feature but requires
            | careful configuration to avoid breaking your application.
            |
            | IMPORTANT: Test thoroughly when enabling CSP in production.
            */
            'csp' => [
                'enabled' => env('CSP_ENABLED', false),

                // Default sources for all content types
                'default-src' => ["'self'"],

                // Allowed sources for JavaScript
                'script-src' => ["'self'", "'unsafe-inline'", 'cdn.tailwindcss.com'],

                // Allowed sources for CSS
                'style-src' => ["'self'", "'unsafe-inline'", 'cdn.jsdelivr.net'],

                // Allowed sources for images
                'img-src' => ["'self'", 'data:', 'https:'],

                // Allowed sources for fonts
                'font-src' => ["'self'", 'data:'],

                // Additional CSP directives (uncomment as needed):
                // 'connect-src' => ["'self'", 'api.example.com'],
                // 'media-src' => ["'self'"],
                // 'object-src' => ["'none'"], // Prevents Flash, Java, etc.
                // 'frame-ancestors' => ["'none'"], // Prevents framing
                // 'base-uri' => ["'self'"], // Restricts base tag URLs
                // 'form-action' => ["'self'"], // Restricts form submissions
            ],

            /*
            |--------------------------------------------------------------------------
            | Rate Limiting Configuration
            |--------------------------------------------------------------------------
            |
            | Protect against brute force attacks and API abuse by limiting
            | the number of requests from a single source within a time period.
            */
            'rate_limit' => [
                'enabled' => env('RATE_LIMIT_ENABLED', false), // Disabled in favor of Security Shield
                'max_requests' => 60,    // Maximum requests allowed
                'per_minutes' => 1,      // Time window in minutes
            ],

            /*
            |--------------------------------------------------------------------------
            | CORS (Cross-Origin Resource Sharing) Configuration
            |--------------------------------------------------------------------------
            |
            | Configure which origins are allowed to access your API resources.
            | This is essential for single-page applications and mobile apps
            | that need to make cross-origin requests.
            */
            'cors' => [
                'enabled' => env('CORS_ENABLED', false),
                'allowed_origins' => ['*'], // Use specific domains in production
                'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
                'max_age' => 86400, // How long preflight requests can be cached (in seconds)
            ],

            /*
            |--------------------------------------------------------------------------
            | Session Security Configuration
            |--------------------------------------------------------------------------
            |
            | Secure session handling to prevent session hijacking and fixation attacks.
            | These settings control how session cookies are created and managed.
            */
            'session' => [
                'secure' => env('SESSION_SECURE', false), // Set to true in production with HTTPS
                'httponly' => true,    // Prevent JavaScript access to session cookie
                'samesite' => 'Lax',   // Controls cross-site cookie sending
                'lifetime' => 120,     // Session lifetime in minutes
            ],

            /*
            |--------------------------------------------------------------------------
            | Security Shield - Advanced Threat Protection
            |--------------------------------------------------------------------------
            |
            | Comprehensive security module that combines multiple protection layers
            | including rate limiting, bot detection, behavioral analysis, and more.
            | This provides enterprise-grade security for your application.
            */
            'security_shield' => [
                'enabled' => env('SECURITY_SHIELD_ENABLED', true),

                /*
                |--------------------------------------------------------------------------
                | Whitelisted IPs
                |--------------------------------------------------------------------------
                |
                | IP addresses that bypass all security checks.
                | Useful for trusted networks, monitoring systems, and development.
                */
                'whitelisted_ips' => ['127.0.0.1', '::1'],

                /*
                |--------------------------------------------------------------------------
                | Rate Limiting Configuration
                |--------------------------------------------------------------------------
                |
                | Granular rate limiting for different types of requests.
                | All values are in seconds unless otherwise specified.
                */
                'config' => [
                    'rate_limits' => [
                        'login_attempts' => 5,          // Max login attempts before blocking
                        'login_window' => 900,          // Time window for login attempts (15 minutes)
                        'ip_daily_limit' => 100,        // Max requests per IP per day
                        'user_daily_limit' => 50,       // Max requests per user per day
                        'endpoint_limit' => 20,         // Max requests per endpoint per minute
                    ],

                    /*
                    |--------------------------------------------------------------------------
                    | Bot Detection
                    |--------------------------------------------------------------------------
                    |
                    | Detect and block automated bots, crawlers, and scrapers.
                    | Checks user-agent strings and request patterns.
                    */
                    'bot_detection' => [
                        'suspicious_headers' => [
                            'bot', 'crawler', 'spider', 'scraper',
                            'curl', 'wget', 'python-requests',
                            'http-client', 'java/', 'go-http-client'
                        ],
                        'block_suspicious_bots' => true, // Auto-block detected bots
                    ],
                ],

                /*
                |--------------------------------------------------------------------------
                | Security Rules
                |--------------------------------------------------------------------------
                |
                | Enable or disable specific security features.
                | Turn off features you don't need to reduce overhead.
                */
                'rules' => [
                    'rate_limit' => true,       // Enable rate limiting checks
                    'bot_detection' => true,    // Enable bot detection
                    'email' => true,            // Validate email addresses
                    'behavior' => true,         // Analyze user behavior patterns
                    'fingerprint' => false,     // Device fingerprinting (increases security but may affect UX)
                ],

                /*
                |--------------------------------------------------------------------------
                | Trusted IP Whitelist
                |--------------------------------------------------------------------------
                |
                | IP addresses that completely bypass Security Shield.
                | Add your office IPs, VPN endpoints, or monitoring services here.
                */
                'whitelist' => [
                    '127.0.0.1',    // Localhost
                    '::1',          // IPv6 localhost
                    // Add production whitelist IPs:
                    // '192.168.1.0/24', // Office network
                    // '10.0.0.0/8',     // Internal network
                ],

                /*
                |--------------------------------------------------------------------------
                | Risk Thresholds
                |--------------------------------------------------------------------------
                |
                | Define risk scores that trigger different security actions.
                | Risk scores range from 0.0 (no risk) to 1.0 (maximum risk).
                */
                'thresholds' => [
                    'deny' => 0.85,             // Auto-deny request if risk score exceeds this
                    'challenge_high' => 0.70,   // Require MFA/2FA if risk score exceeds this
                    'challenge_low' => 0.50,    // Require CAPTCHA if risk score exceeds this
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Security Profiler
            |--------------------------------------------------------------------------
            |
            | Collect and analyze security-related data for monitoring and debugging.
            | This helps identify attack patterns and improve security over time.
            */
            'profiler' => [
                'enabled' => env('SECURITY_PROFILER_ENABLED', true),
            ],

            /*
            |--------------------------------------------------------------------------
            | Production Security Checklist
            |--------------------------------------------------------------------------
            |
            | Before deploying to production, ensure:
            | 1. CSRF protection is enabled
            | 2. Security headers are properly configured
            | 3. Rate limiting is enabled and tuned for your traffic
            | 4. Session secure flag is true (if using HTTPS)
            | 5. CORS is configured with specific origins, not '*'
            | 6. All environment-specific IPs are whitelisted
            | 7. Security Shield rules are optimized for your use case
            | 8. Error logging is enabled for security events
            */
        ];
        PHP;
    }

    public function generateServicesConfig(): string
    {
        return <<<'PHP'
        <?php

        declare(strict_types=1);

        /*
        |--------------------------------------------------------------------------
        | Services Configuration File
        |--------------------------------------------------------------------------
        |
        | This file is used to define and bind services into the application's
        | service container. Services are registered as singletons or instances
        | and can be resolved from the container throughout your application.
        |
        | The service container provides dependency injection and service location
        | capabilities, making your application more testable and maintainable.
        */

        use Plugs\Router\Router;
        use Plugs\View\ViewEngine;
        use Plugs\Container\Container;
        use Plugs\Database\Connection;
        use Plugs\Mail\MailService;
        use Plugs\Auth\AuthManager;
        use Plugs\Cache\CacheManager;
        use Plugs\Session\SessionManager;

        return function (Container $container) {
            /*
            |--------------------------------------------------------------------------
            | View Engine Service
            |--------------------------------------------------------------------------
            |
            | Register the template engine as a singleton service.
            | The view engine compiles and renders template files, with optional
            | caching for better performance in production environments.
            |
            | Configuration:
            | - views_path: Directory containing template files
            | - cache_path: Directory for compiled templates (auto-created)
            | - cache_enabled: Enable template caching in production
            */
            $container->singleton(ViewEngine::class, function ($container) {
                // Define paths for views and cache
                $viewPath = base_path('resources/views');
                $cachePath = base_path('storage/framework/views');

                // Ensure cache directory exists with proper permissions
                if (!is_dir($cachePath)) {
                    mkdir($cachePath, 0755, true);
                }

                // Create and configure the view engine instance
                return new ViewEngine(
                    $viewPath,
                    $cachePath,
                    !config('app.debug', false) // Enable caching when not in debug mode
                );
            });

            /*
            |--------------------------------------------------------------------------
            | Database Connection Service
            |--------------------------------------------------------------------------
            |
            | Register the database connection as a singleton.
            | This ensures a single database connection is reused throughout
            | the application lifecycle, improving performance and consistency.
            |
            | The connection is configured using settings from the database
            | configuration file and supports multiple database drivers.
            */
            $container->singleton(Connection::class, function ($container) {
                // Get database configuration from config files
                $defaultConnection = config('database.default', 'mysql');
                $dbConfig = config("database.connections.{$defaultConnection}", []);

                // Validate configuration exists
                if (empty($dbConfig)) {
                    throw new RuntimeException(
                        "Database configuration for '{$defaultConnection}' not found."
                    );
                }

                // Get or create the database connection instance
                return Connection::getInstance($dbConfig);
            });

            /*
            |--------------------------------------------------------------------------
            | Router Service
            |--------------------------------------------------------------------------
            |
            | Register the router as a singleton.
            | The router handles HTTP request routing, middleware execution,
            | and controller resolution. It's the central component for
            | defining and processing application routes.
            */
            $container->singleton(Router::class, function ($container) {
                $router = new Router();

                // Optional: Set custom route patterns or global middleware
                // $router->pattern('id', '[0-9]+');
                // $router->middleware('web');

                return $router;
            });

            /*
            |--------------------------------------------------------------------------
            | Mail Service
            |--------------------------------------------------------------------------
            |
            | Register the mail service as a singleton.
            | This service provides email sending capabilities using the
            | configuration from the mail configuration file.
            |
            | Supports multiple mail drivers: SMTP, Mailgun, SendGrid, etc.
            */
            $container->singleton('mail', function ($container) {
                $config = config('mail', []);

                // Validate required mail configuration
                if (empty($config)) {
                    throw new RuntimeException('Mail configuration not found.');
                }

                return new MailService($config);
            });

            /*
            |--------------------------------------------------------------------------
            | Authentication Service
            |--------------------------------------------------------------------------
            |
            | Register the authentication manager as a singleton.
            | This service handles user authentication, session management,
            | and authorization checks throughout the application.
            |
            | Optional: Uncomment to enable authentication services
            */
            // $container->singleton(AuthManager::class, function ($container) {
            //     $config = config('auth', []);
            //     return new AuthManager($container, $config);
            // });

            /*
            |--------------------------------------------------------------------------
            | Cache Service
            |--------------------------------------------------------------------------
            |
            | Register the cache manager as a singleton.
            | This service provides a unified interface for various cache drivers:
            | file, redis, memcached, etc., with configuration from cache config.
            |
            | Optional: Uncomment to enable caching services
            */
            // $container->singleton(CacheManager::class, function ($container) {
            //     $config = config('cache', []);
            //     return new CacheManager($config);
            // });

            /*
            |--------------------------------------------------------------------------
            | Session Service
            |--------------------------------------------------------------------------
            |
            | Register the session manager as a singleton.
            | This service handles HTTP session management with support for
            | multiple session drivers (file, database, redis, etc.).
            |
            | Optional: Uncomment to enable session services
            */
            // $container->singleton(SessionManager::class, function ($container) {
            //     $config = config('session', []);
            //     return new SessionManager($config);
            // });

            /*
            |--------------------------------------------------------------------------
            | Custom Services
            |--------------------------------------------------------------------------
            |
            | Register your own application services here.
            | Example: Payment gateway, API client, or custom business logic service.
            */
            // $container->singleton(PaymentGateway::class, function ($container) {
            //     $config = config('services.payments', []);
            //     return new PaymentGateway($config);
            // });

            // $container->singleton(ApiClient::class, function ($container) {
            //     $config = config('services.api', []);
            //     return new ApiClient($config);
            // });

            /*
            |--------------------------------------------------------------------------
            | Service Aliases
            |--------------------------------------------------------------------------
            |
            | Define aliases for easier service resolution.
            | Aliases allow you to resolve services using shorter, more convenient
            | names instead of the full class name.
            |
            | Usage:
            | $view = $container->get('view');
            | $db = $container->get('db');
            */
            $container->alias(ViewEngine::class, 'view');
            $container->alias(Connection::class, 'db');
            $container->alias(Router::class, 'router');
            $container->alias(MailService::class, 'mail');

            // Optional aliases for additional services
            // $container->alias(AuthManager::class, 'auth');
            // $container->alias(CacheManager::class, 'cache');
            // $container->alias(SessionManager::class, 'session');

            /*
            |--------------------------------------------------------------------------
            | Service Provider Registration
            |--------------------------------------------------------------------------
            |
            | For larger applications, consider organizing services into
            | Service Provider classes. Each provider registers a set of
            | related services.
            |
            | Example:
            | $container->register(App\Providers\AppServiceProvider::class);
            | $container->register(App\Providers\AuthServiceProvider::class);
            | $container->register(App\Providers\RouteServiceProvider::class);
            */

            /*
            |--------------------------------------------------------------------------
            | Service Resolution Events
            |--------------------------------------------------------------------------
            |
            | You can attach event listeners to services when they are resolved
            | from the container. This is useful for initialization logic that
            | should run after the service is instantiated.
            |
            | Example:
            | $container->resolving(ViewEngine::class, function ($view, $container) {
            |     // Add global variables to all views
            |     $view->share('app_name', config('app.name'));
            | });
            */
        };
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

    public function generateBootstrapBoot(): string
    {
        return <<<'PHP'
        <?php

        declare(strict_types=1);

        /*
        |--------------------------------------------------------------------------
        | Application Bootstrap File
        |--------------------------------------------------------------------------
        |
        | This file is responsible for bootstrapping your application. It loads
        | essential dependencies, configures services, sets up middleware,
        | and prepares the application for handling HTTP requests.
        |
        | This is the core initialization file that runs on every request.
        */

        use Plugs\Plugs;
        use Plugs\Router\Router;
        use Plugs\Container\Container;
        use Plugs\Base\Model\PlugModel;
        use Plugs\Session\SessionManager;
        use Plugs\Http\Message\ServerRequest;
        use Plugs\Facades\Route;
        use Plugs\Http\ResponseFactory;

        /*
        |--------------------------------------------------------------------------
        | Define Application Constants
        |--------------------------------------------------------------------------
        |
        | Define core directory paths used throughout the application.
        | These constants provide consistent path references across the codebase.
        */
        define('BASE_PATH', dirname(__DIR__) . '/');
        define('APP_PATH', BASE_PATH . 'app/');
        define('CONFIG_PATH', BASE_PATH . 'config/');
        define('PUBLIC_PATH', BASE_PATH . 'public/');
        define('STORAGE_PATH', BASE_PATH . 'storage/');
        define('RESOURCES_PATH', BASE_PATH . 'resources/');
        define('VENDOR_PATH', BASE_PATH . 'vendor/');
        define('ROUTES_PATH', BASE_PATH . 'routes/');

        /*
        |--------------------------------------------------------------------------
        | Autoload Dependencies
        |--------------------------------------------------------------------------
        |
        | Load the Composer autoloader to manage PHP dependencies and class loading.
        | This enables automatic loading of classes from installed packages and
        | your application's source code.
        */
        require VENDOR_PATH . 'autoload.php';

        /*
        |--------------------------------------------------------------------------
        | Load Environment Variables
        |--------------------------------------------------------------------------
        |
        | Load environment-specific configuration from the .env file.
        | This allows you to configure the application differently for
        | development, testing, and production environments.
        |
        | Note: The .env file should never be committed to version control
        | in production environments.
        */
        $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
        try {
            $dotenv->load();
        } catch (Exception $e) {
            // Silently fail if .env file doesn't exist (might be using server env vars)
            // Optionally log this: error_log('Environment file not found: ' . $e->getMessage());
        }

        /*
        |--------------------------------------------------------------------------
        | Error Handling Configuration
        |--------------------------------------------------------------------------
        |
        | Configure error reporting based on the application environment.
        | In development, show all errors for debugging. In production,
        | log errors but don't display them to users.
        */
        if (env('APP_DEBUG', false)) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
            ini_set('display_startup_errors', '0');
        }

        // Set timezone from configuration
        date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

        /*
        |--------------------------------------------------------------------------
        | Initialize Application Container
        |--------------------------------------------------------------------------
        |
        | Create and configure the dependency injection container.
        | The container manages class dependencies and service resolution
        | throughout the application lifecycle.
        */
        $container = Container::getInstance();

        /*
        |--------------------------------------------------------------------------
        | Create Application Instance
        |--------------------------------------------------------------------------
        |
        | Initialize the main application instance.
        | This creates the middleware pipeline and request/response flow.
        */
        $app = new Plugs();

        /*
        |--------------------------------------------------------------------------
        | Register Configuration Files
        |--------------------------------------------------------------------------
        |
        | Load application configuration from config files.
        | Configuration is loaded early so it's available to all services.
        */
        $appConfig = config('app', []);
        $databaseConfig = config('database', []);
        $securityConfig = config('security', []);
        $sessionConfig = config('security.session', []);

        /*
        |--------------------------------------------------------------------------
        | Database Connection Setup
        |--------------------------------------------------------------------------
        |
        | Configure the database connection for models.
        | This sets up the default database connection that will be used
        | by all Eloquent models and database queries.
        */
        if (!empty($databaseConfig) && isset($databaseConfig['default'])) {
            $defaultConnection = $databaseConfig['default'];
            $connectionConfig = $databaseConfig['connections'][$defaultConnection] ?? [];
            
            if (!empty($connectionConfig)) {
                PlugModel::setConnection($connectionConfig);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Session Management
        |--------------------------------------------------------------------------
        |
        | Initialize and start the session manager with configuration from
        | the security settings. Sessions are essential for maintaining
        | user state across requests.
        */
        if (!empty($sessionConfig)) {
            $sessionManager = new SessionManager($sessionConfig);
            $sessionManager->start();
            
            // Register session manager in container for dependency injection
            $container->singleton(SessionManager::class, function () use ($sessionManager) {
                return $sessionManager;
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Load Service Providers
        |--------------------------------------------------------------------------
        |
        | Execute the services configuration file to register all application
        | services in the container. This includes view engines, database
        | connections, mail services, and custom application services.
        */
        if (function_exists('config')) {
            $serviceLoader = config('services');
            if (is_callable($serviceLoader)) {
                $serviceLoader($container);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Security Middleware Setup
        |--------------------------------------------------------------------------
        |
        | Configure security middleware based on the security configuration.
        | These middleware layers protect the application from common
        | web vulnerabilities and attacks.
        */

        // Add SPA detection middleware (for Single Page Applications)
        $app->pipe(new \Plugs\Http\Middleware\SPAMiddleware());

        // Add security headers middleware
        if (!empty($securityConfig['headers'])) {
            $app->pipe(new \Plugs\Http\Middleware\SecurityHeadersMiddleware($securityConfig['headers']));
        }

        // Add CORS middleware (if enabled)
        if (($securityConfig['cors']['enabled'] ?? false) === true) {
            $corsConfig = $securityConfig['cors'];
            $app->pipe(new \Plugs\Http\Middleware\CorsMiddleware(
                $corsConfig['allowed_origins'] ?? ['*'],
                $corsConfig['allowed_methods'] ?? ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                $corsConfig['allowed_headers'] ?? ['Content-Type', 'Authorization', 'X-Requested-With'],
                $corsConfig['max_age'] ?? 86400
            ));
        }

        // Add SecurityShield Middleware (Advanced Protection)
        if (($securityConfig['security_shield']['enabled'] ?? false) === true) {
            $shieldConfig = $securityConfig['security_shield'];
            
            // Create SecurityShield instance with configuration
            $securityShield = new \Plugs\Middlewares\SecurityShieldMiddleware($shieldConfig['config'] ?? []);
            
            // Configure rules (enable/disable specific checks)
            if (!empty($shieldConfig['rules'])) {
                foreach ($shieldConfig['rules'] as $rule => $enabled) {
                    if ($enabled) {
                        $securityShield->enableRule($rule);
                    } else {
                        $securityShield->disableRule($rule);
                    }
                }
            }
            
            // Add whitelisted IPs
            if (!empty($shieldConfig['whitelist'])) {
                foreach ($shieldConfig['whitelist'] as $ip) {
                    $securityShield->addToWhitelist($ip);
                }
            }
            
            // Add to middleware pipeline
            $app->pipe($securityShield);
        }

        // Add rate limiting middleware (if enabled and SecurityShield is not handling it)
        if (($securityConfig['rate_limit']['enabled'] ?? false) === true && 
            ($securityConfig['security_shield']['enabled'] ?? false) !== true) {
            $rateLimitConfig = $securityConfig['rate_limit'];
            $app->pipe(new \Plugs\Http\Middleware\RateLimitMiddleware(
                $rateLimitConfig['max_requests'] ?? 60,
                $rateLimitConfig['per_minutes'] ?? 1
            ));
        }

        // Add CSRF protection middleware (if enabled)
        if (($securityConfig['csrf']['enabled'] ?? false) === true) {
            $app->pipe(new \Plugs\Http\Middleware\CsrfMiddleware($securityConfig['csrf']));
        }

        // Add Profiler Middleware (for development and debugging)
        if (($securityConfig['profiler']['enabled'] ?? false) === true) {
            $app->pipe(new \Plugs\Http\Middleware\ProfilerMiddleware());
        }

        /*
        |--------------------------------------------------------------------------
        | Router Configuration
        |--------------------------------------------------------------------------
        |
        | Initialize the router and register it in the container.
        | The router handles URL routing and controller dispatching.
        */
        $router = new Router();

        // Register router as singleton in container
        $container->singleton('router', function () use ($router) {
            return $router;
        });

        $container->singleton(Router::class, function () use ($router) {
            return $router;
        });

        // Set router instance in Route facade for static access
        Route::setFacadeInstance('router', $router);

        /*
        |--------------------------------------------------------------------------
        | Request Configuration
        |--------------------------------------------------------------------------
        |
        | Create the PSR-7 request from PHP globals and configure trusted
        | proxies and hosts for applications behind load balancers or CDNs.
        */
        $request = ServerRequest::fromGlobals();

        // Configure trusted proxies (if behind load balancer/CDN)
        // Update these with your actual proxy IPs or CIDR ranges
        ServerRequest::setTrustedProxies([
            '10.0.0.0/8',      // Private network
            '172.16.0.0/12',   // Private network
            '192.168.0.0/16',  // Private network
            // Add your load balancer IPs here:
            // '203.0.113.1',
            // '198.51.100.1',
        ]);

        // Configure trusted hosts (for host header validation)
        // Add your application's domain names here
        ServerRequest::setTrustedHosts([
            // 'example.com',
            // 'www.example.com',
            // '*.example.com', // Wildcard for subdomains
        ]);

        // Register request as singleton in container
        $container->singleton(\Psr\Http\Message\ServerRequestInterface::class, function () use ($request) {
            return $request;
        });

        /*
        |--------------------------------------------------------------------------
        | Route Loading
        |--------------------------------------------------------------------------
        |
        | Load application route definitions from route files.
        | Routes define the URL patterns and their corresponding controllers.
        */

        // Load web routes
        if (file_exists(ROUTES_PATH . 'web.php')) {
            require ROUTES_PATH . 'web.php';
        }

        // Load API routes (if they exist)
        if (file_exists(ROUTES_PATH . 'api.php')) {
            require ROUTES_PATH . 'api.php';
        }

        // Enable automatic page routing (for file-based routing)
        $router->enablePagesRouting(RESOURCES_PATH . 'pages', [
            'namespace' => 'App\\Pages',
            'cache' => !($appConfig['debug'] ?? false), // Disable cache in debug mode
        ]);
        $router->loadPagesRoutes();

        // Add routing middleware to the pipeline
        $app->pipe(new \Plugs\Http\Middleware\RoutingMiddleware($router, $container));

        /*
        |--------------------------------------------------------------------------
        | Error Handling Middleware
        |--------------------------------------------------------------------------
        |
        | Add error handling middleware to catch and process exceptions.
        | This should be added after routing middleware to catch route errors.
        */
        $app->pipe(new \Plugs\Http\Middleware\ErrorHandlerMiddleware());

        /*
        |--------------------------------------------------------------------------
        | 404 Not Found Fallback Handler
        |--------------------------------------------------------------------------
        |
        | Define a fallback handler for requests that don't match any routes.
        | This provides a user-friendly 404 error page.
        */
        $app->setFallback(function ($request) {
            $acceptHeader = $request->getHeaderLine('Accept');
            $path = $request->getUri()->getPath();

            // Return JSON for API requests
            if (strpos($acceptHeader, 'application/json') !== false || 
                strpos($path, '/api/') === 0) {
                return ResponseFactory::json([
                    'error' => 'Not Found',
                    'message' => 'The requested resource was not found.',
                    'path' => $path,
                    'timestamp' => date('c'),
                ], 404);
            }

            // Return HTML for browser requests
            return ResponseFactory::html(
                '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>404 - Page Not Found | ' . ($appConfig['name'] ?? 'Application') . '</title>
                    <link rel="preconnect" href="https://fonts.googleapis.com">
                    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                    <style>
                        @import url("https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=JetBrains+Mono:wght@700&family=Dancing+Script:wght@700&display=swap");
                        
                        :root {
                            --bg-body: #080b12;
                            --bg-card: rgba(30, 41, 59, 0.5);
                            --border-color: rgba(255, 255, 255, 0.08);
                            --text-primary: #f8fafc;
                            --text-secondary: #94a3b8;
                            --accent-primary: #8b5cf6;
                            --accent-secondary: #3b82f6;
                        }

                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        
                        body {
                            font-family: "Outfit", sans-serif;
                            background-color: var(--bg-body);
                            background-image: 
                                radial-gradient(circle at 10% 10%, rgba(139, 92, 246, 0.05) 0%, transparent 40%),
                                radial-gradient(circle at 90% 90%, rgba(59, 130, 246, 0.05) 0%, transparent 40%);
                            color: var(--text-primary);
                            min-height: 100vh;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-direction: column;
                            padding: 20px;
                            overflow: hidden;
                        }

                        .brand-container {
                            position: absolute;
                            top: 40px;
                            text-align: center;
                        }

                        .brand {
                            font-family: "Dancing Script", cursive;
                            font-size: 2.5rem;
                            font-weight: 700;
                            color: var(--text-primary);
                            text-decoration: none;
                            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            background-clip: text;
                        }

                        .error-card {
                            background: var(--bg-card);
                            backdrop-filter: blur(20px);
                            border: 1px solid var(--border-color);
                            border-radius: 24px;
                            padding: 60px 40px;
                            max-width: 500px;
                            width: 100%;
                            text-align: center;
                            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                        }

                        .error-icon {
                            font-size: 4rem;
                            margin-bottom: 24px;
                            display: block;
                        }

                        .error-code {
                            font-family: "JetBrains Mono", monospace;
                            font-size: 6rem;
                            line-height: 1;
                            font-weight: 700;
                            margin-bottom: 16px;
                            opacity: 0.2;
                            letter-spacing: -2px;
                        }

                        h1 {
                            font-size: 2rem;
                            font-weight: 700;
                            margin-bottom: 16px;
                        }

                        .message {
                            color: var(--text-secondary);
                            font-size: 1.1rem;
                            line-height: 1.6;
                            margin-bottom: 40px;
                        }

                        .actions {
                            display: flex;
                            gap: 16px;
                            justify-content: center;
                        }

                        .btn {
                            padding: 12px 28px;
                            border-radius: 12px;
                            text-decoration: none;
                            font-weight: 600;
                            font-size: 0.95rem;
                            transition: all 0.3s ease;
                            display: inline-flex;
                            align-items: center;
                            gap: 8px;
                            cursor: pointer;
                        }

                        .btn-primary {
                            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
                            color: white;
                            box-shadow: 0 10px 15px -3px rgba(139, 92, 246, 0.3);
                        }

                        .btn-primary:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 20px 25px -5px rgba(139, 92, 246, 0.4);
                        }

                        .btn-secondary {
                            background: rgba(255, 255, 255, 0.05);
                            border: 1px solid var(--border-color);
                            color: var(--text-primary);
                        }

                        .btn-secondary:hover {
                            background: rgba(255, 255, 255, 0.1);
                            transform: translateY(-2px);
                        }

                        @media (max-width: 640px) {
                            .error-card { padding: 40px 20px; }
                            .error-code { font-size: 4.5rem; }
                            .actions { flex-direction: column; }
                            .btn { width: 100%; justify-content: center; }
                        }
                    </style>
                </head>
                <body>
                    <div class="brand-container">
                        <a href="/" class="brand">' . ($appConfig['name'] ?? 'Application') . '</a>
                    </div>

                    <div class="error-card">
                        <span class="error-icon">🌌</span>
                        <div class="error-code">404</div>
                        <h1>Page Not Found</h1>
                        <p class="message">The requested page has vanished into the deep space of our server. It might have been moved or deleted.</p>
                        <div class="actions">
                            <a href="/" class="btn btn-primary">
                                <span>🏠</span> Return Home
                            </a>
                            <button onclick="window.location.reload()" class="btn btn-secondary">
                                <span>🔄</span> Try Again
                            </button>
                        </div>
                    </div>
                </body>
                </html>',
                404
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Return Application Instance
        |--------------------------------------------------------------------------
        |
        | Return the fully configured application instance.
        | This instance will be used by the entry point (public/index.php)
        | to handle incoming HTTP requests.
        */
        return $app;
        PHP;
    }

    public function generateWebRoutes(): string
    {
        return <<<'PHP'
        <?php

        declare(strict_types=1);

        /*
        |--------------------------------------------------------------------------
        | Web Routes Configuration
        |--------------------------------------------------------------------------
        |
        | This file defines all the web routes for your application. Web routes
        | are those that are intended to return HTML views to the browser.
        |
        | Routes are defined using the Route facade, which provides a clean,
        | expressive syntax for defining routes and their associated handlers.
        |
        | Documentation: https://docs.plugs.dev/routing
        */

        use Plugs\Facades\Route;
        use App\Http\Controllers\HomeController;
        use App\Http\Controllers\AuthController;

        /*
        |--------------------------------------------------------------------------
        | Public Routes
        |--------------------------------------------------------------------------
        |
        | Routes accessible to all visitors (no authentication required).
        | These typically include landing pages, documentation, and public content.
        */

        // Homepage - The main landing page of your application
        Route::get('/', [HomeController::class, 'index'])
            ->name('home')
            ->middleware('web');

        // Alternative home route
        Route::get('/home', [HomeController::class, 'index']);

        /*
        |--------------------------------------------------------------------------
        | Authentication Routes
        |--------------------------------------------------------------------------
        |
        | Routes for user authentication, registration, and account management.
        | These routes handle login, logout, registration, and password reset.
        */

        // Authentication Routes
        Route::group(['prefix' => 'auth', 'middleware' => 'web'], function () {
            // Login
            Route::get('/login', [AuthController::class, 'showLoginForm'])
                ->name('login')
                ->middleware('guest');

            Route::post('/login', [AuthController::class, 'login'])
                ->name('login.submit');

            // Registration
            Route::get('/register', [AuthController::class, 'showRegistrationForm'])
                ->name('register')
                ->middleware('guest');

            Route::post('/register', [AuthController::class, 'register'])
                ->name('register.submit');

            // Logout
            Route::post('/logout', [AuthController::class, 'logout'])
                ->name('logout')
                ->middleware('auth');

            // Password Reset
            Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])
                ->name('password.request')
                ->middleware('guest');

            Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])
                ->name('password.email')
                ->middleware('guest');

            Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])
                ->name('password.reset')
                ->middleware('guest');

            Route::post('/reset-password', [AuthController::class, 'resetPassword'])
                ->name('password.update')
                ->middleware('guest');

            // Email Verification
            Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
                ->name('verification.verify')
                ->middleware(['auth', 'signed']);

            Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
                ->name('verification.send')
                ->middleware(['auth', 'throttle:6,1']);
        });

        /*
        |--------------------------------------------------------------------------
        | 404 Catch-All Route (Fallback)
        |--------------------------------------------------------------------------
        |
        | This route will catch any requests that don't match the routes above.
        | It should be the last route defined in the file.
        |
        | Note: The actual 404 handling is done in the bootstrap file,
        | but this provides an alternative method if needed.
        */

        // Route::fallback(function () {
        //     return response()->view('errors.404', [
        //         'title' => 'Page Not Found',
        //         'message' => 'The page you are looking for could not be found.'
        //     ], 404);
        // })->name('404');

        /*
        |--------------------------------------------------------------------------
        | Route Pattern Definitions
        |--------------------------------------------------------------------------
        |
        | Global route patterns that can be reused across multiple routes.
        | These patterns validate route parameters.
        */

        Route::pattern('id', '[0-9]+');
        Route::pattern('slug', '[a-z0-9\-]+');
        Route::pattern('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
        Route::pattern('username', '[a-zA-Z0-9_\-\.]+');

        /*
        |--------------------------------------------------------------------------
        | Route Group Global Middleware
        |--------------------------------------------------------------------------
        |
        | Apply middleware to all routes in this file.
        | Uncomment to apply middleware globally to web routes.
        */

        // Route::middleware(['web'])->group(function () {
        //     // All web routes would go here
        // });
        PHP;
    }

    public function getResults(): array
    {
        return $this->results;
    }
}
