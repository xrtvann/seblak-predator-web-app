# 🌶️ Cost Analysis & Spiciness Level System

**Implementation Date:** November 2, 2025  
**Database:** seblak_app  
**Status:** ✅ Fully Implemented

---

## 📋 Overview

This document describes the enhanced database system for **Seblak Predator** that adds:
1. **Cost Analysis & Profit Tracking** - Track expenses and calculate profit margins
2. **Spiciness Level Variants** - Allow customers to choose spice levels (Level 0-5)

---

## 🆕 New Database Tables

### 1. **expense_categories**
Categorize different types of business expenses.

**Pre-populated categories:**
- 🥬 **Bahan Baku Sayuran** - Fresh vegetables
- 🦑 **Bahan Baku Seafood** - Seafood ingredients
- 🌶️ **Bahan Baku Bumbu** - Spices and seasonings
- 🍘 **Bahan Baku Kerupuk** - Crackers
- 💰 **Gaji Karyawan** - Employee salaries
- ⚡ **Listrik & Air** - Utilities
- 🔥 **Gas LPG** - Cooking gas
- 🍳 **Peralatan Dapur** - Kitchen equipment
- 🧼 **Kebersihan** - Cleaning supplies
- 📢 **Marketing & Promosi** - Advertising
- 🏠 **Sewa Tempat** - Rent
- 🔧 **Maintenance** - Repairs
- 📦 **Lain-lain** - Other expenses

### 2. **expenses**
Track all business expenses with details.

**Columns:**
- `category_id` - Links to expense_categories
- `expense_date` - Date of expense
- `description` - What was purchased
- `amount` - Total cost
- `quantity` - Amount purchased
- `unit` - Unit of measurement (pcs, kg, etc.)
- `supplier_name` - Vendor name
- `receipt_number` - Invoice/receipt reference

### 3. **product_costs**
Track Cost of Goods Sold (COGS) for each product.

**Columns:**
- `product_id` - Links to products table
- `ingredient_cost` - Raw material cost per unit
- `labor_cost` - Labor cost per unit
- `overhead_cost` - Allocated overhead per unit
- `total_cost` - *Auto-calculated* (sum of above)
- `effective_from` / `effective_until` - Date range for pricing
- `is_current` - Flag for active cost record

### 4. **daily_sales_summary**
Automatically calculated daily sales and profit summary.

**Columns (some auto-calculated):**
- `summary_date` - Date of summary
- `total_orders` - Number of completed orders
- `total_revenue` - Total sales
- `total_cost` - Total COGS
- `gross_profit` - *Auto: revenue - cost*
- `profit_margin` - *Auto: gross profit / revenue × 100*
- `total_expenses` - Sum of expenses for the day
- `net_profit` - *Auto: gross profit - expenses*

---

## 🌶️ Spiciness Level Implementation

### Product Variant Groups
Each main product (not toppings) now has a **"Tingkat Kepedasan"** variant group with 6 levels:

| Level | Name | Price Adjustment |
|-------|------|------------------|
| 0 | Level 0 | +Rp 0 (Mild) |
| 1 | Level 1 | +Rp 0 |
| 2 | Level 2 | +Rp 0 |
| 3 | Level 3 | +Rp 0 |
| 4 | Level 4 | +Rp 1,000 (Super Spicy) |
| 5 | Level 5 | +Rp 2,000 (Extreme!) |

### How It Works
1. When creating an order, customers select a spiciness level
2. The `variant_option_id` and `variant_name` are stored in `order_items`
3. If Level 4 or 5 is selected, the price is automatically increased
4. The trigger `trg_order_items_set_cost_before_insert` handles this automatically

---

## 🔧 Enhanced Order Items Table

**New Columns Added:**
- `variant_option_id` - Selected spiciness level ID
- `variant_name` - Cached variant name (e.g., "Level 3")
- `cost_price` - COGS for this item (auto-populated from product_costs)
- `profit` - *Auto-calculated: subtotal - (cost_price × quantity)*

