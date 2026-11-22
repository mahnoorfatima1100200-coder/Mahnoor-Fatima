<?php
/**
 * Skinora - Global Customer Header
 */
require_once __DIR__ . '/../config/config.php';

$pageTitle = isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME . ' | ' . SITE_TAGLINE : SITE_NAME . ' - ' . SITE_TAGLINE;
$metaDescription = isset($metaDescription) ? $metaDescription : 'Discover your natural glow with Skinora. Premium, dermatologist-tested skincare, serums, moisturizers, and sunscreens tailored for Pakistani skin.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDescription) ?>">
    <meta name="author" content="Skinora Skincare">
    <title><?= e($pageTitle) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/responsive.css">

    <!-- Global JavaScript Configuration -->
    <script>
        window.skinoraConfig = {
            baseUrl: <?= json_encode(BASE_URL) ?>,
            csrfToken: <?= json_encode(generateCsrfToken()) ?>,
            currency: <?= json_encode(SITE_CURRENCY_SYMBOL) ?>
        };
    </script>
</head>
<body>
<?php require_once ROOT_PATH . 'includes/navbar.php'; ?>
