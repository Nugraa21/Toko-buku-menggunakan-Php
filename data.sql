CREATE DATABASE IF NOT EXISTS autentikasi;
USE autentikasi;

-- Disable foreign key checks for clean cleanup
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS wishlists;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS topups;
DROP TABLE IF EXISTS transaction_items;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS categories;
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

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    isbn VARCHAR(20),
    publisher VARCHAR(100),
    pages INT,
    language VARCHAR(50) DEFAULT 'Indonesia',
    price INT NOT NULL, -- Price in Tokens
    description TEXT,
    image VARCHAR(255) DEFAULT 'default_book.png',
    rating DECIMAL(2, 1) DEFAULT 0.0,
    stock INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
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

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

CREATE TABLE wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE(user_id, book_id)
);

-- Sample Users (password: password)
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO users (name, email, password, role, tokens) VALUES 
('Admin User', 'admin@toko.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 99999),
('John Doe', 'user@toko.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 50);

-- Sample Categories
INSERT INTO categories (name, slug, description) VALUES 
('Teknologi', 'teknologi', 'Buku-buku seputar pemrograman, AI, dan inovasi teknologi.'),
('Sains', 'sains', 'Eksplorasi alam semesta, fisika, dan biologi.'),
('Pengembangan Diri', 'self-development', 'Buku untuk meningkatkan kualitas hidup dan mental.'),
('Bisnis', 'bisnis', 'Strategi bisnis, keuangan, dan manajemen.'),
('Fiksi', 'fiksi', 'Novel dan cerita imajinatif.');

-- Sample Books (Prices in Tokens)
INSERT INTO books (title, author, category_id, isbn, publisher, pages, price, description, image, rating, stock) VALUES 
('The Art of Code', 'Alan Turing', 1, '978-3-16-148410-0', 'Tech Press', 350, 150, 'Sebuah buku mendalam tentang seni menulis kode yang efisien dan indah.', 'book1.png', 4.8, 20),
('Universe in a Nutshell', 'Stephen Hawking', 2, '978-0-553-80202-3', 'Bantam', 224, 250, 'Menjelajahi rahasia alam semesta dengan penjelasan yang mudah dipahami.', 'book2.png', 4.9, 15),
('Clean Architecture', 'Robert C. Martin', 1, '978-0-13-449416-6', 'Prentice Hall', 432, 300, 'Panduan arsitektur perangkat lunak yang tak lekang oleh waktu.', 'book3.png', 4.7, 10),
('Psychology of Money', 'Morgan Housel', 4, '978-0-857-19768-9', 'Harriman House', 256, 120, 'Pelajaran abadi mengenai kekayaan, ketamakan, dan kebahagiaan.', 'book4.png', 4.6, 25),
('Atomic Habits', 'James Clear', 3, '978-0-7352-1129-2', 'Avery', 320, 180, 'Perubahan kecil yang memberikan hasil luar biasa dalam hidup Anda.', 'book5.png', 4.9, 30);