---

## 📊 Analysis Views

### v_product_profitability
Analyze profit margin for each product.
```sql
SELECT * FROM v_product_profitability 
WHERE is_active = 1 
ORDER BY profit_margin_percent DESC;
```

**Columns:**
- `product_name`, `selling_price`, `cost_price`
- `profit_per_unit`, `profit_margin_percent`
- `ingredient_cost`, `labor_cost`, `overhead_cost`

### v_monthly_sales_summary
Monthly sales and profit overview.
```sql
SELECT * FROM v_monthly_sales_summary 
ORDER BY month DESC 
LIMIT 12;
```

**Shows:**
- Total orders, revenue, cost, and profit by month
- Average profit margin

### v_expense_summary
Expense breakdown by category.
```sql
SELECT * FROM v_expense_summary 
WHERE total_amount > 0 
ORDER BY total_amount DESC;
```

### v_order_items_profit
Detailed profit analysis per order item.
```sql
SELECT * FROM v_order_items_profit 
WHERE order_date = CURDATE();
```

---

## 🔄 Stored Procedures

### sp_update_daily_summary
Calculate and update daily sales summary.

**Usage:**
```sql
-- Update today's summary
CALL sp_update_daily_summary(CURDATE());

-- Update specific date
CALL sp_update_daily_summary('2025-11-01');
```

**What it does:**
- Counts completed and paid orders
- Calculates total revenue
- Sums up COGS from order items
- Sums expenses for the day
- Calculates gross and net profit

### sp_profit_analysis
Get profit analysis for a date range.

**Usage:**
```sql
-- Last 30 days
CALL sp_profit_analysis(
    DATE_SUB(CURDATE(), INTERVAL 30 DAY),
    CURDATE()
);

-- Specific month
CALL sp_profit_analysis('2025-11-01', '2025-11-30');
```

---

## 🤖 Automatic Triggers

### trg_order_items_set_cost_before_insert
Automatically runs **BEFORE** inserting an order item.

**What it does:**
1. Looks up current cost price for the product
2. If variant is selected, adds price adjustment
3. Recalculates unit_price and subtotal
4. Sets cost_price for profit calculation

---

## 💡 Usage Examples

### 1. Adding Product Costs
```sql
INSERT INTO product_costs (
    id, product_id, ingredient_cost, labor_cost, 
    overhead_cost, effective_from, is_current
) VALUES (
    UUID(),
    'prod_6900e16428bdf',  -- Product ID
    15000.00,              -- Ingredient cost
    5000.00,               -- Labor cost
    2000.00,               -- Overhead
    CURDATE(),
    1                      -- Is current
);
```

### 2. Recording Expenses
```sql
INSERT INTO expenses (
    id, category_id, expense_date, description,
    amount, quantity, unit, supplier_name
) VALUES (
    UUID(),
    'exp_cat_001',                    -- Bahan Baku Sayuran
    CURDATE(),
    'Pembelian sawi dan pakcoy',
    50000.00,
    5,
    'kg',
    'Pasar Tradisional'
);
```

### 3. Creating Order with Spiciness Level
```sql
-- First, create the order
INSERT INTO orders (...) VALUES (...);

-- Then add order item with spiciness level
INSERT INTO order_items (
    id, order_id, product_id, product_name,
    quantity, unit_price, subtotal,
    variant_option_id, variant_name
) VALUES (
    UUID(),
    'order_123',
    'prod_6900e16428bdf',
    'Level 0',
    2,                                    -- Quantity
    25000.00,                             -- Base price
    50000.00,                             -- Subtotal
    'vg_spice_prod_6900e16428bdf_level_4', -- Level 4 variant
    'Level 4'
);
-- The trigger will automatically:
-- - Add Rp 1,000 to unit_price (becomes 26,000)
-- - Recalculate subtotal (becomes 52,000)
-- - Set cost_price from product_costs
```

