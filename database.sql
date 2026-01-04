-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    icon VARCHAR(50) DEFAULT 'fa-box',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default_product.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Insert Sample Categories
INSERT INTO categories (name, slug, icon) VALUES
('Games', 'games', 'fa-gamepad'),
('Electronics', 'electronics', 'fa-tv'),
('Mobile Phones', 'mobile-phones', 'fa-mobile-alt'),
('Computers', 'computers', 'fa-laptop'),
('Everyday Tech', 'everyday-tech', 'fa-plug')
ON DUPLICATE KEY UPDATE name=name;

-- Insert Sample Products
-- Assuming IDs 1-5 correspond to the order above
INSERT INTO products (category_id, name, description, price, image) VALUES
(1, 'PlayStation 5', 'Next-gen gaming console with 825GB SSD', 450000.00, 'ps5.jpg'),
(1, 'Xbox Series X', 'The fastest, most powerful Xbox ever', 420000.00, 'xbox.jpg'),
(2, 'Samsung 55" 4K TV', 'Crystal UHD Smart TV', 350000.00, 'tv.jpg'),
(3, 'iPhone 15 Pro', 'Titanium design, A17 Pro chip', 1200000.00, 'iphone15.jpg'),
(3, 'Samsung Galaxy S24', 'AI-powered smartphone', 1100000.00, 's24.jpg'),
(4, 'MacBook Air M2', 'Supercharged by M2', 1400000.00, 'macbook.jpg'),
(4, 'HP Pavilion 15', 'Reliable laptop for work and play', 450000.00, 'hp.jpg'),
(5, 'Anker PowerBank', '20000mAh Portable Charger', 35000.00, 'powerbank.jpg'),
(5, 'USB-C Cable', 'Durable fast charging cable', 5000.00, 'cable.jpg');

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
