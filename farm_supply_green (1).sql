-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 28, 2026 at 11:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `farm_supply_green`
--

-- --------------------------------------------------------

--
-- Table structure for table `animals`
--

CREATE TABLE `animals` (
  `id` int(11) NOT NULL,
  `animal_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `image_path` varchar(255) DEFAULT 'https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=400',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `animals`
--

INSERT INTO `animals` (`id`, `animal_name`, `quantity`, `image_path`, `updated_at`) VALUES
(1, 'Chicken', 100, 'https://images.unsplash.com/photo-1546445317-29f4545e9d53?w=400', '2026-03-26 15:29:36'),
(2, 'Pig', 50, 'uploads/1774649230_download.jpg', '2026-03-27 22:07:10'),
(3, 'Boer Goat', 50, 'uploads/1774649267_download (2).jpg', '2026-03-27 22:07:47');

-- --------------------------------------------------------

--
-- Table structure for table `birds`
--

CREATE TABLE `birds` (
  `id` int(11) NOT NULL,
  `breed` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `age_weeks` int(11) DEFAULT 0,
  `health_status` varchar(50) DEFAULT 'Healthy',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `birds`
--

INSERT INTO `birds` (`id`, `breed`, `type`, `quantity`, `age_weeks`, `health_status`, `created_at`) VALUES
(1, 'Local', 'Layer', 100, 4, 'Healthy', '2026-03-27 17:04:51');

-- --------------------------------------------------------

--
-- Table structure for table `crops`
--

CREATE TABLE `crops` (
  `id` int(11) NOT NULL,
  `crop_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `image_path` varchar(255) DEFAULT 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=400',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `crops`
--

INSERT INTO `crops` (`id`, `crop_name`, `quantity`, `image_path`, `created_at`) VALUES
(1, 'Bananas', 100, 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=400', '2026-03-26 15:29:36'),
(2, 'Maize', 100, 'uploads/1774550064_images.jpg', '2026-03-26 18:34:24');

-- --------------------------------------------------------

--
-- Table structure for table `harvest_logs`
--

CREATE TABLE `harvest_logs` (
  `id` int(11) NOT NULL,
  `crop_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `harvest_date` date NOT NULL,
  `logged_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produce`
--

CREATE TABLE `produce` (
  `id` int(11) NOT NULL,
  `produce_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `image_path` varchar(255) DEFAULT 'https://images.unsplash.com/photo-1606787366850-de6330128bfc?w=400',
  `crop_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `produce`
--

INSERT INTO `produce` (`id`, `produce_name`, `quantity`, `image_path`, `crop_id`) VALUES
(1, 'Milk', 44, 'https://images.unsplash.com/photo-1606787366850-de6330128bfc?w=400', NULL),
(2, 'Meat', 20, 'uploads/1774540153_meat.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `joined_date` date DEFAULT curdate(),
  `status` varchar(20) DEFAULT 'Active',
  `phone` varchar(20) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT 0.00,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `role`, `joined_date`, `status`, `phone`, `salary`, `photo`) VALUES
(3, 'Mr. Martin Ssaazi', 'Farm Manager', '2026-03-26', 'Active', '+256 709707785', 1000000.00, '1774549733_MrCarlPNkangi.jpeg'),
(4, 'Carl Peter Nkangi', 'Vet Technician', '2026-03-28', 'Active', '+256 709707785', 890000.00, '1774649510_CarlPeterNkangi.png');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(150) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `category` enum('Wholesaler','Retailer','Export','Local Market') DEFAULT 'Local Market',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `contact_person`, `phone`, `email`, `address`, `category`, `created_at`, `status`) VALUES
(1, 'Fresh Mart Uganda', 'Alice Nakato', '+256 770 123456', NULL, NULL, 'Wholesaler', '2026-03-26 15:51:23', 'active'),
(2, 'Sama Tech', 'Martin', '+256 776 955 433', NULL, NULL, 'Wholesaler', '2026-03-26 16:18:53', 'active'),
(3, 'Bri Suppliers', 'Nakabonge Bridget', '+256 751149737', NULL, NULL, 'Retailer', '2026-03-27 15:31:21', 'active'),
(4, 'Agro Based Suppliers', 'Ddumba Richard', '+256 708 597 985', NULL, NULL, 'Local Market', '2026-03-27 15:46:02', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_orders`
--

CREATE TABLE `supplier_orders` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `produce_id` int(11) NOT NULL,
  `order_quantity` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `supplier_orders`
--

INSERT INTO `supplier_orders` (`id`, `supplier_id`, `produce_id`, `order_quantity`, `order_date`) VALUES
(1, 1, 1, 11, '2026-03-26 18:40:05'),
(2, 4, 2, 10, '2026-03-27 22:08:46');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_produce`
--

CREATE TABLE `supplier_produce` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `produce_id` int(11) NOT NULL,
  `last_delivery_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Marto', 'admin@greenacres.com', '$2y$10$HB3usX2f7uw54b.NGZqC8u4TMu0MJGeTMz8mIQYtQamN2ETCm7at2', 'user', '2026-03-22 19:59:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `animals`
--
ALTER TABLE `animals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `birds`
--
ALTER TABLE `birds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crops`
--
ALTER TABLE `crops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `harvest_logs`
--
ALTER TABLE `harvest_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_harvest_crop` (`crop_id`);

--
-- Indexes for table `produce`
--
ALTER TABLE `produce`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_produce_crop` (`crop_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_orders`
--
ALTER TABLE `supplier_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_produce`
--
ALTER TABLE `supplier_produce`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `produce_id` (`produce_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `animals`
--
ALTER TABLE `animals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `birds`
--
ALTER TABLE `birds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `crops`
--
ALTER TABLE `crops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `harvest_logs`
--
ALTER TABLE `harvest_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produce`
--
ALTER TABLE `produce`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `supplier_orders`
--
ALTER TABLE `supplier_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `supplier_produce`
--
ALTER TABLE `supplier_produce`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `harvest_logs`
--
ALTER TABLE `harvest_logs`
  ADD CONSTRAINT `fk_harvest_crop` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `produce`
--
ALTER TABLE `produce`
  ADD CONSTRAINT `fk_produce_crop` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `supplier_produce`
--
ALTER TABLE `supplier_produce`
  ADD CONSTRAINT `supplier_produce_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `supplier_produce_ibfk_2` FOREIGN KEY (`produce_id`) REFERENCES `produce` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
