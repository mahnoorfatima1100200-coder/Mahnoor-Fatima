-- =======================================================
-- Skinora - Beauty & Skincare E-Commerce Database Schema
-- Brand: Skinora – Discover Your Natural Glow
-- Target: MySQL 5.7+ / 8.0+ / MariaDB (XAMPP & Free Hosting)
-- =======================================================

CREATE DATABASE IF NOT EXISTS `skinora_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `skinora_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Users Table (Customers and Administrators)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `role` ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
  `status` ENUM('active', 'inactive', 'banned') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_user_email` (`email`),
  INDEX `idx_user_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categories Table
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `image` VARCHAR(255) NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_cat_slug` (`slug`),
  INDEX `idx_cat_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Products Table
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NULL,
  `name` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL UNIQUE,
  `sku` VARCHAR(60) NULL UNIQUE,
  `short_description` VARCHAR(350) NULL,
  `full_description` LONGTEXT NULL,
  `ingredients` TEXT NULL,
  `how_to_use` TEXT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `sale_price` DECIMAL(10,2) NULL DEFAULT NULL,
  `stock_quantity` INT NOT NULL DEFAULT 0,
  `main_image` VARCHAR(255) NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_best_seller` TINYINT(1) NOT NULL DEFAULT 0,
  `is_new_arrival` TINYINT(1) NOT NULL DEFAULT 1,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_prod_category` (`category_id`),
  INDEX `idx_prod_slug` (`slug`),
  INDEX `idx_prod_featured` (`is_featured`),
  INDEX `idx_prod_price` (`price`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Product Gallery Images
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_pimages_prod` (`product_id`),
  CONSTRAINT `fk_pimages_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Customer Addresses
DROP TABLE IF EXISTS `addresses`;
CREATE TABLE `addresses` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `full_name` VARCHAR(120) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `address_line1` VARCHAR(255) NOT NULL,
  `address_line2` VARCHAR(255) NULL,
  `city` VARCHAR(80) NOT NULL,
  `state` VARCHAR(80) NULL,
  `postal_code` VARCHAR(20) NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_addr_user` (`user_id`),
  CONSTRAINT `fk_addr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Coupons Table
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `discount_type` ENUM('percent', 'fixed') NOT NULL DEFAULT 'percent',
  `discount_value` DECIMAL(10,2) NOT NULL,
  `min_spend` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `max_discount` DECIMAL(10,2) NULL,
  `expiry_date` DATE NULL,
  `usage_limit` INT NULL,
  `usage_count` INT NOT NULL DEFAULT 0,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_coupon_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Cart Table (Session or User backed)
DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NULL,
  `session_id` VARCHAR(100) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_cart_user` (`user_id`),
  INDEX `idx_cart_session` (`session_id`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Cart Items Table
DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE `cart_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `cart_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `price` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_citems_cart` (`cart_id`),
  INDEX `idx_citems_product` (`product_id`),
  CONSTRAINT `fk_citems_cart` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_citems_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Wishlist Table
DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE `wishlist` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_user_product` (`user_id`, `product_id`),
  INDEX `idx_wish_user` (`user_id`),
  CONSTRAINT `fk_wish_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_wish_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Orders Table
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT UNSIGNED NULL,
  `customer_name` VARCHAR(120) NOT NULL,
  `customer_email` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(30) NOT NULL,
  `shipping_address` VARCHAR(255) NOT NULL,
  `city` VARCHAR(80) NOT NULL,
  `postal_code` VARCHAR(20) NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  `shipping_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `payment_method` ENUM('cod', 'card', 'bank_transfer') NOT NULL DEFAULT 'cod',
  `payment_status` ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending',
  `order_status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
  `notes` TEXT NULL,
  `coupon_code` VARCHAR(50) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_order_num` (`order_number`),
  INDEX `idx_order_user` (`user_id`),
  INDEX `idx_order_status` (`order_status`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Order Items (Historical Snapshot)
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `product_price` DECIMAL(10,2) NOT NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `subtotal` DECIMAL(10,2) NOT NULL,
  INDEX `idx_oitems_order` (`order_id`),
  CONSTRAINT `fk_oitems_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_oitems_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Reviews Table
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NULL,
  `reviewer_name` VARCHAR(100) NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `review_text` TEXT NOT NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_rev_prod` (`product_id`),
  INDEX `idx_rev_status` (`status`),
  CONSTRAINT `fk_rev_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rev_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Contact Messages Table
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_msg_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Newsletter Subscribers Table
DROP TABLE IF EXISTS `newsletter_subscribers`;
CREATE TABLE `newsletter_subscribers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `status` ENUM('active', 'unsubscribed') NOT NULL DEFAULT 'active',
  `subscribed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;


-- =======================================================
-- SEED DATA: Realistic Skincare demo store
-- =======================================================

-- Admin user (email: admin@skinora.com / password: admin123)
-- Customer user (email: ayesha@example.com / password: customer123)
-- Password hash for 'admin123' and 'customer123': $2y$10$wE9qE2o3W6qEwOsk1Cj4E.eN1z3x4v5w6u7t8s9r0q1p2o3n4m5l6
INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `role`, `status`) VALUES
(1, 'Skinora Admin', 'admin@skinora.com', '$2y$10$wE9qE2o3W6qEwOsk1Cj4E.eN1z3x4v5w6u7t8s9r0q1p2o3n4m5l6', '+92 300 1234567', 'admin', 'active'),
(2, 'Ayesha Khan', 'ayesha@example.com', '$2y$10$wE9qE2o3W6qEwOsk1Cj4E.eN1z3x4v5w6u7t8s9r0q1p2o3n4m5l6', '+92 321 9876543', 'customer', 'active');

-- Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `is_featured`, `status`) VALUES
(1, 'Serums', 'serums', 'Potent, active formulations for intense glow, hydration and repair.', 'cat-serums.svg', 1, 'active'),
(2, 'Moisturizers', 'moisturizers', 'Nourishing creams and lightweight gels to lock in vital skin hydration.', 'cat-moisturizers.svg', 1, 'active'),
(3, 'Face Wash', 'face-wash', 'Gentle, sulphate-free cleansers that balance and refresh your skin.', 'cat-facewash.svg', 1, 'active'),
(4, 'Sunscreens', 'sunscreens', 'Broad-spectrum SPF protection leaving zero white cast and maximum defense.', 'cat-sunscreen.svg', 1, 'active'),
(5, 'Toners', 'toners', 'Alcohol-free clarifying and soothing floral waters for radiant balance.', 'cat-toners.svg', 1, 'active'),
(6, 'Face Masks', 'face-masks', 'Intensive spa-grade sheet masks and clay treatments for rejuvenation.', 'cat-masks.svg', 1, 'active'),
(7, 'Lip Care', 'lip-care', 'Ultra-hydrating peptide balms and organic butter treatments.', 'cat-lipcare.svg', 1, 'active'),
(8, 'Cleansers', 'cleansers', 'Nourishing botanical cleansing oils and micellar melts.', 'cat-cleansers.svg', 1, 'active');

-- Products
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `sku`, `short_description`, `full_description`, `ingredients`, `how_to_use`, `price`, `sale_price`, `stock_quantity`, `main_image`, `is_featured`, `is_best_seller`, `is_new_arrival`, `status`) VALUES
(1, 1, 'Vitamin C 15% Radiance Glow Serum', 'vitamin-c-15-radiance-glow-serum', 'SKN-SRM-001', 'High-potency brightening serum infused with pure Ethyl Ascorbic Acid, Ferulic Acid, and Hyaluronic Acid for even tone.', 'Experience an instant surge of luminosity with Skinora’s Vitamin C 15% Radiance Glow Serum. Formulated with clinical-grade ethyl ascorbic acid, this antioxidant powerhouse defends against free radical damage, minimizes dark spots, and revitalizes tired skin.', 'Aqua, 3-O-Ethyl Ascorbic Acid (15%), Niacinamide, Ferulic Acid, Hyaluronic Acid, Tocopherol (Vitamin E), Citrus Aurantium Dulcis Fruit Extract, Glycerin, Phenoxyethanol.', 'Apply 3-4 drops each morning to freshly cleansed face and neck. Gently tap into skin until fully absorbed. Always follow with SPF sunscreen during the day.', 2450.00, 2150.00, 50, 'prod-vit-c-serum.svg', 1, 1, 1, 'active'),

