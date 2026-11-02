<?php
$pageTitle = $pageTitle ?? 'Student Grading System';
$pageDescription = $pageDescription ?? 'Review student results and grades.';
$pageBackUrl = $pageBackUrl ?? '';
$pageBackLabel = $pageBackLabel ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="docs/styles.css">
</head>

<body>
    <main class="page">
        <header class="page-header">
            <div>
                <p class="eyebrow">Academic records</p>
                <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="subtitle"><?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php if ($pageBackUrl !== ''): ?>
                <a class="button button--secondary" href="<?= htmlspecialchars($pageBackUrl, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($pageBackLabel, ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endif; ?>
        </header>
