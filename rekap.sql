-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2026 at 10:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `autentikasi`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `publisher` varchar(100) DEFAULT NULL,
  `pages` int(11) DEFAULT NULL,
  `language` varchar(50) DEFAULT 'Indonesia',
  `price` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT 'default_book.png',
  `rating` decimal(2,1) DEFAULT 0.0,
  `stock` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `category_id`, `title`, `author`, `isbn`, `publisher`, `pages`, `language`, `price`, `description`, `image`, `rating`, `stock`, `created_at`) VALUES
(6, 4, 'The Art of Code', 'Alan Turing', '978-3-16-148410-0', 'Tech Press', 350, 'Indonesia', 150, 'Sebuah buku mendalam tentang seni menulis kode yang efisien dan indah.', 'book1.png', 4.8, 1860, '2026-02-08 06:44:40'),
(7, 2, 'Universe in a Nutshell', 'Stephen Hawking', '978-0-553-80202-3', 'Bantam', 224, 'Indonesia', 250, 'Menjelajahi rahasia alam semesta dengan penjelasan yang mudah dipahami.', 'book2.png', 4.9, 13, '2026-02-08 06:44:40'),
(8, 1, 'Clean Architecture', 'Robert C. Martin', '978-0-13-449416-6', 'Prentice Hall', 432, 'Indonesia', 300, 'Panduan arsitektur perangkat lunak yang tak lekang oleh waktu.', 'book3.png', 4.7, 10, '2026-02-08 06:44:40'),
(9, 4, 'Psychology of Money', 'Morgan Housel', '978-0-857-19768-9', 'Harriman House', 256, 'Indonesia', 120, 'Pelajaran abadi mengenai kekayaan, ketamakan, dan kebahagiaan.', 'book4.png', 4.6, 25, '2026-02-08 06:44:40'),
(10, 3, 'Atomic Habits', 'James Clear', '978-0-7352-1129-2', 'Avery', 320, 'Indonesia', 180, 'Perubahan kecil yang memberikan hasil luar biasa dalam hidup Anda.', 'book5.png', 4.9, 30, '2026-02-08 06:44:40'),
(11, 2, 'Sapiens: A Brief History', 'Yuval Noah Harari', '978-0062316097', 'Harper', 443, 'Indonesia', 220, 'Riwayat singkat umat manusia dari masa prasejarah hingga modern.', '1770533300_20240928_161650.jpg', 4.7, 12, '2026-02-08 06:44:40'),
(12, 4, 'Zero to One', 'Peter Thiel', '978-0804139298', 'Crown Currency', 210, 'Indonesia', 190, 'Catatan tentang masa depan startup dan cara membangun masa depan.', '1770533306_20240928_161650.jpg', 4.5, 8, '2026-02-08 06:44:40'),
(13, 1, 'The Pragmatic Programmer', 'Andrew Hunt', '978-0201616224', 'Addison-Wesley', 352, 'Indonesia', 280, 'Perjalanan menuju penguasaan pemrograman perangkat lunak.', '1770533336_20240928_161650.jpg', 4.8, 8, '2026-02-08 06:44:40'),
(14, 5, 'Laskar Pelangi', 'Andrea Hirata', '978-979-3062-79-2', 'Bentang Pustaka', 529, 'Indonesia', 130, 'Kisah inspiratif anak-anak Belitong dalam mengejar mimpi.', '1770533341_20240928_161650.jpg', 4.9, 50, '2026-02-08 06:44:40'),
(15, 5, 'Bumi Manusia', 'Pramoedya Ananta Toer', '978-979-97312-3-4', 'Lentera Dipantara', 535, 'Indonesia', 160, 'Roman sejarah pergerakan nasional di awal abad 20.', '1770533355_20240928_161706.jpg', 5.0, 25, '2026-02-08 06:44:40'),
(16, 4, 'Rich Dad Poor Dad', 'Robert Kiyosaki', '978-1612680194', 'Plata', 336, 'Indonesia', 140, 'Apa yang diajarkan orang kaya pada anak mereka tentang uang.', '1770533360_20240928_161650.jpg', 4.6, 40, '2026-02-08 06:44:40'),
(17, 3, 'Deep Work', 'Cal Newport', '978-1455586691', 'Grand Central', 304, 'Indonesia', 175, 'Aturan untuk sukses terfokus di dunia yang penuh gangguan.', '1770533366_20240928_161706.jpg', 4.4, 20, '2026-02-08 06:44:40'),
(18, 2, 'Cosmos', 'Carl Sagan', '978-0345331309', 'Ballantine Books', 365, 'Indonesia', 210, 'Perjalanan pribadi melalui alam semesta yang luas.', '1770533371_20240928_161650.jpg', 4.9, 10, '2026-02-08 06:44:40'),
(19, 1, 'Design Patterns', 'Erich Gamma', '978-0201633610', 'Pearson', 395, 'Indonesia', 350, 'Elemen-elemen software berorientasi objek yang dapat digunakan kembali.', '1770533378_2025-01-06_09.36.20.png', 4.7, 5, '2026-02-08 06:44:40'),
(20, 3, 'Filosofi Teras', 'Henry Manampiring', '978-602-412-518-9', 'Kompas', 312, 'Indonesia', 95, 'Penerapan stoisisme dalam kehidupan sehari-hari yang penuh kecemasan.', '1770533396_2025-01-06_09.37.30.png', 4.8, 100, '2026-02-08 06:44:40'),
(21, 3, 'Thinking, Fast and Slow', 'Daniel Kahneman', '978-0374275631', 'Farrar, Straus and Giroux', 499, 'Indonesia', 230, 'Dua sistem yang mendorong cara kita berpikir dan mengambil keputusan.', '1770533401_20240928_161650.jpg', 4.5, 13, '2026-02-08 06:44:40'),
(22, 5, 'Harry Potter and the Sorcerers Stone', 'J.K. Rowling', '978-0590353427', 'Scholastic', 309, 'Indonesia', 180, 'Petualangan pertama Harry Potter di sekolah sihir Hogwarts.', '1770533409_2025-01-06_09.37.30.png', 4.9, 60, '2026-02-08 06:44:40'),
(23, 1, 'Cracking the Coding Interview', 'Gayle Laakmann McDowell', '978-0984782857', 'CareerCup', 687, 'Indonesia', 400, '189 pertanyaan dan solusi pemrograman untuk wawancara kerja.', '1770533348_20240928_161706.jpg', 3.0, 7, '2026-02-08 06:44:40'),
(24, 3, 'Ikigai', 'Hector Garcia', '978-0143130727', 'Penguin Life', 208, 'Indonesia', 110, 'Rahasia hidup bahagia dan panjang umur ala orang Jepang.', '1770533327_GbIAIrNWAAALkko.jpg', 4.6, 35, '2026-02-08 06:44:40'),
(25, 2, 'Elon Musk', 'Walter Isaacson', '978-1982181284', 'Simon & Schuster', 688, 'Indonesia', 320, 'Biografi inovator di balik SpaceX dan Tesla.', '1770533318_˚꒦꒷🖤꒷．𝐖𝐀𝐋𝐋𝐏𝐀𝐏𝐄𝐑𝐒˚﹆.jpeg', 4.7, 22, '2026-02-08 06:44:40');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Teknologi', 'teknologi', 'Buku-buku seputar pemrograman, AI, dan inovasi teknologi.'),
(2, 'Sains', 'sains', 'Eksplorasi alam semesta, fisika, dan biologi.'),
(3, 'Pengembangan Diri', 'self-development', 'Buku untuk meningkatkan kualitas hidup dan mental.'),
(4, 'Bisnis', 'bisnis', 'Strategi bisnis, keuangan, dan manajemen.'),
(5, 'Fiksi', 'fiksi', 'Novel dan cerita imajinatif.');

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `bank_name` varchar(50) NOT NULL,
  `bank_account` varchar(50) NOT NULL,
  `account_holder` varchar(100) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `refunds`