### 4. Daily Summary Update
```sql
-- At end of day, run this to calculate profit
CALL sp_update_daily_summary(CURDATE());

-- View the results
SELECT * FROM daily_sales_summary 
WHERE summary_date = CURDATE();
```

### 5. Check Profitability
```sql
-- Which products are most profitable?
SELECT 
    product_name,
    selling_price,
    cost_price,
    profit_per_unit,
    profit_margin_percent
FROM v_product_profitability
WHERE is_active = 1
ORDER BY profit_margin_percent DESC;

-- Total profit today
SELECT 
    SUM(total_profit) as total_profit_today,
    AVG(profit_margin_percent) as avg_margin
FROM v_order_items_profit
WHERE order_date = CURDATE();
```

### 6. Expense Analysis
```sql
-- This month's expenses by category
SELECT 
    ec.name,
    ec.type,
    SUM(e.amount) as total
FROM expenses e
JOIN expense_categories ec ON e.category_id = ec.id
WHERE e.expense_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
GROUP BY ec.name, ec.type
ORDER BY total DESC;
```

---

## 📈 Reporting Queries

### Monthly Performance Report
```sql
SELECT 
    DATE_FORMAT(summary_date, '%Y-%m') as month,
    SUM(total_orders) as orders,
    SUM(total_revenue) as revenue,
    SUM(total_cost) as cogs,
    SUM(gross_profit) as gross_profit,
    ROUND(AVG(profit_margin), 2) as avg_margin,
    SUM(total_expenses) as expenses,
    SUM(net_profit) as net_profit
FROM daily_sales_summary
WHERE summary_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
GROUP BY DATE_FORMAT(summary_date, '%Y-%m')
ORDER BY month DESC;
```

### Best Selling Items with Profit
```sql
SELECT 
    product_name,
    variant_name,
    COUNT(*) as times_sold,
    SUM(quantity) as total_quantity,
    SUM(revenue) as total_revenue,
    SUM(total_profit) as total_profit,
    ROUND(AVG(profit_margin_percent), 2) as avg_margin
FROM v_order_items_profit
WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY product_name, variant_name
ORDER BY total_revenue DESC
LIMIT 10;
```

### Spiciness Level Popularity
```sql
SELECT 
    variant_name,
    COUNT(*) as orders,
    SUM(quantity) as total_qty,
    SUM(revenue) as total_revenue
FROM v_order_items_profit
WHERE variant_name IS NOT NULL
GROUP BY variant_name
ORDER BY orders DESC;
```

---

## 🎯 Integration with Application

### Order Creation Flow
1. Customer selects product(s)
2. Customer chooses spiciness level (for main dishes)
3. Customer adds toppings
4. Application creates order with:
   - `variant_option_id` = selected level ID
   - `variant_name` = "Level X" (for display)
5. Trigger automatically:
   - Adds price adjustment if Level 4/5
   - Sets cost_price from product_costs
   - Calculates profit

### End-of-Day Process
```sql
-- Run this daily (can be automated via cron)
CALL sp_update_daily_summary(CURDATE());
```

### Profit Analysis Dashboard
Use the views to power your dashboard:
- `v_product_profitability` - Product margins
- `v_monthly_sales_summary` - Monthly trends
- `v_expense_summary` - Spending breakdown
- `v_order_items_profit` - Detailed transaction profit

---

## ⚠️ Important Notes

1. **Product Costs Must Be Set**
   - Before you can calculate profit, add cost records to `product_costs`
   - Set `is_current = 1` for active costs
   - When costs change, set old record to `is_current = 0` and insert new one

2. **Spiciness Levels Are Pre-Created**
   - All main products already have Level 0-5 variants
   - Level 0-3: No extra charge
   - Level 4: +Rp 1,000
   - Level 5: +Rp 2,000

3. **Toppings Don't Have Spiciness Levels**
   - Only main products have the spiciness variant
   - Toppings are separate order_item_toppings records

