<?php
ob_start();
?>

<div style="text-align: center; padding: 20px 0;">
    <h1
        style="font-size: 3rem; margin-bottom: 1rem; font-weight: 800; letter-spacing: -1px; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
        Build Something Amazing.</h1>
    <p
        style="font-size: 1.25rem; color: #64748b; margin-bottom: 3rem; max-width: 600px; margin-left: auto; margin-right: auto;">
        Welcome to <strong>ThePlugs Framework</strong>. The toolset for modern PHP developers who value speed,
        simplicity, and elegance.
    </p>

    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; text-align: left; margin-bottom: 40px;">
        <div
            style="padding: 20px; background: rgba(22, 163, 74, 0.05); border-radius: 12px; border: 1px solid rgba(22, 163, 74, 0.1);">
            <div style="font-size: 1.5rem; margin-bottom: 10px;">⚡</div>
            <h3 style="margin: 0 0 5px 0;">Lightning Fast</h3>
            <p style="margin: 0; font-size: 0.9rem; color: #64748b;">Optimized for performance with a lightweight core.
            </p>
        </div>
        <div
            style="padding: 20px; background: rgba(21, 128, 61, 0.05); border-radius: 12px; border: 1px solid rgba(21, 128, 61, 0.1);">
            <div style="font-size: 1.5rem; margin-bottom: 10px;">🔒</div>
            <h3 style="margin: 0 0 5px 0;">Secure by Default</h3>
            <p style="margin: 0; font-size: 0.9rem; color: #64748b;">Built-in protection against common vulnerabilities.
            </p>
        </div>
        <div
            style="padding: 20px; background: rgba(34, 197, 94, 0.05); border-radius: 12px; border: 1px solid rgba(34, 197, 94, 0.1);">
            <div style="font-size: 1.5rem; margin-bottom: 10px;">🛠️</div>
            <h3 style="margin: 0 0 5px 0;">Developer Friendly</h3>
            <p style="margin: 0; font-size: 0.9rem; color: #64748b;">Intuitive API and robust tooling included.</p>
        </div>
    </div>

    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 40px; border: 1px solid #e2e8f0;"
        class="dark:bg-slate-800 dark:border-slate-700">
        <p style="margin: 0; color: #475569;" class="dark:text-slate-300">
            Current Environment: <code
                style="background: #e2e8f0; padding: 2px 6px; border-radius: 4px; color: #dc2626; font-weight: 600;"
                class="dark:bg-slate-700 dark:text-red-400">Development</code> (Setup Mode)
        </p>
    </div>

    <a href="?step=requirements" style="text-decoration: none; display: inline-block;">
        <button
            style="background: var(--primary); color: white; border: none; padding: 16px 40px; font-size: 1.1rem; border-radius: 8px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.5);">
            Start Installation Loop →
        </button>
    </a>

    <p style="margin-top: 20px; font-size: 0.85rem; color: #94a3b8;">
        By continuing, you agree to the setup of files on your local system.
    </p>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