--

INSERT INTO `refunds` (`id`, `user_id`, `amount`, `bank_name`, `bank_account`, `account_holder`, `reason`, `status`, `admin_note`, `created_at`) VALUES
(1, 4, 2000, 'Mandiri', '3456785', 'bri', 'tfj', 'approved', 'Refund disetujui dan ditransfer.', '2026-02-08 08:37:50'),
(2, 4, 2350, 'BNI', '3456785', 'bri', 'rhjrdjtyjtf', 'approved', 'Refund disetujui dan ditransfer.', '2026-02-08 08:39:13'),
(3, 4, 50000, 'BCA', '3456785', 'bri', 'rt', 'approved', 'Refund disetujui dan ditransfer.', '2026-02-08 08:45:00'),
(4, 4, 2000, 'BCA', '3456785', 'bca', 'ww', 'approved', 'Refund disetujui dan ditransfer.', '2026-02-08 08:48:19');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `book_id`, `rating`, `comment`, `created_at`) VALUES
(2, 4, 6, 4, 'g', '2026-02-08 07:35:07'),
(3, 4, 7, 1, 'd', '2026-02-08 07:35:14'),
(4, 4, 6, 4, 'r', '2026-02-08 07:35:30'),
(5, 4, 6, 2, 'e', '2026-02-08 07:35:33'),
(6, 4, 7, 4, 'x', '2026-02-08 08:00:18'),
(7, 1, 23, 3, 'f', '2026-02-08 08:19:06'),
(8, 4, 10, 5, 'fw', '2026-02-08 08:22:20');

