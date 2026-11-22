<?php
/**
 * Skinora - Database Configuration & PDO Connection
 * Brand: Skinora - Discover Your Natural Glow
 * Supports both MySQL (XAMPP / Production) and automatic SQLite local dev fallback.
 */

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'skinora_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a singleton PDO database connection
 * @return PDO
 */
function getDBConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $mysqlDsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ];

        // 1. Try connecting to MySQL (XAMPP / Live Hosting)
        try {
            $pdo = new PDO($mysqlDsn, DB_USER, DB_PASS, $options);
            $pdo->exec("SET NAMES " . DB_CHARSET);
            return $pdo;
        } catch (PDOException $mysqlEx) {
            // 2. If MySQL is not running locally, seamlessly use SQLite so the website runs out-of-the-box!
            $sqliteDir = __DIR__ . '/../database';
            if (!is_dir($sqliteDir)) {
                mkdir($sqliteDir, 0777, true);
            }
            $sqliteFile = $sqliteDir . '/skinora.sqlite';
            $isNewDb = !file_exists($sqliteFile) || filesize($sqliteFile) === 0;

            try {
                $pdo = new PDO("sqlite:" . $sqliteFile, null, null, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);

                // Register MySQL helper functions for SQLite compatibility
                $pdo->sqliteCreateFunction('NOW', function() { return date('Y-m-d H:i:s'); });
                $pdo->sqliteCreateFunction('CURDATE', function() { return date('Y-m-d'); });

                // If SQLite database is newly created, automatically seed it with all tables & demo data
                if ($isNewDb) {
                    initSqliteSchema($pdo);
                }

                return $pdo;
            } catch (Exception $e) {
                die("Database connection failed: " . htmlspecialchars($e->getMessage()));
            }
        }
    }

    return $pdo;
}

/**
 * Initializes SQLite schema and seeds demo data if MySQL is not present
 */
