# =============================================================================
# ICS 2203 – Capstone Project  |  Database Schema & Seed Data
# =============================================================================
# Run once:  mysql -u root -p ics2203_capstone < schema.sql
# =============================================================================

# ── Create & select database ─────────────────────────────────────────────────
CREATE DATABASE IF NOT EXISTS ics2203_capstone
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ics2203_capstone;

# ── 1. users ─────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,                   -- bcrypt via password_hash()
    role          ENUM('admin','user') NOT NULL DEFAULT 'user',
    theme_pref    ENUM('light','dark') NOT NULL DEFAULT 'light',
    lang_pref     ENUM('en','fr','es') NOT NULL DEFAULT 'en',
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

# ── 2. categories ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

# ── 3. products ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS products (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(200) NOT NULL,
    description TEXT,
    price       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock       INT UNSIGNED NOT NULL DEFAULT 0,
    category_id INT UNSIGNED NOT NULL,
    image_url   VARCHAR(500) DEFAULT NULL,               -- optional product thumbnail URL
    created_by  INT UNSIGNED NOT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_category FOREIGN KEY (category_id) REFERENCES categories(id),
    CONSTRAINT fk_product_creator  FOREIGN KEY (created_by)  REFERENCES users(id)
) ENGINE=InnoDB;

# ── Migration: add image_url to existing installs ────────────────────────────
# Run this if you already have the table created without image_url:
# ALTER TABLE products ADD COLUMN image_url VARCHAR(500) DEFAULT NULL AFTER stock;

# ── 4. audit_log ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS audit_log (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    action     ENUM('CREATE','UPDATE','DELETE') NOT NULL,
    entity     VARCHAR(50)  NOT NULL,                      -- e.g. 'product'
    entity_id  INT UNSIGNED NOT NULL,
    details    TEXT,                                        -- JSON snapshot (optional)
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

# ── Seed: categories ─────────────────────────────────────────────────────────
INSERT INTO categories (name) VALUES
    ('Electronics'),
    ('Clothing'),
    ('Home & Garden'),
    ('Sports'),
    ('Books');

# ── Seed: admin user ─────────────────────────────────────────────────────────
# Password for this seed account is:  admin123
# The hash below was generated with: password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO users (name, email, password_hash, role) VALUES
    ('Admin User',
     'admin@example.com',
     '$2y$10$YourHashHere_ReplaceWithActualBcryptHash000000000000000',
     'admin');

# ── Seed: sample products ────────────────────────────────────────────────────
INSERT INTO products (name, description, price, stock, category_id, created_by) VALUES
    ('Wireless Headphones',  'Noise-cancelling Bluetooth headphones', 89.99,  45, 1, 1),
    ('Running Shoes',        'Lightweight marathon trainers',         129.50, 30, 4, 1),
    ('Desk Lamp',            'Adjustable LED desk lamp, 3 modes',    34.00,  60, 3, 1),
    ('Python Cookbook',       '3rd edition – advanced recipes',       42.75,  20, 5, 1),
    ('Cotton T-Shirt',       'Organic cotton, available in 5 sizes', 19.99,  100,2, 1);

# ── NOTE: The original seed products above used the old schema (no image_url).
# ── Replace them with the Kenyan product seeds below (delete old INSERT first):

# ── Seed: Kenyan products with images ────────────────────────────────────────
# DELETE FROM products;  -- run this first if re-seeding a fresh DB
# INSERT INTO products (name, description, price, stock, category_id, created_by, image_url) VALUES
#     ('Kitenge Wrap Dress',       'Vibrant hand-printed Kitenge fabric dress. Nairobi-made.',           2800.00, 40,  2, 1, 'https://images.unsplash.com/photo-1590735213920-68192a487bc2?w=400&q=80'),
#     ('Ankara Print Shirt – Men', 'Bold Ankara-print short-sleeve shirt, 100% cotton.',                1950.00, 55,  2, 1, 'https://images.unsplash.com/photo-1594938298603-c8148c4b4057?w=400&q=80'),
#     ('Kitenge Tote Bag',         'Handcrafted tote bag made from colourful Kitenge fabric.',           850.00,  80,  2, 1, 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400&q=80'),
#     ('Safari Field Hat',         'Wide-brim khaki hat with UV protection. Essential for safari.',      1200.00, 35,  4, 1, 'https://images.unsplash.com/photo-1521369909029-2afed882baee?w=400&q=80'),
#     ('Maasai Beaded Bracelet',   'Hand-beaded by Maasai artisans in Kajiado County.',                  650.00,  120, 4, 1, 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=400&q=80'),
#     ('Maasai Beaded Necklace',   'Traditional Maasai beadwork necklace. Fair-trade certified.',        1450.00, 60,  3, 1, 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=400&q=80'),
#     ('Soapstone Carved Bowl',    'Hand-carved Kisii soapstone bowl from Tabaka, Western Kenya.',       1100.00, 45,  3, 1, 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=400&q=80'),
#     ('Sisal Woven Basket',       'Traditional Kenyan sisal basket woven by women artisans.',           780.00,  90,  3, 1, 'https://images.unsplash.com/photo-1606760227091-3dd870d97f1d?w=400&q=80');
