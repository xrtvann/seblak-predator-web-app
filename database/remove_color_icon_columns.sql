-- SQL Script to remove color and icon columns from expense_categories table
-- Run this script to update your database schema

ALTER TABLE `expense_categories` 
DROP COLUMN `color`,
DROP COLUMN `icon`;
