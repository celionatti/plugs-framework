<?php
ob_start();

$requirements = [
    'php' => [
        'label' => 'PHP Version >= 8.0',
        'check' => version_compare(PHP_VERSION, '8.0.0', '>='),
        'current' => PHP_VERSION
    ],
    'pdo' => [
        'label' => 'PDO Extension',
        'check' => extension_loaded('pdo'),
    ],
    'mbstring' => [
        'label' => 'Mbstring Extension',
        'check' => extension_loaded('mbstring'),
    ],
    'openssl' => [
        'label' => 'OpenSSL Extension',
        'check' => extension_loaded('openssl'),
    ],
    'json' => [
        'label' => 'JSON Extension',
        'check' => extension_loaded('json'),
    ],
];

// Check permissions if storage exists, or parent if it doesn't
$storagePath = dirname(__DIR__, 2) . '/storage';
$isWritable = is_writable(file_exists($storagePath) ? $storagePath : dirname($storagePath));
$requirements['storage'] = [
    'label' => 'Storage Directory Writable',
    'check' => $isWritable,
];

$allMet = !in_array(false, array_column($requirements, 'check'));

if ($allMet && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Location: ?step=database');
    exit;
}
?>

<div style="text-align: left;">
    <h1 style="margin-bottom: 20px;">Server Requirements</h1>
    <p style="color: #64748b; margin-bottom: 30px;">
        Checking if your server meets the requirements to run ThePlugs Framework.
    </p>

    <div style="background: white; border: 1px solid var(--border-light); border-radius: 8px; overflow: hidden; margin-bottom: 30px;"
        class="dark:bg-slate-800 dark:border-slate-700">
        <?php foreach ($requirements as $key => $req): ?>
            <div
                style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid var(--border-light);">
                <div>
                    <span style="font-weight: 500;">
                        <?= htmlspecialchars($req['label']) ?>
                    </span>
                    <?php if (isset($req['current'])): ?>
                        <span style="font-size: 0.85em; color: #64748b; margin-left: 10px;">(Current:
                            <?= $req['current'] ?>)
                        </span>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($req['check']): ?>
                        <span style="color: var(--success); font-weight: bold;">✔ Passed</span>
                    <?php else: ?>
                        <span style="color: #ef4444; font-weight: bold;">✘ Failed</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <a href="?step=welcome" style="text-decoration: none; color: #64748b; padding: 10px 20px;">Back</a>

        <?php if ($allMet): ?>
            <form method="post" style="margin: 0;">
                <button type="submit"
                    style="background: var(--primary); color: white; border: none; padding: 12px 30px; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: 500;">
                    Continue to Configuration →
                </button>
            </form>
        <?php else: ?>
            <button disabled
                style="background: #e2e8f0; color: #94a3b8; border: none; padding: 12px 30px; border-radius: 6px; cursor: not-allowed; font-size: 1rem;">
                Fix Requirements to Continue
            </button>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
