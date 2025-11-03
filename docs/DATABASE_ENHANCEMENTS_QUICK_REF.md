# Quick Reference: Database Enhancements

## ✅ What Was Added

### New Tables (4)
1. **expense_categories** - 13 pre-defined categories
2. **expenses** - Track all business expenses
3. **product_costs** - Track COGS for profit calculation
4. **daily_sales_summary** - Daily sales & profit summary

### Enhanced Tables
- **order_items** - Added 4 new columns:
  - `variant_option_id` - Selected spiciness level
  - `variant_name` - Display name (e.g., "Level 3")
  - `cost_price` - Cost of goods sold
  - `profit` - Auto-calculated profit

### Analysis Views (4)
1. **v_product_profitability** - Product profit margins
2. **v_monthly_sales_summary** - Monthly aggregations
3. **v_expense_summary** - Expense breakdown
4. **v_order_items_profit** - Transaction-level profit

### Stored Procedures (2)
1. **sp_update_daily_summary(date)** - Calculate daily summary
2. **sp_profit_analysis(start_date, end_date)** - Profit report

### Triggers (1)
- **trg_order_items_set_cost_before_insert** - Auto-set costs & prices

### Spiciness Levels
- **30 variant options** created (6 levels × 5 products)
- Level 0-3: No extra charge
- Level 4: +Rp 1,000
- Level 5: +Rp 2,000

---

## 🚀 Quick Start Commands

```sql
-- View daily profit
CALL sp_update_daily_summary(CURDATE());

-- Check product profitability
SELECT * FROM v_product_profitability ORDER BY profit_margin_percent DESC;

-- Add an expense
INSERT INTO expenses (id, category_id, expense_date, description, amount)
VALUES (UUID(), 'exp_cat_001', CURDATE(), 'Sayuran segar', 50000);

-- Add product cost
INSERT INTO product_costs (id, product_id, ingredient_cost, labor_cost, overhead_cost, effective_from, is_current)
VALUES (UUID(), 'prod_xxx', 15000, 5000, 2000, CURDATE(), 1);

-- View today's profit
SELECT * FROM daily_sales_summary WHERE summary_date = CURDATE();

-- Popular spice levels
SELECT variant_name, COUNT(*) FROM order_items 
WHERE variant_name IS NOT NULL 
GROUP BY variant_name 
ORDER BY COUNT(*) DESC;
```

---

## 📊 Key Metrics Available

- **Gross Profit** = Revenue - COGS
- **Profit Margin** = (Gross Profit / Revenue) × 100
- **Net Profit** = Gross Profit - Expenses
- **Per-Item Profit** = Subtotal - (Cost Price × Quantity)

---

## 📁 Files Created

1. `/sql/setup_cost_analysis_and_variants.sql` - Setup script
2. `/docs/COST_ANALYSIS_AND_SPICINESS_LEVELS.md` - Full documentation

---

## 🔗 Documentation

For detailed usage instructions, see:
**`docs/COST_ANALYSIS_AND_SPICINESS_LEVELS.md`**