-- --------------------------------------------------------

--
-- Table structure for table `topups`
--

CREATE TABLE `topups` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topups`
--

INSERT INTO `topups` (`id`, `user_id`, `amount`, `status`, `created_at`) VALUES
(2, 4, 200, 'approved', '2026-02-08 06:35:02'),
(3, 4, 100, 'rejected', '2026-02-08 06:35:34'),
(4, 5, 200, 'approved', '2026-02-08 07:19:31'),
(5, 4, 250, 'approved', '2026-02-08 07:42:49'),
(6, 4, 50, 'approved', '2026-02-08 07:44:52'),
(7, 4, 20000, 'approved', '2026-02-08 07:45:13'),
(8, 4, 250, 'approved', '2026-02-08 07:59:56'),
(9, 4, 100, 'approved', '2026-02-08 08:25:22'),
(10, 4, 1000, 'approved', '2026-02-08 08:28:08'),
(11, 4, 250, 'approved', '2026-02-08 08:29:03'),
(12, 4, 1000, 'rejected', '2026-02-08 08:31:44'),
(13, 4, 500, 'approved', '2026-02-08 08:32:09'),
(14, 4, 250, 'approved', '2026-02-08 08:35:56'),
(15, 4, 100, 'approved', '2026-02-08 08:46:02'),
(16, 4, 1000, 'approved', '2026-02-08 09:13:32'),
(17, 4, 1000, 'approved', '2026-02-08 09:14:24'),
(18, 4, 250, 'pending', '2026-02-08 09:32:46');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_tokens` int(11) NOT NULL,
  `status` enum('completed') DEFAULT 'completed',
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `total_tokens`, `status`, `transaction_date`) VALUES
(3, 4, 250, 'completed', '2026-02-08 07:16:07'),
(4, 4, 15000, 'completed', '2026-02-08 07:48:45'),
(5, 4, 6000, 'completed', '2026-02-08 07:49:11'),
(6, 4, 250, 'completed', '2026-02-08 07:49:20'),
(7, 4, 2130, 'completed', '2026-02-08 09:14:34');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_token` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`id`, `transaction_id`, `book_id`, `quantity`, `price_per_token`) VALUES
(3, 3, 7, 1, 250),
(4, 4, 6, 100, 150),
(5, 5, 6, 40, 150),
(6, 6, 7, 1, 250),
(7, 7, 12, 10, 190),
(8, 7, 21, 1, 230);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `tokens` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `tokens`, `created_at`) VALUES
(1, 'Admin User', 'admin@toko.com', '$2y$10$2M6mNgBZIiAhLu86oOj9z.TERP98cT29sPj9Olxeso3Swb4c8m54S', 'admin', 99999, '2026-02-08 06:18:25'),
(4, 'nugraa', 'ludang081328@gmail.com', '$2y$10$L0BNZzGAxl9xMlSLi4F.bOE.FNooOcf7C.HbkUyqaLc6JJSICtPUG', 'user', 270, '2026-02-08 06:25:19'),
(5, 'wibu', 'jkfhskfsh@dkjfhdf.sdsd', '$2y$10$OcHfz1o/cVdC0/tv448k9uOgKWhTH6pmNQvjzmhIPJvPxLPj9WHEu', 'user', 200, '2026-02-08 06:32:06'),
(6, 'Johann', 'johan@gmail.com', '$2y$10$yj/ltloPkiO3ci8MDGP23Oni1f8awtWpgx.Bch6nBg7LUkkOLRUpa', 'user', 0, '2026-02-08 09:18:40');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`id`, `user_id`, `book_id`, `created_at`) VALUES
(3, 4, 8, '2026-02-08 07:14:38'),
(4, 4, 12, '2026-02-08 07:14:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `topups`
--
ALTER TABLE `topups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`book_id`),
  ADD KEY `book_id` (`book_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `topups`
--
ALTER TABLE `topups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `topups`
--
ALTER TABLE `topups`
  ADD CONSTRAINT `topups_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
