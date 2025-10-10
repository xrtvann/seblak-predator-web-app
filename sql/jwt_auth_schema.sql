-- JWT Authentication System Database Schema
-- Additional tables for secure authentication and monitoring

-- Login attempts tracking table
CREATE TABLE IF NOT EXISTS login_attempts (
    id VARCHAR(100) NOT NULL PRIMARY KEY DEFAULT (CONCAT('attempt_', UNIX_TIMESTAMP(), '_', CONNECTION_ID())),
    username VARCHAR(100) NOT NULL,
    success TINYINT(1) NOT NULL DEFAULT 0,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    attempted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_login_attempts_username (username),
    INDEX idx_login_attempts_ip (ip_address),
    INDEX idx_login_attempts_time (attempted_at),
    INDEX idx_login_attempts_success (success, attempted_at)
);

-- Token refresh log table
CREATE TABLE IF NOT EXISTS token_refresh_log (
    id VARCHAR(100) NOT NULL PRIMARY KEY DEFAULT (CONCAT('refresh_', UNIX_TIMESTAMP(), '_', CONNECTION_ID())),
    user_id VARCHAR(100) NOT NULL,
    refreshed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    
    INDEX idx_token_refresh_user (user_id),
    INDEX idx_token_refresh_time (refreshed_at),
    CONSTRAINT fk_token_refresh_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- API access log table for security monitoring
CREATE TABLE IF NOT EXISTS api_access_log (
    id VARCHAR(100) NOT NULL PRIMARY KEY DEFAULT (CONCAT('api_', UNIX_TIMESTAMP(), '_', CONNECTION_ID())),
    user_id VARCHAR(100) NULL,
    endpoint VARCHAR(255) NOT NULL,
    method VARCHAR(10) NOT NULL,
    success TINYINT(1) NOT NULL DEFAULT 1,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    accessed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_api_access_user (user_id),
    INDEX idx_api_access_endpoint (endpoint),
    INDEX idx_api_access_time (accessed_at),
    INDEX idx_api_access_success (success, accessed_at),
    CONSTRAINT fk_api_access_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Blacklisted tokens table (for logout functionality)
CREATE TABLE IF NOT EXISTS blacklisted_tokens (
    id VARCHAR(100) NOT NULL PRIMARY KEY DEFAULT (CONCAT('blacklist_', UNIX_TIMESTAMP(), '_', CONNECTION_ID())),
    token_jti VARCHAR(255) NOT NULL UNIQUE,
    user_id VARCHAR(100) NOT NULL,
    blacklisted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    reason ENUM('logout', 'security', 'admin') NOT NULL DEFAULT 'logout',
    
    INDEX idx_blacklisted_tokens_jti (token_jti),
    INDEX idx_blacklisted_tokens_user (user_id),
    INDEX idx_blacklisted_tokens_expires (expires_at),
    CONSTRAINT fk_blacklisted_tokens_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- User sessions table for additional security (optional)
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(100) NOT NULL PRIMARY KEY DEFAULT (CONCAT('session_', UNIX_TIMESTAMP(), '_', CONNECTION_ID())),
    user_id VARCHAR(100) NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    
    INDEX idx_user_sessions_user (user_id),
    INDEX idx_user_sessions_token (session_token),
    INDEX idx_user_sessions_expires (expires_at),
    INDEX idx_user_sessions_active (is_active, last_activity),
    CONSTRAINT fk_user_sessions_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);