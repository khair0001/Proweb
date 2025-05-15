-- Database creation
CREATE DATABASE IF NOT EXISTS inventory_management;
USE inventory_management;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('admin', 'manager', 'employee') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Suppliers table
CREATE TABLE IF NOT EXISTS suppliers (
    supplier_id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(100) NOT NULL,
    contact_name VARCHAR(100),
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    postal_code VARCHAR(20),
    country VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    sku VARCHAR(50) UNIQUE,
    category_id INT,
    supplier_id INT,
    unit_price DECIMAL(10, 2) NOT NULL,
    unit_cost DECIMAL(10, 2) NOT NULL,
    quantity_in_stock INT NOT NULL DEFAULT 0,
    reorder_level INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id)
);

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    postal_code VARCHAR(20),
    country VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    required_date DATE,
    shipped_date DATE,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_fee DECIMAL(10, 2) DEFAULT 0.00,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'credit_card', 'bank_transfer', 'paypal') NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    notes TEXT,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Order details table
CREATE TABLE IF NOT EXISTS order_details (
    order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    discount DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Inventory transactions table
CREATE TABLE IF NOT EXISTS inventory_transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    transaction_type ENUM('purchase', 'sale', 'adjustment', 'return') NOT NULL,
    quantity INT NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    reference_id INT COMMENT 'Can refer to order_id, supplier_id, etc.',
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'credit_card', 'bank_transfer', 'paypal') NOT NULL,
    transaction_id VARCHAR(100) COMMENT 'External payment reference',
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- Insert sample data for testing

-- Sample categories
INSERT INTO categories (name, description) VALUES
('Electronics', 'Electronic devices and accessories'),
('Clothing', 'Apparel and fashion items'),
('Furniture', 'Home and office furniture'),
('Books', 'Books, magazines, and publications'),
('Food', 'Food and beverage products');

-- Sample suppliers
INSERT INTO suppliers (company_name, contact_name, contact_email, contact_phone, address, city, state, postal_code, country) VALUES
('Tech Supplies Inc.', 'John Smith', 'john@techsupplies.com', '555-1234', '123 Tech St', 'San Francisco', 'CA', '94105', 'USA'),
('Fashion World', 'Emma Johnson', 'emma@fashionworld.com', '555-5678', '456 Style Ave', 'New York', 'NY', '10001', 'USA'),
('Furniture Plus', 'Michael Brown', 'michael@furnitureplus.com', '555-9012', '789 Comfort Rd', 'Chicago', 'IL', '60601', 'USA'),
('Book Haven', 'Sarah Davis', 'sarah@bookhaven.com', '555-3456', '321 Reader Ln', 'Boston', 'MA', '02108', 'USA'),
('Fresh Foods Co.', 'David Wilson', 'david@freshfoods.com', '555-7890', '654 Organic Blvd', 'Seattle', 'WA', '98101', 'USA');

-- Sample users
INSERT INTO users (username, password, email, first_name, last_name, role) VALUES
('admin', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'admin@example.com', 'Admin', 'User', 'admin'),
('manager', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'manager@example.com', 'Manager', 'User', 'manager'),
('employee', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'employee@example.com', 'Employee', 'User', 'employee');

-- Sample customers
INSERT INTO customers (first_name, last_name, email, phone, address, city, state, postal_code, country) VALUES
('Alice', 'Johnson', 'alice@example.com', '555-1111', '111 Customer St', 'Los Angeles', 'CA', '90001', 'USA'),
('Bob', 'Smith', 'bob@example.com', '555-2222', '222 Buyer Ave', 'Miami', 'FL', '33101', 'USA'),
('Carol', 'Williams', 'carol@example.com', '555-3333', '333 Shopper Rd', 'Dallas', 'TX', '75201', 'USA'),
('Dave', 'Brown', 'dave@example.com', '555-4444', '444 Client Ln', 'Denver', 'CO', '80201', 'USA'),
('Eve', 'Davis', 'eve@example.com', '555-5555', '555 Consumer Blvd', 'Phoenix', 'AZ', '85001', 'USA');

