-- Create database
CREATE DATABASE IF NOT EXISTS reshina_db;
USE reshina_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) DEFAULT 'default_avatar.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(50) NOT NULL
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    location VARCHAR(100) NOT NULL,
    type ENUM('sell', 'donation', 'auction') NOT NULL,
    category_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('active', 'sold', 'reserved') DEFAULT 'active',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create product_images table
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

ALTER TABLE users 
ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS address TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS city VARCHAR(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS bio TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS profile_pic VARCHAR(255) DEFAULT '../assets/image/user.png';

-- Insert sample user (password: password123)
INSERT INTO users (name, email, password) VALUES 
('User Demo', 'user@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample products
INSERT INTO products (title, description, price, location, type, category_id, user_id) VALUES 
('Sepatu Nike Air Max', 'Sepatu Nike Air Max bekas pakai 3 bulan, kondisi 90% masih bagus', 800000, 'Jakarta Selatan', 'sell', 2, 1),
('Koleksi Buku Pelajaran SD', 'Koleksi lengkap buku pelajaran SD kelas 1-6 untuk didonasikan', 0, 'Bandung', 'donation', 7, 1),
('Laptop Asus ROG 2019', 'Laptop gaming Asus ROG 2019, spek i7, RAM 16GB, SSD 512GB', 5500000, 'Surabaya', 'sell', 1, 1),
('Jam Tangan Fossil Gen 5', 'Jam tangan Fossil Gen 5 kondisi mulus, dilelang mulai dari 1.2jt', 1200000, 'Yogyakarta', 'auction', 2, 1);

-- Insert sample product images
INSERT INTO product_images (product_id, image_path, is_primary) VALUES 
(1, 'product1.jpg', TRUE),
(2, 'product2.jpg', TRUE),
(3, 'product3.jpg', TRUE),
(4, 'product4.jpg', TRUE);
