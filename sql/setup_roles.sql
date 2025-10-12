-- Role-Based Access Control Setup for Seblak Predator
-- This script sets up the three roles: owner, admin, customer

-- First, ensure the roles table exists and has the required roles
INSERT IGNORE INTO roles (id, name, created_at) VALUES 
('role_owner', 'owner', CURRENT_TIMESTAMP),
('role_admin', 'admin', CURRENT_TIMESTAMP),
('role_customer', 'customer', CURRENT_TIMESTAMP);

-- Update existing roles if they exist with different names
UPDATE roles SET name = 'owner' WHERE id = 'role_owner';
UPDATE roles SET name = 'admin' WHERE id = 'role_admin';
UPDATE roles SET name = 'customer' WHERE id = 'role_customer';

-- Create default owner user if it doesn't exist
-- Note: Password is 'owner123' hashed with PASSWORD_DEFAULT
INSERT IGNORE INTO users (
    id, 
    name, 
    email, 
    username, 
    password_hash, 
    role_id, 
    is_active, 
    created_at, 
    updated_at
) VALUES (
    'user_owner_001',
    'System Owner',
    'owner@seblakpredator.com',
    'owner',
    '$2y$10$5c0I/bCfxnPaSZ5vJRo8x.eesxDolxYRUK.pTS83SszOZ1cmxgzDm', -- password: owner123
    'role_owner',
    TRUE,
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
);

-- Create default admin user if it doesn't exist  
-- Note: Password is 'admin123' hashed with PASSWORD_DEFAULT
INSERT IGNORE INTO users (
    id,
    name,
    email, 
    username,
    password_hash,
    role_id,
    is_active,
    created_at,
    updated_at
) VALUES (
    'user_admin_001',
    'System Administrator', 
    'admin@seblakpredator.com',
    'admin',
    '$2y$10$wppfPmhA6gQede6jKAEcjua3N8QzxmQ9ng/gBmWxBh3WIsRbR/pcu', -- password: admin123
    'role_admin',
    TRUE,
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
);

-- Create sample customer user if it doesn't exist
-- Note: Password is 'customer123' hashed with PASSWORD_DEFAULT
INSERT IGNORE INTO users (
    id,
    name,
    email,
    username, 
    password_hash,
    role_id,
    is_active,
    created_at,
    updated_at
) VALUES (
    'user_customer_001',
    'Sample Customer',
    'customer@example.com',
    'customer',
    '$2y$10$KD4McndARifxHejX9XiV..554sIQy8fvdebibbkVLgPaUIBFwdYeu', -- password: customer123
    'role_customer',
    TRUE,
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
);

-- Show the created roles and users
SELECT 'ROLES CREATED:' as info;
SELECT * FROM roles ORDER BY name;

SELECT 'USERS CREATED:' as info;
SELECT u.id, u.name, u.username, u.email, r.name as role_name, u.is_active 
FROM users u 
JOIN roles r ON u.role_id = r.id 
ORDER BY r.name, u.name;