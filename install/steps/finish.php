<?php
session_start();

if (!isset($_SESSION['install_ready'])) {
    header('Location: ?step=welcome');
    exit;
}

ob_start();
?>

<div style="text-align: center; padding: 40px 0;">
    <div style="font-size: 4rem; margin-bottom: 20px;">🎉</div>
    <h1 style="color: var(--success); margin-bottom: 10px;">Already there!</h1>
    <p style="color: #64748b; font-size: 1.1rem; max-width: 600px; margin: 0 auto 30px;">
        The system has been configured and the file structure has been created.
        Click the button below to finalize the installation and launch your application.
    </p>

    <div style="margin: 30px 0; text-align: left; background: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);"
        class="dark:bg-slate-800 dark:border-slate-700">
        <h3 style="margin-top: 0; color: #334155; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 15px;"
            class="dark:text-slate-200 dark:border-slate-700">
            Installation Log
        </h3>
        <ul style="max-height: 250px; overflow-y: auto; font-family: 'Fira Code', monospace; font-size: 0.9em; color: #475569; list-style: none; padding: 0;"
            class="dark:text-slate-400">
            <?php if (isset($_SESSION['install_results'])): ?>
                <?php foreach ($_SESSION['install_results'] as $result): ?>
                    <li style="padding: 4px 0; border-bottom: 1px dashed #f1f5f9; display: flex; align-items: center;">
                        <span style="color: var(--success); margin-right: 10px;">✓</span>
                        <?= htmlspecialchars($result) ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <div
        style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); padding: 30px; border-radius: 12px; color: white; margin-bottom: 30px; text-align: left;">
        <h2 style="margin-top: 0; font-size: 1.5rem;">🚀 About ThePlugs Framework</h2>
        <p style="opacity: 0.9; line-height: 1.6; margin-bottom: 0;">
            You are now ready to build scalable, high-performance applications.
            ThePlugs Framework provides you with a robust routing system, powerful database ORM,
            and a sleek template engine to speed up your development.
        </p>
    </div>

    <form action="?step=activate" method="post">
        <button type="submit"
            style="background: var(--success); color: white; border: none; padding: 16px 48px; font-size: 1.2rem; font-weight: 600; border-radius: 8px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);">
            Finalize & Launch Application →
        </button>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