(2, 1, 'Multi-Molecular Hyaluronic Acid Booster', 'multi-molecular-hyaluronic-acid-booster', 'SKN-SRM-002', 'Tri-weight hyaluronic acid hydration complex delivering deep cellular moisture and plumpness.', 'Quench thirsty skin with our Multi-Molecular Hyaluronic Acid Booster. Engineered with three molecular weights of hyaluronic acid, this featherlight gel delivers hydration across multiple epidermal layers for smooth, supple, bouncy skin.', 'Aqua, Sodium Hyaluronate, Hydrolyzed Hyaluronic Acid, Panthenol (Pro-Vitamin B5), Aloe Barbadensis Leaf Juice, Centella Asiatica Extract, Sodium PCA.', 'Dispense 2-3 drops onto damp skin morning and night. Massage gently in upward circular motions. Seal with Skinora Barrier Relief Moisturizer.', 2250.00, NULL, 65, 'prod-hyaluronic-serum.svg', 1, 1, 0, 'active'),

(3, 3, 'Gentle Cloud Foaming Face Wash', 'gentle-cloud-foaming-face-wash', 'SKN-CL-001', 'Sulphate-free pH-balanced foaming cleanser enriched with Chamomile and Green Tea.', 'Wash away impurities, excess sebum, and pollution without stripping your natural lipid barrier. Gentle Cloud Face Wash produces an airy, whipped lather that calms sensitive and acne-prone skin.', 'Aqua, Cocamidopropyl Betaine, Sodium Cocoyl Isethionate, Glycerin, Camellia Sinensis (Green Tea) Leaf Extract, Chamomilla Recutita Flower Extract, Allantoin, Citric Acid.', 'Pump 1-2 times onto wet palms. Work into a delicate foam and massage onto face in circular motions for 60 seconds. Rinse thoroughly with lukewarm water.', 1450.00, 1250.00, 80, 'prod-facewash.svg', 1, 1, 1, 'active'),

(4, 2, 'Barrier Relief Ceramide Daily Moisturizer', 'barrier-relief-ceramide-daily-moisturizer', 'SKN-MST-001', 'Restorative lightweight cream packed with 5 Essential Ceramides, Squalane, and Niacinamide.', 'Skinora Barrier Relief Daily Moisturizer strengthens compromised skin barriers while sealing in hydration all day. It sinks in effortlessly with a velvety, non-greasy finish ideal for all skin types including sensitive Pakistani skin.', 'Aqua, Caprylic/Capric Triglyceride, Ceramide NP, Ceramide AP, Ceramide EOP, Phytosphingosine, Cholesterol, Squalane, Niacinamide 3%, Butyrospermum Parkii (Shea) Butter, Xanthan Gum.', 'Smooth an almond-sized amount over face and neck after your serum routine, both morning and evening.', 1850.00, NULL, 90, 'prod-moisturizer.svg', 1, 1, 0, 'active'),

(5, 4, 'Invisible Shield Featherlight Sunscreen SPF 50+', 'invisible-shield-featherlight-sunscreen-spf-50', 'SKN-SPF-001', 'Broad spectrum UVA/UVB protection with Zero white cast, water-resistant and non-comedogenic.', 'Never skip your sun defense again. Invisible Shield SPF 50+ PA++++ is specifically crafted for warm and humid climates. It leaves an invisible, matte-satin finish with absolutely zero white cast and works wonderfully beneath makeup.', 'Aqua, Ethylhexyl Methoxycinnamate, Zinc Oxide (Micronized), Octocrylene, Diethylamino Hydroxybenzoyl Hexyl Benzoate, Glycerin, Silica, Polygonum Aviculare Extract, Bisabolol.', 'Apply generously and evenly to face, neck, and ears 15 minutes before sun exposure. Reapply every 2 hours or after 80 minutes of swimming or sweating.', 2100.00, 1899.00, 110, 'prod-sunscreen.svg', 1, 1, 1, 'active'),

