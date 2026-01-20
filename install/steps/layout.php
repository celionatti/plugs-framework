<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Framework Installation Wizard">
    <title>Framework - Installation Wizard</title>
    <!-- Use Inter font for a more professional look -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Dancing+Script:wght@700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/framework/install/assets/style.css">
    <style>
        :root {
            /* Shades of Green Theme */
            --primary: #16a34a;
            /* green-600 */
            --primary-hover: #15803d;
            /* green-700 */
            --success: #15803d;
            /* green-700 */
            --bg-light: #f0fdf4;
            /* green-50 */
            --text-light: #14532d;
            /* green-900 */
            --card-light: #ffffff;
            --border-light: #bbf7d0;
            /* green-200 */

            --bg-dark: #052e16;
            /* green-950 */
            --text-dark: #dcfce7;
            /* green-100 */
            --card-dark: #14532d;
            /* green-900 */
            --border-dark: #166534;
            /* green-800 */
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-light);
            transition: background-color 0.3s, color 0.3s;
            margin: 0;
            line-height: 1.5;
        }

        .installer {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        .steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }

        .steps::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--border-light);
            z-index: -1;
            transform: translateY(-50%);
        }

        .step {
            background: var(--card-light);
            padding: 10px 25px;
            border-radius: 9999px;
            border: 2px solid var(--border-light);
            color: #64748b;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .step.active {
            border-color: var(--primary);
            color: var(--primary);
            background: #eff6ff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .step.completed {
            border-color: var(--success);
            color: var(--success);
            background: #f0fdf4;
        }

        .card {
            background: var(--card-light);
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 50px;
            border: 1px solid var(--border-light);
        }

        /* Dark Mode Overrides */
        body.dark {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }

        body.dark .card {
            background-color: var(--card-dark);
            border-color: var(--border-dark);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }

        body.dark .steps::before {
            background: var(--border-dark);
        }

        body.dark .step {
            background: var(--card-dark);
            border-color: var(--border-dark);
            color: #94a3b8;
        }

        body.dark .step.active {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(59, 130, 246, 0.1);
        }

        body.dark .step.completed {
            border-color: var(--success);
            color: var(--success);
            background: rgba(34, 197, 94, 0.1);
        }

        body.dark input {
            background-color: #020617;
            border-color: var(--border-dark);
            color: white;
        }

        body.dark input:focus {
            border-color: var(--primary);
            outline: none;
        }

        /* Utility */
        h1,
        h2,
        h3 {
            color: inherit;
        }

        button {
            cursor: pointer;
            font-family: inherit;
        }
    </style>
</head>

<body>

    <div class="installer">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div
                style="font-family: 'Dancing Script', cursive; font-size: 2.5rem; font-weight: 700; color: var(--primary); letter-spacing: 1px;">
                ThePlugs
            </div>
            <button class="theme-toggle" onclick="toggleTheme()"
                style="background:var(--card-light); border:1px solid var(--border-light); cursor:pointer; font-size: 1.2rem; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px;"
                aria-label="Toggle Theme">
                <span id="theme-icon">🌙</span>
            </button>
        </div>

        <?php
        $currentStep = $_GET['step'] ?? 'welcome';
        $stepOrder = ['welcome' => 0, 'requirements' => 1, 'database' => 2, 'admin' => 3, 'finish' => 4];
        $displaySteps = [
            'welcome' => 'Start',
            'requirements' => 'System',
            'database' => 'Database',
            'admin' => 'Account',
            'finish' => 'Done'
        ];
        ?>

        <div class="steps">
            <?php foreach ($displaySteps as $key => $label):
                $stepIndex = $stepOrder[$key];
                $currentIndex = $stepOrder[$currentStep] ?? 0;

                // Adjust index for processing state
                if ($currentStep === 'process')
                    $currentIndex = 3.5;
                if ($currentStep === 'activate')
                    $currentIndex = 5;

                $isActive = $key === $currentStep;
                $isCompleted = $stepIndex < $currentIndex;

                // Logic for intermediate steps
                if ($currentStep === 'process' && $key === 'admin') {
                    $isActive = true;
                    $isCompleted = false;
                }
                if ($currentStep === 'activate' && $key === 'finish') {
                    $isActive = true;
                    $isCompleted = false;
                }
                ?>
                <div class="step <?= $isActive ? 'active' : '' ?><?= $isCompleted ? ' completed' : '' ?>">
                    <?= $label ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card">
            <?= $content ?>
        </div>

        <div style="text-align: center; margin-top: 30px; color: #64748b; font-size: 0.9rem;">
            &copy; <?= date('Y') ?> ThePlugs Framework. All rights reserved.
        </div>
    </div>

    <script>
        function toggleTheme() {
            document.body.classList.toggle('dark');
            const isDark = document.body.classList.contains('dark');
            localStorage.setItem('plugs-theme', isDark ? 'dark' : 'light');
            updateThemeIcon(isDark);
        }

        function updateThemeIcon(isDark) {
            document.getElementById('theme-icon').textContent = isDark ? '☀️' : '🌙';
            const btn = document.querySelector('.theme-toggle');
            if (isDark) {
                btn.style.background = 'var(--card-dark)';
                btn.style.borderColor = 'var(--border-dark)';
            } else {
                btn.style.background = 'var(--card-light)';
                btn.style.borderColor = 'var(--border-light)';
            }
        }

        const savedTheme = localStorage.getItem('plugs-theme');
        if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.body.classList.add('dark');
            updateThemeIcon(true);
        } else {
            updateThemeIcon(false);
        }
    </script>

</body>

</html>