-- Sample products
INSERT INTO products (name, description, sku, category_id, supplier_id, unit_price, unit_cost, quantity_in_stock, reorder_level) VALUES
('Smartphone X', 'Latest smartphone with advanced features', 'PHONE-001', 1, 1, 799.99, 600.00, 50, 10),
('Laptop Pro', 'Professional laptop for business use', 'LAPTOP-001', 1, 1, 1299.99, 1000.00, 30, 5),
('Designer T-shirt', 'Premium cotton t-shirt', 'SHIRT-001', 2, 2, 29.99, 15.00, 100, 20),
('Office Desk', 'Ergonomic office desk', 'DESK-001', 3, 3, 249.99, 180.00, 15, 5),
('Bestseller Novel', 'Award-winning fiction book', 'BOOK-001', 4, 4, 19.99, 10.00, 75, 15),
('Organic Coffee', 'Premium organic coffee beans', 'COFFEE-001', 5, 5, 12.99, 8.00, 60, 20),
('Wireless Headphones', 'Noise-cancelling wireless headphones', 'AUDIO-001', 1, 1, 149.99, 100.00, 40, 10),
('Winter Jacket', 'Warm winter jacket with hood', 'JACKET-001', 2, 2, 89.99, 60.00, 35, 10),
('Bookshelf', 'Wooden bookshelf with 5 shelves', 'SHELF-001', 3, 3, 179.99, 120.00, 20, 5),
('Cookbook', 'International recipes cookbook', 'BOOK-002', 4, 4, 24.99, 15.00, 45, 10);

-- Create views for common queries

-- View for product inventory status
CREATE OR REPLACE VIEW vw_product_inventory AS
SELECT 
    p.product_id,
    p.name AS product_name,
    p.sku,
    c.name AS category,
    s.company_name AS supplier,
    p.quantity_in_stock,
    p.reorder_level,
    p.unit_cost,
    p.unit_price,
    (p.unit_price - p.unit_cost) AS profit_margin,
    ((p.unit_price - p.unit_cost) / p.unit_price * 100) AS profit_percentage,
    p.is_active
FROM 
    products p
JOIN 
    categories c ON p.category_id = c.category_id
JOIN 
    suppliers s ON p.supplier_id = s.supplier_id;

-- View for order summary
CREATE OR REPLACE VIEW vw_order_summary AS
SELECT 
    o.order_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    CONCAT(u.first_name, ' ', u.last_name) AS employee_name,
    o.order_date,
    o.shipped_date,
    o.status AS order_status,
    o.payment_status,
    o.total_amount,
    COUNT(od.product_id) AS total_products,
    SUM(od.quantity) AS total_items
FROM 
    orders o
JOIN 
    customers c ON o.customer_id = c.customer_id
JOIN 
    users u ON o.user_id = u.user_id
JOIN 
    order_details od ON o.order_id = od.order_id
GROUP BY 
    o.order_id;

-- Create stored procedures

-- Procedure to add a product with inventory transaction
DELIMITER //
CREATE PROCEDURE sp_add_product(
    IN p_name VARCHAR(100),
    IN p_description TEXT,
    IN p_sku VARCHAR(50),
    IN p_category_id INT,
    IN p_supplier_id INT,
    IN p_unit_price DECIMAL(10, 2),
    IN p_unit_cost DECIMAL(10, 2),
    IN p_quantity INT,
    IN p_reorder_level INT,
    IN p_user_id INT
)
BEGIN
    DECLARE v_product_id INT;
    
    -- Insert product
    INSERT INTO products (
        name, description, sku, category_id, supplier_id, 
        unit_price, unit_cost, quantity_in_stock, reorder_level
    ) VALUES (
        p_name, p_description, p_sku, p_category_id, p_supplier_id, 
        p_unit_price, p_unit_cost, p_quantity, p_reorder_level
    );
    
    SET v_product_id = LAST_INSERT_ID();
    
    -- Record inventory transaction
    IF p_quantity > 0 THEN
        INSERT INTO inventory_transactions (
            product_id, user_id, transaction_type, quantity, notes
        ) VALUES (
            v_product_id, p_user_id, 'purchase', p_quantity, 'Initial inventory'
        );
    END IF;
    
    SELECT v_product_id AS product_id;
