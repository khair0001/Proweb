-- Database structure for ReCycleIt

-- Create database
CREATE DATABASE IF NOT EXISTS recycleit;
USE recycleit;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    profile_image VARCHAR(255),
    address TEXT,
    city VARCHAR(100),
    province VARCHAR(100),
    postal_code VARCHAR(20),
    country VARCHAR(50) DEFAULT 'Indonesia',
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    payment_method ENUM('bank_transfer', 'e_wallet', 'cash') DEFAULT 'cash',
    bank_name VARCHAR(100),
    account_number VARCHAR(50),
    account_holder VARCHAR(100),
    wallet_provider VARCHAR(50),
    wallet_number VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Items table
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category_id INT NOT NULL,
    price DECIMAL(12, 2),
    condition_status ENUM('baru', 'sangat_bagus', 'bagus', 'cukup_bagus', 'kurang_bagus') NOT NULL,
    listing_type ENUM('sale', 'auction', 'donation') NOT NULL,
    status ENUM('active', 'sold', 'donated', 'inactive') DEFAULT 'active',
    starting_bid DECIMAL(12, 2),
    end_date DATETIME,
    pickup_home TINYINT(1) DEFAULT 0,
    pickup_warehouse TINYINT(1) DEFAULT 0,
    cod TINYINT(1) DEFAULT 0,
    warehouse_id INT,
    location VARCHAR(255),
    city VARCHAR(100),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Item images table
CREATE TABLE IF NOT EXISTS item_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_main TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
);

-- Bids table for auctions
CREATE TABLE IF NOT EXISTS bids (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    user_id INT NOT NULL,
    bid_amount DECIMAL(12, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    seller_id INT NOT NULL,
    buyer_id INT NOT NULL,
    transaction_type ENUM('sale', 'auction', 'donation') NOT NULL,
    price DECIMAL(12, 2),
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    shipping_method ENUM('pickup_home', 'pickup_warehouse', 'cod') NOT NULL,
    shipping_address TEXT,
    payment_method ENUM('bank_transfer', 'e_wallet', 'cash') NOT NULL,
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    reviewed_id INT NOT NULL,
    item_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
);

-- Warehouses table
CREATE TABLE IF NOT EXISTS warehouses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    capacity INT NOT NULL,
    available_space INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('transaction', 'bid', 'system', 'review') NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    related_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default categories
INSERT INTO categories (name, icon) VALUES
('Elektronik', 'fas fa-laptop'),
('Fashion', 'fas fa-tshirt'),
('Buku', 'fas fa-book'),
('Mainan', 'fas fa-gamepad'),
('Dapur', 'fas fa-utensils'),
('Furnitur', 'fas fa-couch'),
('Lainnya', 'fas fa-box');

-- Insert default warehouses
INSERT INTO warehouses (name, address, city, province, postal_code, latitude, longitude, capacity, available_space) VALUES
('Gudang Jakarta Pusat', 'Jl. Tanah Abang No. 10', 'Jakarta Pusat', 'DKI Jakarta', '10110', -6.186486, 106.816666, 1000, 1000),
('Gudang Jakarta Selatan', 'Jl. Kemang Raya No. 25', 'Jakarta Selatan', 'DKI Jakarta', '12730', -6.260697, 106.814095, 1000, 1000),
('Gudang Jakarta Barat', 'Jl. Puri Indah Raya No. 15', 'Jakarta Barat', 'DKI Jakarta', '11610', -6.186486, 106.716666, 1000, 1000),
('Gudang Jakarta Timur', 'Jl. Raden Inten No. 30', 'Jakarta Timur', 'DKI Jakarta', '13350', -6.246486, 106.916666, 1000, 1000),
('Gudang Jakarta Utara', 'Jl. Pluit Raya No. 20', 'Jakarta Utara', 'DKI Jakarta', '14450', -6.126486, 106.816666, 1000, 1000);
