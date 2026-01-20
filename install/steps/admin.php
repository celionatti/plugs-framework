<?php
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Validation
    if (empty($name))
        $errors['name'] = 'Full Name is required';
    if (empty($email))
        $errors['email'] = 'Email Address is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Invalid email format';
    if (empty($password))
        $errors['password'] = 'Password is required';
    if (strlen($password) < 8)
        $errors['password'] = 'Password must be at least 8 characters';
    if ($password !== $confirm)
        $errors['confirm'] = 'Passwords do not match';

    if (empty($errors)) {
        // Store admin details in session to be processed in the next step
        $_SESSION['admin'] = [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ];

        // Proceed to installation process
        header('Location: ?step=process');
        exit;
    }
}

ob_start();
?>

<div style="text-align: left;">
    <h1 style="margin-bottom: 20px;">Admin Account Setup</h1>
    <p style="color: #64748b; margin-bottom: 30px;">
        Create your primary administrator account to manage the application.
    </p>

    <form method="post">
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="John Doe"
                style="width: 100%; padding: 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            <?php if (isset($errors['name'])): ?>
                <div style="color: #ef4444; font-size: 0.9em; margin-top: 5px;">
                    <?= $errors['name'] ?>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Email Address</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                placeholder="admin@example.com"
                style="width: 100%; padding: 12px; border: 1px solid var(--border-light); border-radius: 6px;">
            <?php if (isset($errors['email'])): ?>
                <div style="color: #ef4444; font-size: 0.9em; margin-top: 5px;">
                    <?= $errors['email'] ?>
                </div>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Password</label>
                <input type="password" name="password"
                    style="width: 100%; padding: 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                <?php if (isset($errors['password'])): ?>
                    <div style="color: #ef4444; font-size: 0.9em; margin-top: 5px;">
                        <?= $errors['password'] ?>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Confirm Password</label>
                <input type="password" name="confirm"
                    style="width: 100%; padding: 12px; border: 1px solid var(--border-light); border-radius: 6px;">
                <?php if (isset($errors['confirm'])): ?>
                    <div style="color: #ef4444; font-size: 0.9em; margin-top: 5px;">
                        <?= $errors['confirm'] ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <a href="?step=database" style="text-decoration: none; color: #64748b; padding: 10px 20px;">Back</a>
            <button type="submit"
                style="background: var(--primary); color: white; border: none; padding: 12px 30px; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: 500;">Create
                Account & Install →</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
