-- Schema untuk Sistem Transaksi Seblak Prasmanan
-- Mendukung produk dengan multiple topping

-- Tabel untuk order/transaksi utama
CREATE TABLE IF NOT EXISTS `orders` (
  `id` VARCHAR(36) PRIMARY KEY,
  `order_number` VARCHAR(50) UNIQUE NOT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `table_number` VARCHAR(20) NULL,
  `phone` VARCHAR(20) NULL,
  `notes` TEXT NULL,
  `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `tax` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `discount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `total_amount` DECIMAL(15,2) NOT NULL,
  `payment_method` ENUM('cash', 'card', 'qris', 'transfer') DEFAULT 'cash',
  `payment_status` ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
  `order_status` ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
  `created_by` VARCHAR(36) NULL,
  `completed_at` DATETIME NULL,
  `cancelled_at` DATETIME NULL,
  `cancel_reason` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_order_number` (`order_number`),
  INDEX `idx_order_status` (`order_status`),
  INDEX `idx_payment_status` (`payment_status`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel untuk item dalam order (produk utama)
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` VARCHAR(36) PRIMARY KEY,
  `order_id` VARCHAR(36) NOT NULL,
  `product_id` VARCHAR(36) NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(15,2) NOT NULL,
  `subtotal` DECIMAL(15,2) NOT NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_order_id` (`order_id`),
  INDEX `idx_product_id` (`product_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel untuk topping yang ditambahkan ke produk dalam order
CREATE TABLE IF NOT EXISTS `order_item_toppings` (
  `id` VARCHAR(36) PRIMARY KEY,
  `order_item_id` VARCHAR(36) NOT NULL,
  `topping_id` VARCHAR(36) NOT NULL,
  `topping_name` VARCHAR(255) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(15,2) NOT NULL,
  `subtotal` DECIMAL(15,2) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_order_item_id` (`order_item_id`),
  INDEX `idx_topping_id` (`topping_id`),
  FOREIGN KEY (`order_item_id`) REFERENCES `order_items`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`topping_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Trigger untuk generate order number otomatis
DELIMITER $$
CREATE TRIGGER `before_insert_order` 
BEFORE INSERT ON `orders`
FOR EACH ROW
BEGIN
  IF NEW.order_number IS NULL OR NEW.order_number = '' THEN
    SET NEW.order_number = CONCAT('ORD-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD((SELECT COALESCE(COUNT(*) + 1, 1) FROM orders WHERE DATE(created_at) = CURDATE()), 4, '0'));
  END IF;
END$$
DELIMITER ;
