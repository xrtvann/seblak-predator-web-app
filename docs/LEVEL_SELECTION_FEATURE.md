# Level Selection Feature for Transaction Step 2

## Overview
This document describes the implementation of the level selection feature in the transaction process. The feature allows users to select product variants (levels) when adding products to a new transaction.

## Implementation Details

### Database Structure
The system uses two main tables for variants:
- `product_variant_groups`: Stores variant group information (e.g., "Level Pedas")
- `product_variant_options`: Stores individual variant options (e.g., "Level 0", "Level 1", "Level 2", etc.)

### UI/UX Design
The level selection is implemented as a **modal dialog** that appears when a user selects a product that has variants:

1. **Trigger**: When a product with variants is clicked in Step 2
2. **Modal Display**: 
   - Shows product name in the title
   - Displays base price
   - Shows price adjustments dynamically
   - Lists all variant groups with their options

3. **Group Selection**:
   - Uses radio buttons for single selection per group
   - Displays variant options as a list with clear labels
   - Shows price adjustments (if any) for each option
   - Marks required groups with a red badge
   - Optional groups have a secondary badge

4. **Visual Feedback**:
   - Real-time price calculation as options are selected
   - Color-coded badges for price adjustments:
     - Yellow badge for positive price adjustments (+)
     - Green badge for negative price adjustments (-)
     - Grey badge for standard (no adjustment)

### Features

#### 1. Variant Selection Modal
- **Title**: "Pilih Level untuk [Product Name]"
- **Price Display**:
  - Base Price
  - Price Adjustment (calculated dynamically)
  - Total Price (base + adjustments)
- **Validation**: Ensures all required variant groups are selected before adding to cart

#### 2. Product Card Display
Products with selected variants show:
- Variant badges below product name in the order summary
- Icon indicator (adjustments-horizontal) for variants
- Variant option names displayed as blue badges

#### 3. Step 3 Summary
The payment/review step shows:
- Product name with quantity and unit price
- Selected variants with their names and price adjustments
- Toppings (if any)
- Total price per item

### Technical Implementation

#### Frontend (transaksi.php)

**Key Functions:**
- `showVariantSelectionModal(product)`: Displays the variant selection modal
- `renderVariantGroups(variantGroups)`: Renders the variant group options
- `toggleProduct(productId)`: Modified to check for variants before adding to cart

**Data Structure for Cart Items:**
```javascript
{
    id: 'item_timestamp',
    product_id: 'product_id',
    product_name: 'Product Name',
    unit_price: finalPrice, // base price + variant adjustments
    quantity: 1,
    toppings: [],
    variants: [
        {
            group_id: 'group_id',
            group_name: 'Group Name',
            option_id: 'option_id',
            option_name: 'Option Name',
            price_adjustment: 0.00
        }
    ]
}
```

#### Backend (api/menu/products.php)

**Modified Functions:**
- `getAllProducts()`: Now includes variant data for all products

**Variant Data Structure:**
```json
{
    "id": "variant_group_id",
    "name": "Level Pedas",
    "is_required": true,
    "allow_multiple": false,
    "sort_order": 0,
    "options": [
        {
            "id": "option_id",
            "name": "Level 0",
            "price_adjustment": 0.00,
            "is_active": true,
            "sort_order": 0
        }
    ]
}
```

### User Flow

1. **Step 1**: Customer enters their information
2. **Step 2**: Customer selects products
   - Click on a product
   - If product has variants, a modal appears
   - Select desired level/variant options
   - Required groups must be selected
   - Review total price with adjustments
   - Click "Tambahkan ke Pesanan" to add to cart
3. **Cart Display**: Shows product with selected variants as badges
4. **Step 3**: Review order with variant details before payment

### Validation Rules

1. **Required Variants**: All variant groups marked as `is_required` must have a selection
2. **Auto-Selection**: If a variant group is required, the first option is pre-selected
3. **Price Calculation**: Automatically calculates and displays total with price adjustments

### Benefits

1. **Clear Visual Hierarchy**: Group-based selection makes it easy to understand options
2. **Real-time Feedback**: Prices update immediately as selections change
3. **Validation**: Prevents incomplete orders by requiring selections
4. **Flexible**: Supports multiple variant groups per product
5. **Price Transparency**: Shows how each variant affects the final price

### Future Enhancements

Potential improvements for future versions:
1. Support for `allow_multiple` selections (currently uses radio buttons for single selection)
2. Image previews for variant options
3. Variant-specific stock management
4. Default variant selection based on user preferences
5. Variant search/filter for products with many options

## Testing

To test the feature:
1. Create a product with variant groups in the menu management
2. Add variant options (e.g., Level 0, Level 1, Level 2)
3. Set price adjustments for different levels
4. Go to transaction page
5. Start a new transaction
6. Select the product
7. Verify modal appears with all variant options
8. Select variants and check price calculation
9. Add to cart and verify display in order summary
10. Complete transaction and verify in Step 3 summary

## Database Setup

Example SQL to create variant groups and options for a product:

```sql
-- Create variant group
INSERT INTO product_variant_groups (id, product_id, name, is_required, allow_multiple, sort_order)
VALUES ('vg_001', 'product_001', 'Level Pedas', 1, 0, 0);

-- Create variant options (levels)
INSERT INTO product_variant_options (id, variant_group_id, name, price_adjustment, is_active, sort_order)
VALUES 
    ('vo_001', 'vg_001', 'Level 0 (Tidak Pedas)', 0.00, 1, 0),
    ('vo_002', 'vg_001', 'Level 1 (Sedikit Pedas)', 0.00, 1, 1),
    ('vo_003', 'vg_001', 'Level 2 (Pedas)', 2000.00, 1, 2),
    ('vo_004', 'vg_001', 'Level 3 (Sangat Pedas)', 3000.00, 1, 3),
    ('vo_005', 'vg_001', 'Level 4 (Extra Pedas)', 5000.00, 1, 4);
```

## Related Files

- `dist/dashboard/pages/transaksi.php` - Main transaction page with level selection UI
- `api/menu/products.php` - Products API with variant data
- Database tables:
  - `product_variant_groups`
  - `product_variant_options`
