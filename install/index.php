<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Install Index File
|--------------------------------------------------------------------------
|
| This is the install entry point to help create folders and files needed.
*/

$lockFile = dirname(__DIR__) . '/plugs.lock';
$step = $_GET['step'] ?? 'welcome';

// Only block if lock exists AND we are not trying to view a non-install page 
// (though here we want to block install completely if locked)
if (file_exists($lockFile)) {
    // If the user just finished, we might still be in the install directory. 
    // Redirect to public.
    header('Location: /');
    // header('Location: ../public/index.php');
    exit;
}

$allowedSteps = [
    'welcome',
    'requirements',
    'database',
    'admin',
    'process',
    'finish',
    'activate'
];

if (!in_array($step, $allowedSteps)) {
    $step = 'welcome';
}

$stepFile = __DIR__ . '/steps/' . $step . '.php';

if (file_exists($stepFile)) {
    require $stepFile;
} else {
    exit("Step '$step' not found.");
}