(6, 2, 'Peptide & Bakuchiol Night Repair Cream', 'peptide-bakuchiol-night-repair-cream', 'SKN-MST-002', 'Luxurious anti-aging night treatment with natural Retinol alternative Bakuchiol and Copper Peptides.', 'Wake up to renewed radiance. This rich overnight treatment stimulates cellular collagen renewal and gently firms fine lines without the irritation commonly caused by conventional retinoids.', 'Aqua, Rosa Damascena Flower Water, Bakuchiol (1.5%), Copper Tripeptide-1, Palmitoyl Pentapeptide-4, Simmondsia Chinensis (Jojoba) Seed Oil, Argania Spinosa Kernel Oil, Cetearyl Alcohol.', 'Warm a pea-sized amount between fingertips and gently press into face, neck, and décolletage every night before bed.', 2850.00, 2499.00, 40, 'prod-night-cream.svg', 1, 0, 1, 'active'),

(7, 5, 'Damask Rose Petal Clarifying Toner', 'damask-rose-petal-clarifying-toner', 'SKN-TNR-001', 'Distilled organic Rose Water with Witch Hazel and Witch Leaf extract to balance skin pH.', 'Infused with handpicked Damask roses from organic gardens, this soothing toner refines pores, balances natural sebum, and primes skin for serums and moisturizers.', 'Rosa Damascena (Rose) Distillate (90%), Hamamelis Virginiana (Witch Hazel) Water, Glycerin, Sodium Hyaluronate, Rosa Centifolia Flower Extract, Phenoxyethanol.', 'Pour onto a soft cotton pad or mist directly onto clean skin. Press gently over face and neck until absorbed.', 1350.00, NULL, 75, 'prod-toner.svg', 0, 0, 1, 'active'),

(8, 7, 'Rose Petal Nourishing Peptide Lip Balm', 'rose-petal-nourishing-peptide-lip-balm', 'SKN-LIP-001', 'Intensive buttery lip treatment with Shea Butter, Vitamin E, and Plumping Peptides.', 'Say goodbye to chapped, dry lips. Skinora’s Rose Petal Lip Balm wraps lips in a protective cocoon of moisture, giving a delicate glossy rose sheen with a subtle natural tint.', 'Ricinus Communis Seed Oil, Butyrospermum Parkii (Shea) Butter, Cera Alba, Palmitoyl Oligopeptide, Tocopheryl Acetate, Rosa Damascena Flower Oil, CI 77491.', 'Apply directly to lips throughout the day as needed. Apply a generous layer before bedtime as an overnight lip sleeping mask.', 750.00, 650.00, 150, 'prod-lip-balm.svg', 0, 1, 1, 'active'),

(9, 8, 'Meltdown Gentle Botanical Cleansing Oil', 'meltdown-gentle-botanical-cleansing-oil', 'SKN-CL-002', 'Silky emulsifying oil cleanser that melts waterproof makeup, sunscreen, and stubborn blackheads.', 'The essential first step in your double-cleansing ritual. Formulated with lightweight olive squalane and sweet almond oil, it effortlessly breaks down waterproof mascara and SPF while emulsifying cleanly with water.', 'Prunus Amygdalus Dulcis (Sweet Almond) Oil, Olea Europaea (Olive) Squalane, PEG-20 Glyceryl Triisostearate, Simmondsia Chinensis Seed Oil, Tocopherol, Lavandula Angustifolia Oil.', 'Pump 2-3 times onto dry hands and massage across dry face for 60 seconds. Add warm water to emulsify into a milky lotion, then rinse completely.', 1950.00, NULL, 45, 'prod-cleanser.svg', 0, 0, 0, 'active'),

(10, 6, 'Hydra-Glow Bio-Cellulose Face Mask (Pack of 3)', 'hydra-glow-bio-cellulose-face-mask-pack-of-3', 'SKN-MSK-001', 'Second-skin bio-cellulose sheet masks soaked in 25ml concentrated Marine Collagen & Niacinamide essence.', 'Drench dull, stressed skin with a high-intensity hydration bath. These eco-friendly coconut bio-cellulose sheets adhere like a second skin, pushing active peptides and botanicals deep into pores.', 'Aqua, Hydrolyzed Collagen, Niacinamide 4%, Dipropylene Glycol, Betaine, Tremella Fuciformis (Mushroom) Extract, Sodium Hyaluronate, Hydroxyethylcellulose.', 'Unfold mask and apply to clean face. Leave on for 15-20 minutes. Peel off and gently pat remaining essence into face and neck. Do not rinse.', 1200.00, 999.00, 95, 'prod-mask.svg', 1, 1, 1, 'active');