function initSqliteSchema(PDO $db): void {
    $schema = "
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        full_name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        phone TEXT,
        role TEXT NOT NULL DEFAULT 'customer',
        status TEXT NOT NULL DEFAULT 'active',
        created_at TEXT DEFAULT (datetime('now')),
        updated_at TEXT DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        description TEXT,
        image TEXT,
        is_featured INTEGER NOT NULL DEFAULT 0,
        status TEXT NOT NULL DEFAULT 'active',
        created_at TEXT DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category_id INTEGER,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        sku TEXT UNIQUE,
        short_description TEXT,
        full_description TEXT,
        ingredients TEXT,
        how_to_use TEXT,
        price REAL NOT NULL,
        sale_price REAL DEFAULT NULL,
        stock_quantity INTEGER NOT NULL DEFAULT 0,
        main_image TEXT,
        is_featured INTEGER NOT NULL DEFAULT 0,
        is_best_seller INTEGER NOT NULL DEFAULT 0,
        is_new_arrival INTEGER NOT NULL DEFAULT 1,
        status TEXT NOT NULL DEFAULT 'active',
        created_at TEXT DEFAULT (datetime('now')),
        updated_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL
    );

    CREATE TABLE IF NOT EXISTS product_images (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
        image_url TEXT NOT NULL,
        sort_order INTEGER NOT NULL DEFAULT 0,
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS addresses (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        full_name TEXT NOT NULL,
        phone TEXT NOT NULL,
        address_line1 TEXT NOT NULL,
        address_line2 TEXT,
        city TEXT NOT NULL,
        state TEXT,
        postal_code TEXT,
        is_default INTEGER NOT NULL DEFAULT 0,
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS coupons (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        code TEXT NOT NULL UNIQUE,
        discount_type TEXT NOT NULL DEFAULT 'percent',
        discount_value REAL NOT NULL,
        min_spend REAL NOT NULL DEFAULT 0.00,
        max_discount REAL,
        expiry_date TEXT,
        usage_limit INTEGER,
        usage_count INTEGER NOT NULL DEFAULT 0,
        status TEXT NOT NULL DEFAULT 'active',
        created_at TEXT DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS cart (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        session_id TEXT,
        created_at TEXT DEFAULT (datetime('now')),
        updated_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS cart_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cart_id INTEGER NOT NULL,
        product_id INTEGER NOT NULL,
        quantity INTEGER NOT NULL DEFAULT 1,
        price REAL NOT NULL,
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (cart_id) REFERENCES cart (id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS wishlist (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        product_id INTEGER NOT NULL,
        created_at TEXT DEFAULT (datetime('now')),
        UNIQUE (user_id, product_id),
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_number TEXT NOT NULL UNIQUE,
        user_id INTEGER,
        customer_name TEXT NOT NULL,
        customer_email TEXT NOT NULL,
        customer_phone TEXT NOT NULL,
        shipping_address TEXT NOT NULL,
        city TEXT NOT NULL,
        postal_code TEXT,
        subtotal REAL NOT NULL,
        shipping_fee REAL NOT NULL DEFAULT 0.00,
        discount_amount REAL NOT NULL DEFAULT 0.00,
        total_amount REAL NOT NULL,
        payment_method TEXT NOT NULL DEFAULT 'cod',
        payment_status TEXT NOT NULL DEFAULT 'pending',
        order_status TEXT NOT NULL DEFAULT 'pending',
        notes TEXT,
        coupon_code TEXT,
        created_at TEXT DEFAULT (datetime('now')),
        updated_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
    );

    CREATE TABLE IF NOT EXISTS order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL,
        product_id INTEGER,
        product_name TEXT NOT NULL,
        product_price REAL NOT NULL,
        quantity INTEGER NOT NULL DEFAULT 1,
        subtotal REAL NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL
    );

    CREATE TABLE IF NOT EXISTS reviews (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
        user_id INTEGER,
        reviewer_name TEXT NOT NULL,
        rating INTEGER NOT NULL DEFAULT 5,
        review_text TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'approved',
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS contact_messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT,
        subject TEXT NOT NULL,
        message TEXT NOT NULL,
        is_read INTEGER NOT NULL DEFAULT 0,
        created_at TEXT DEFAULT (datetime('now'))
    );

    CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT NOT NULL UNIQUE,
        status TEXT NOT NULL DEFAULT 'active',
        subscribed_at TEXT DEFAULT (datetime('now'))
    );
    ";

    $db->exec($schema);

    // Seed Data
    $pass = password_hash('admin123', PASSWORD_DEFAULT);
    $custPass = password_hash('customer123', PASSWORD_DEFAULT);

    $db->exec("
        INSERT OR IGNORE INTO users (id, full_name, email, password, phone, role, status) VALUES
        (1, 'Skinora Admin', 'admin@skinora.com', '{$pass}', '+92 300 1234567', 'admin', 'active'),
        (2, 'Ayesha Khan', 'ayesha@example.com', '{$custPass}', '+92 321 9876543', 'customer', 'active');

        INSERT OR IGNORE INTO categories (id, name, slug, description, image, is_featured, status) VALUES
        (1, 'Serums', 'serums', 'Potent, active formulations for intense glow, hydration and repair.', 'cat-serums.svg', 1, 'active'),
        (2, 'Moisturizers', 'moisturizers', 'Nourishing creams and lightweight gels to lock in vital skin hydration.', 'cat-moisturizers.svg', 1, 'active'),
        (3, 'Face Wash', 'face-wash', 'Gentle, sulphate-free cleansers that balance and refresh your skin.', 'cat-facewash.svg', 1, 'active'),
        (4, 'Sunscreens', 'sunscreens', 'Broad-spectrum SPF protection leaving zero white cast and maximum defense.', 'cat-sunscreen.svg', 1, 'active'),
        (5, 'Toners', 'toners', 'Alcohol-free clarifying and soothing floral waters for radiant balance.', 'cat-toners.svg', 1, 'active'),
        (6, 'Face Masks', 'face-masks', 'Intensive spa-grade sheet masks and clay treatments for rejuvenation.', 'cat-masks.svg', 1, 'active'),
        (7, 'Lip Care', 'lip-care', 'Ultra-hydrating peptide balms and organic butter treatments.', 'cat-lipcare.svg', 1, 'active'),
        (8, 'Cleansers', 'cleansers', 'Nourishing botanical cleansing oils and micellar melts.', 'cat-cleansers.svg', 1, 'active');

        INSERT OR IGNORE INTO products (id, category_id, name, slug, sku, short_description, full_description, ingredients, how_to_use, price, sale_price, stock_quantity, main_image, is_featured, is_best_seller, is_new_arrival, status) VALUES
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

        INSERT OR IGNORE INTO coupons (code, discount_type, discount_value, min_spend, max_discount, expiry_date, usage_limit, usage_count, status) VALUES
        ('GLOW10', 'percent', 10.00, 1500.00, 500.00, '2028-12-31', 1000, 14, 'active'),
        ('SKIN20', 'percent', 20.00, 3500.00, 1000.00, '2028-12-31', 500, 8, 'active'),
        ('FREESHIP', 'fixed', 200.00, 1000.00, 200.00, '2028-12-31', 2000, 31, 'active');

        INSERT OR IGNORE INTO reviews (product_id, user_id, reviewer_name, rating, review_text, status, created_at) VALUES
        (1, 2, 'Ayesha Khan', 5, 'This Vitamin C serum transformed my skin texture in just 2 weeks! The hyperpigmentation around my cheeks has visibly faded. Absolutely love it.', 'approved', '2026-10-15 14:30:00'),
        (1, NULL, 'Zainab Fatima', 5, 'Best serum I have used in Pakistan. Zero stickiness and gives an immediate glass skin glow. Will definitely repurchase!', 'approved', '2026-10-22 10:15:00'),
        (4, 2, 'Ayesha Khan', 5, 'The Ceramide moisturizer is so nourishing yet light for Lahore weather. Doesn’t break me out at all!', 'approved', '2026-11-01 18:20:00'),
        (5, NULL, 'Mahnoor Ali', 5, 'Finally a sunscreen that leaves ZERO white cast on brown skin! Sits so well under foundation without pill-up.', 'approved', '2026-11-05 09:40:00'),
        (3, NULL, 'Sana Tariq', 4, 'Very soft and gentle cleanser. Does not leave the face dry or stretchy.', 'approved', '2026-11-10 16:00:00');
    ");
}
