<?php
/**
 * Skinora - About Us
 */
require_once __DIR__ . '/config/config.php';

$pageTitle = 'Our Story & Skincare Philosophy';
$metaDescription = 'Learn about Skinora’s journey: clean botanical science, clinical-grade actives, and our mission to help you discover your effortless, natural glow.';

require_once ROOT_PATH . 'includes/header.php';
?>

<div class="breadcrumb">
    <div class="container">
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <span class="separator">/</span>
        <span class="current">About Skinora</span>
    </div>
</div>

<section style="background: linear-gradient(135deg, #fff7f5 0%, #f7ebe8 100%); padding: 60px 0; border-bottom: 1px solid #ede4e0; text-align: center;">
    <div class="container">
        <span class="section-subtitle">Our Heritage</span>
        <h1 style="font-size: 2.8rem; margin-bottom: 14px;">Pure Science. Natural Radiance.</h1>
        <p style="max-width: 650px; margin: 0 auto; font-size: 1.05rem;">
            Skinora was born from a singular conviction: skincare should be deeply nourishing, clinically verified, and celebrate your authentic skin without compromising purity.
        </p>
    </div>
</section>

<div class="container" style="padding: 70px 20px;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center; margin-bottom: 70px;">
        <div>
            <span class="section-subtitle">Crafted In Pakistan</span>
            <h2 style="font-size: 2.2rem; margin-bottom: 18px;">Formulated Specifically For Our Climate & Complexion</h2>
            <p>Most commercial skincare products imported into South Asia are formulated for temperate, dry Western climates. When applied in the humid heat of Karachi or the high smog winters of Lahore, they often feel suffocating, greasy, or trigger comedogenic breakouts.</p>
            <p>At Skinora, our biochemists and dermatologists formulate lightweight, hyper-absorbent formulations that deliver maximum barrier repair and UV defense while maintaining an invisible, satin-soft touch on the skin.</p>
        </div>
        <div>
            <img src="<?= BASE_URL ?>uploads/products/hero-skincare.svg" alt="Skinora Philosophy" style="border-radius: 16px; box-shadow: 0 10px 30px rgba(179,125,122,0.15);">
        </div>
    </div>

    <!-- Core Brand Pillars -->
    <div class="section-header">
        <span class="section-subtitle">Our Guarantees</span>
        <h2 class="section-title">The Skinora Clean Standard</h2>
    </div>

    <div class="categories-grid" style="margin-bottom: 60px;">
        <div class="category-card">
            <div class="category-card-icon">🔬</div>
            <h3>Proven Actives</h3>
            <p>15% pure Vitamin C, 5 Essential Ceramides, and multi-molecular weight Hyaluronic Acid.</p>
        </div>
        <div class="category-card">
            <div class="category-card-icon">🌱</div>
            <h3>100% Cruelty-Free</h3>
            <p>Never tested on animals. Halal compliant and formulated without harsh animal derivatives.</p>
        </div>
        <div class="category-card">
            <div class="category-card-icon">🚫</div>
            <h3>Zero Nasties</h3>
            <p>Completely free from parabens, phthalates, synthetic dyes, and artificial heavy perfumes.</p>
        </div>
        <div class="category-card">
            <div class="category-card-icon">♻️</div>
            <h3>Eco-Conscious</h3>
            <p>Recyclable glass dropper bottles, amber jars, and sustainable biodegradable packaging.</p>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'includes/footer.php'; ?>
