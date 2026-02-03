CREATE DATABASE IF NOT EXISTS autentikasi;
USE autentikasi;

-- Disable foreign key checks for clean cleanup
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS topups;
DROP TABLE IF EXISTS transaction_items;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    tokens INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    price INT NOT NULL, -- Price in Tokens
    description TEXT,
    image VARCHAR(255) DEFAULT 'default_book.png',
    rating DECIMAL(2, 1) DEFAULT 4.5,
    stock INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_tokens INT NOT NULL,
    status ENUM('completed') DEFAULT 'completed', -- Instant completion with tokens
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE transaction_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL,
    price_per_token INT NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

CREATE TABLE topups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample Users (password: password)
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO users (name, email, password, role, tokens) VALUES 
('Admin User', 'admin@toko.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 99999),
('John Doe', 'user@toko.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 50);

-- Sample Books (Prices in Tokens)
INSERT INTO books (title, author, price, description, image, rating, stock) VALUES 
('The Art of Code', 'Alan Turing', 150, 'Sebuah buku mendalam tentang seni menulis kode yang efisien dan indah.', 'book1.png', 4.8, 20),
('Universe in a Nutshell', 'Stephen Hawking', 250, 'Menjelajahi rahasia alam semesta dengan penjelasan yang mudah dipahami.', 'book2.png', 4.9, 15),
('Clean Architecture', 'Robert C. Martin', 300, 'Panduan arsitektur perangkat lunak yang tak lekang oleh waktu.', 'book3.png', 4.7, 10),
('Psychology of Money', 'Morgan Housel', 120, 'Pelajaran abadi mengenai kekayaan, ketamakan, dan kebahagiaan.', 'book4.png', 4.6, 25),
('Atomic Habits', 'James Clear', 180, 'Perubahan kecil yang memberikan hasil luar biasa dalam hidup Anda.', 'book5.png', 4.9, 30);
