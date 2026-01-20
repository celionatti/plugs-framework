<?php
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = trim($_POST['host'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $user = trim($_POST['user'] ?? '');
    $pass = $_POST['pass'] ?? '';

    // Validation
    if (empty($host))
        $errors['host'] = 'Database host is required';
    if (empty($name))
        $errors['name'] = 'Database name is required';
    if (empty($user))
        $errors['user'] = 'Database user is required';

    if (empty($errors)) {
        // Try to establish connection
        try {
            $dsn = "mysql:host=" . htmlspecialchars($host) . ";dbname=" . htmlspecialchars($name);
            $pdo = new PDO($dsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $_SESSION['db'] = [
                'host' => $host,
                'name' => $name,
                'user' => $user,
                'pass' => $pass,
            ];

            // Redirect to admin setup step
            header('Location: ?step=admin');
            exit;
        } catch (PDOException $e) {
            $errors['connection'] = 'Connection failed: ' . $e->getMessage();
        }
    }
}

ob_start();
?>

<div style="text-align: left;">
    <h1 style="margin-bottom: 20px;">Database Configuration</h1>

    <?php if (isset($errors['connection'])): ?>
        <div
            style="background: #fef2f2; border: 1px solid #fee2e2; color: #b91c1c; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            <strong>Connection Error:</strong> <?= htmlspecialchars($errors['connection']) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Host</label>
            <input type="text" name="host" value="<?= htmlspecialchars($_POST['host'] ?? 'localhost') ?>"
                style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            <?php if (isset($errors['host'])): ?>
                <div style="color: red; font-size: 0.9em; margin-top: 5px;"><?= $errors['host'] ?></div><?php endif; ?>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Database Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                placeholder="e.g. wrapper_db"
                style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            <?php if (isset($errors['name'])): ?>
                <div style="color: red; font-size: 0.9em; margin-top: 5px;"><?= $errors['name'] ?></div><?php endif; ?>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Username</label>
            <input type="text" name="user" value="<?= htmlspecialchars($_POST['user'] ?? 'root') ?>"
                style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            <?php if (isset($errors['user'])): ?>
                <div style="color: red; font-size: 0.9em; margin-top: 5px;"><?= $errors['user'] ?></div><?php endif; ?>
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Password</label>
            <input type="password" name="pass"
                style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
        </div>

        <div style="display: flex; justify-content: space-between;">
            <a href="?step=welcome" style="text-decoration: none; color: #64748b; padding: 10px 20px;">Back</a>
            <button type="submit"
                style="background: var(--primary); color: white; border: none; padding: 12px 30px; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: 500;">Verify
                & Continue →</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