END //
DELIMITER ;

-- Procedure to create an order
DELIMITER //
CREATE PROCEDURE sp_create_order(
    IN p_customer_id INT,
    IN p_user_id INT,
    IN p_required_date DATE,
    IN p_shipping_fee DECIMAL(10, 2),
    IN p_payment_method VARCHAR(20),
    IN p_notes TEXT
)
BEGIN
    DECLARE v_order_id INT;
    
    -- Create order with initial total of 0
    INSERT INTO orders (
        customer_id, user_id, required_date, shipping_fee, 
        total_amount, payment_method, notes
    ) VALUES (
        p_customer_id, p_user_id, p_required_date, p_shipping_fee, 
        0, p_payment_method, p_notes
    );
    
    SET v_order_id = LAST_INSERT_ID();
    
    SELECT v_order_id AS order_id;
END //
DELIMITER ;

-- Procedure to add product to order
DELIMITER //
CREATE PROCEDURE sp_add_order_item(
    IN p_order_id INT,
    IN p_product_id INT,
    IN p_quantity INT,
    IN p_discount DECIMAL(10, 2),
    IN p_user_id INT
)
BEGIN
    DECLARE v_unit_price DECIMAL(10, 2);
    DECLARE v_current_stock INT;
    DECLARE v_total_amount DECIMAL(10, 2);
    
    -- Get product price and check stock
    SELECT unit_price, quantity_in_stock INTO v_unit_price, v_current_stock
    FROM products
    WHERE product_id = p_product_id;
    
    -- Check if enough stock
    IF v_current_stock >= p_quantity THEN
        -- Add order detail
        INSERT INTO order_details (
            order_id, product_id, quantity, unit_price, discount
        ) VALUES (
            p_order_id, p_product_id, p_quantity, v_unit_price, p_discount
        );
        
        -- Update product stock
        UPDATE products
        SET quantity_in_stock = quantity_in_stock - p_quantity
        WHERE product_id = p_product_id;
        
        -- Record inventory transaction
        INSERT INTO inventory_transactions (
            product_id, user_id, transaction_type, quantity, 
            notes, reference_id
        ) VALUES (
            p_product_id, p_user_id, 'sale', p_quantity, 
            'Order item', p_order_id
        );
        
        -- Update order total
        SELECT SUM((unit_price - discount) * quantity) INTO v_total_amount
        FROM order_details
        WHERE order_id = p_order_id;
        
        UPDATE orders
        SET total_amount = v_total_amount
        WHERE order_id = p_order_id;
        
        SELECT 'Success' AS result;
    ELSE
        SELECT 'Insufficient stock' AS result;
    END IF;
END //
DELIMITER ;

-- Create triggers

-- Trigger to update product quantity after inventory transaction
DELIMITER //
CREATE TRIGGER trg_after_inventory_transaction
AFTER INSERT ON inventory_transactions
FOR EACH ROW
BEGIN
    DECLARE v_quantity INT;
    
    -- Determine quantity change based on transaction type
    IF NEW.transaction_type = 'purchase' OR NEW.transaction_type = 'return' THEN
        SET v_quantity = NEW.quantity;
    ELSE
        SET v_quantity = -NEW.quantity;
    END IF;
    
    -- Update product quantity if not already updated by procedure
    IF NEW.transaction_type != 'sale' THEN
        UPDATE products
        SET quantity_in_stock = quantity_in_stock + v_quantity
        WHERE product_id = NEW.product_id;
    END IF;
END //
DELIMITER ;

-- Create indexes for performance
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_supplier ON products(supplier_id);
CREATE INDEX idx_orders_customer ON orders(customer_id);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_order_details_order ON order_details(order_id);
CREATE INDEX idx_order_details_product ON order_details(product_id);
CREATE INDEX idx_inventory_product ON inventory_transactions(product_id);
CREATE INDEX idx_payments_order ON payments(order_id);