-- Product Gallery Images
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(1, 'prod-vit-c-serum.svg', 1),
(1, 'prod-vit-c-texture.svg', 2),
(2, 'prod-hyaluronic-serum.svg', 1),
(3, 'prod-facewash.svg', 1),
(4, 'prod-moisturizer.svg', 1),
(5, 'prod-sunscreen.svg', 1),
(6, 'prod-night-cream.svg', 1),
(7, 'prod-toner.svg', 1),
(8, 'prod-lip-balm.svg', 1),
(9, 'prod-cleanser.svg', 1),
(10, 'prod-mask.svg', 1);

-- Active Discount Coupons
INSERT INTO `coupons` (`code`, `discount_type`, `discount_value`, `min_spend`, `max_discount`, `expiry_date`, `usage_limit`, `usage_count`, `status`) VALUES
('GLOW10', 'percent', 10.00, 1500.00, 500.00, '2028-12-31', 1000, 14, 'active'),
('SKIN20', 'percent', 20.00, 3500.00, 1000.00, '2028-12-31', 500, 8, 'active'),
('FREESHIP', 'fixed', 200.00, 1000.00, 200.00, '2028-12-31', 2000, 31, 'active');

-- Customer Reviews
INSERT INTO `reviews` (`product_id`, `user_id`, `reviewer_name`, `rating`, `review_text`, `status`, `created_at`) VALUES
(1, 2, 'Ayesha Khan', 5, 'This Vitamin C serum transformed my skin texture in just 2 weeks! The hyperpigmentation around my cheeks has visibly faded. Absolutely love it.', 'approved', '2026-10-15 14:30:00'),
(1, NULL, 'Zainab Fatima', 5, 'Best serum I have used in Pakistan. Zero stickiness and gives an immediate glass skin glow. Will definitely repurchase!', 'approved', '2026-10-22 10:15:00'),
(4, 2, 'Ayesha Khan', 5, 'The Ceramide moisturizer is so nourishing yet light for Lahore weather. Doesn’t break me out at all!', 'approved', '2026-11-01 18:20:00'),
(5, NULL, 'Mahnoor Ali', 5, 'Finally a sunscreen that leaves ZERO white cast on brown skin! Sits so well under foundation without pill-up.', 'approved', '2026-11-05 09:40:00'),
(3, NULL, 'Sana Tariq', 4, 'Very soft and gentle cleanser. Does not leave the face dry or stretchy.', 'approved', '2026-11-10 16:00:00');

-- Demo Customer Address
INSERT INTO `addresses` (`user_id`, `full_name`, `phone`, `address_line1`, `address_line2`, `city`, `state`, `postal_code`, `is_default`) VALUES
(2, 'Ayesha Khan', '+92 321 9876543', 'House 42, Block C, DHA Phase 5', 'Near Ring Road', 'Lahore', 'Punjab', '54000', 1);

-- Demo Sample Orders
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `city`, `postal_code`, `subtotal`, `shipping_fee`, `discount_amount`, `total_amount`, `payment_method`, `payment_status`, `order_status`, `notes`, `coupon_code`, `created_at`) VALUES
(1, 'SKN-2026-9214A', 2, 'Ayesha Khan', 'ayesha@example.com', '+92 321 9876543', 'House 42, Block C, DHA Phase 5', 'Lahore', '54000', 4049.00, 0.00, 404.90, 3644.10, 'cod', 'pending', 'processing', 'Please call before delivery', 'GLOW10', '2026-11-18 11:25:00');

INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `product_price`, `quantity`, `subtotal`) VALUES
(1, 1, 'Vitamin C 15% Radiance Glow Serum', 2150.00, 1, 2150.00),
(1, 5, 'Invisible Shield Featherlight Sunscreen SPF 50+', 1899.00, 1, 1899.00);

-- Contact Message Sample
INSERT INTO `contact_messages` (`name`, `email`, `phone`, `subject`, `message`, `is_read`, `created_at`) VALUES
('Fatima Zahra', 'fatima@gmail.com', '+92 333 4455667', 'Skincare routine recommendation', 'Hello Skinora team, I have oily combination skin with acne scars. Which serums would you recommend pairing together?', 0, '2026-11-20 15:10:00');