4. **Daily Summary Needs Manual Run**
   - Call `sp_update_daily_summary(CURDATE())` at end of day
   - Or schedule it via cron job

5. **Cost Price Updates**
   - When product costs change, create NEW record
   - Mark old record with `is_current = 0`
   - This maintains cost history

---

## 🔗 API Integration Suggestions

You may want to create API endpoints for:

1. **GET** `/api/products/{id}/variants` - Get spiciness levels
2. **POST** `/api/expenses` - Record new expense
3. **GET** `/api/analytics/daily-summary` - Get daily sales summary
4. **GET** `/api/analytics/product-profitability` - Product profit margins
5. **POST** `/api/product-costs` - Update product costs
6. **GET** `/api/analytics/expense-summary` - Expense breakdown

---

## 📝 Schema Diagram

```
┌─────────────────┐         ┌──────────────────┐
│    products     │────────▶│  product_costs   │
│                 │         │  - ingredient    │
│  - id           │         │  - labor         │
│  - name         │         │  - overhead      │
│  - price        │         │  - total_cost ✨ │
└─────────────────┘         └──────────────────┘
        │
        │ 1:N
        ▼
┌──────────────────────────┐
│ product_variant_groups   │
│  - product_id            │
│  - name: "Tingkat        │
│           Kepedasan"     │
└──────────────────────────┘
        │
        │ 1:N
        ▼
┌──────────────────────────┐
│ product_variant_options  │
│  - name: "Level 0-5"     │
│  - price_adjustment      │
└──────────────────────────┘
        │
        │ Referenced by
        ▼
┌──────────────────────────┐
│     order_items          │
│  - variant_option_id 🆕  │
│  - variant_name 🆕       │
│  - cost_price 🆕         │
│  - profit ✨             │
└──────────────────────────┘

┌──────────────────┐         ┌──────────────────┐
│ expense_         │────────▶│    expenses      │
│ categories       │         │                  │
└──────────────────┘         └──────────────────┘

┌──────────────────────────┐
│  daily_sales_summary     │
│  - total_revenue         │
│  - total_cost            │
│  - gross_profit ✨       │
│  - profit_margin ✨      │
│  - total_expenses        │
│  - net_profit ✨         │
└──────────────────────────┘

Legend:
✨ = Auto-calculated column
🆕 = Newly added column
```

---

## ✅ Verification

Run these queries to verify everything is set up correctly:

```sql
-- 1. Check expense categories
SELECT COUNT(*) as category_count FROM expense_categories;
-- Expected: 13

-- 2. Check spiciness levels per product
SELECT 
    COUNT(DISTINCT vg.product_id) as products_with_variants,
    COUNT(vo.id) as total_variant_options
FROM product_variant_groups vg
LEFT JOIN product_variant_options vo ON vg.id = vo.variant_group_id;
-- Expected: 5 products, 30 options (5 × 6 levels)

-- 3. Check order_items has new columns
DESCRIBE order_items;
-- Should see: variant_option_id, variant_name, cost_price, profit

-- 4. Check views exist
SHOW FULL TABLES WHERE Table_type = 'VIEW';
-- Should see: 4 views

-- 5. Check procedures exist
SHOW PROCEDURE STATUS WHERE Db = 'seblak_app';
-- Should see: sp_update_daily_summary, sp_profit_analysis

-- 6. Check trigger exists
SHOW TRIGGERS WHERE `Table` = 'order_items';
-- Should see: trg_order_items_set_cost_before_insert
```

---

## 🎉 Summary

Your database is now equipped with:
- ✅ Complete cost tracking system
- ✅ Profit margin calculation
- ✅ Spiciness level variants (Level 0-5)
- ✅ Automatic price adjustments
- ✅ Daily sales summaries
- ✅ Expense categorization
- ✅ Analysis views and stored procedures
- ✅ Automated triggers for data consistency

**Ready for production use!** 🚀
