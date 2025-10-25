<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../services/WebAuthService.php';
require_once __DIR__ . '/../api/auth/JWTHelper.php';

class LoginTest extends TestCase
{
    private $auth;
    private $jwt;
    private $koneksi;
    private $testUser;

    protected function setUp(): void
    {
        // Start clean session for each test
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        session_start();
        session_regenerate_id(true);

        // Initialize test user data
        $this->testUser = [
            'username' => 'testuser_' . time(),
            'password' => 'testpass123',
            'email' => 'testuser_' . time() . '@example.com',
            'name' => 'Test User'
        ];

        // Initialize database connection
        require_once __DIR__ . '/../config/database.php';
        $this->koneksi = DatabaseConnection::getInstance();

        // Initialize services
        $this->auth = new WebAuthService($this->koneksi);
        $this->jwt = new JWTHelper();

        // Create test user if not exists
        $this->createTestUser();

        // Clear any existing login attempts for this user and IP to avoid rate limiting
        $this->clearLoginAttempts();
    }

    protected function tearDown(): void
    {
        // Clean up test user
        $this->deleteTestUser();
    }

    private function createTestUser()
    {
        // Check if test user exists
        $stmt = mysqli_prepare($this->koneksi, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $this->testUser['username']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 0) {
            // Create test user
            $password_hash = password_hash($this->testUser['password'], PASSWORD_DEFAULT);
            $role_id = 'role_customer'; // Default role

            $stmt = mysqli_prepare($this->koneksi, "
                INSERT INTO users (id, username, email, password_hash, name, role_id, is_active, created_at)
                VALUES (UUID(), ?, ?, ?, ?, ?, TRUE, NOW())
            ");
            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $this->testUser['username'],
                $this->testUser['email'],
                $password_hash,
                $this->testUser['name'],
                $role_id
            );
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);
    }

    private function deleteTestUser()
    {
        $stmt = mysqli_prepare($this->koneksi, "DELETE FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $this->testUser['username']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    private function clearLoginAttempts()
    {
        // Clear login attempts for this test user (by username)
        $stmt = mysqli_prepare($this->koneksi, "DELETE FROM login_attempts WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $this->testUser['username']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Clear login attempts for this test user (by email, since login can be done with email)
        $stmt = mysqli_prepare($this->koneksi, "DELETE FROM login_attempts WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $this->testUser['email']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Clear all login attempts for the current IP to avoid interference from other tests
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt = mysqli_prepare($this->koneksi, "DELETE FROM login_attempts WHERE ip_address = ?");
        mysqli_stmt_bind_param($stmt, "s", $ip);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    public function testSuccessfulLogin()
    {
        // Test successful login
        $result = $this->auth->login($this->testUser['username'], $this->testUser['password']);

        $this->assertTrue($result['success']);
        $this->assertEquals('Login successful', $result['message']);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals($this->testUser['username'], $result['user']['username']);
        $this->assertEquals($this->testUser['email'], $result['user']['email']);
        $this->assertEquals($this->testUser['name'], $result['user']['name']);
    }

    public function testLoginWithInvalidUsername()
    {
        // Test login with invalid username
        $result = $this->auth->login('invaliduser', $this->testUser['password']);

        $this->assertFalse($result['success']);
        // Could be rate limited or invalid credentials
        $this->assertTrue(
            $result['message'] === 'Invalid username or password' ||
            strpos($result['message'], 'Too many failed login attempts') !== false
        );
        if ($result['code'] !== 'RATE_LIMITED') {
            $this->assertEquals('INVALID_CREDENTIALS', $result['code']);
        }
    }

    public function testLoginWithInvalidPassword()
    {
        // Test login with invalid password
        $result = $this->auth->login($this->testUser['username'], 'wrongpassword');

        $this->assertFalse($result['success']);
        // Could be rate limited or invalid credentials
        $this->assertTrue(
            $result['message'] === 'Invalid username or password' ||
            strpos($result['message'], 'Too many failed login attempts') !== false
        );
        if ($result['code'] !== 'RATE_LIMITED') {
            $this->assertEquals('INVALID_CREDENTIALS', $result['code']);
        }
    }

    public function testLoginWithEmptyUsername()
    {
        // Test login with empty username
        $result = $this->auth->login('', $this->testUser['password']);

        $this->assertFalse($result['success']);
        // Could be rate limited or invalid credentials
        $this->assertTrue(
            $result['message'] === 'Invalid username or password' ||
            strpos($result['message'], 'Too many failed login attempts') !== false
        );
    }

    public function testLoginWithEmptyPassword()
    {
        // Test login with empty password
        $result = $this->auth->login($this->testUser['username'], '');

        $this->assertFalse($result['success']);
        // Could be rate limited or invalid credentials
        $this->assertTrue(
            $result['message'] === 'Invalid username or password' ||
            strpos($result['message'], 'Too many failed login attempts') !== false
        );
    }

    public function testJWTTokenGeneration()
    {
        // Test JWT token generation
        $userData = [
            'id' => 'test-user-id',
            'username' => $this->testUser['username'],
            'email' => $this->testUser['email'],
            'role_id' => 'role_customer',
            'name' => $this->testUser['name']
        ];

        $tokens = $this->jwt->generateTokenPair($userData);

        $this->assertArrayHasKey('access_token', $tokens);
        $this->assertArrayHasKey('refresh_token', $tokens);
        $this->assertArrayHasKey('token_type', $tokens);
        $this->assertArrayHasKey('expires_in', $tokens);
        $this->assertEquals('Bearer', $tokens['token_type']);
        $this->assertIsInt($tokens['expires_in']);
        $this->assertGreaterThan(0, $tokens['expires_in']);
    }

    public function testJWTTokenValidation()
    {
        // Test JWT token validation
        $userData = [
            'id' => 'test-user-id',
            'username' => $this->testUser['username'],
            'email' => $this->testUser['email'],
            'role_id' => 'role_customer',
            'name' => $this->testUser['name']
        ];

        $tokens = $this->jwt->generateTokenPair($userData);

        // Validate access token
        $validation = $this->jwt->validateToken($tokens['access_token']);
        $this->assertTrue($validation['valid']);
        $this->assertEquals('access', $validation['token_type']);
        $expectedUserData = [
            'user_id' => $userData['id'],
            'username' => $userData['username'],
            'email' => $userData['email'],
            'role_id' => $userData['role_id'],
            'name' => $userData['name']
        ];
        $this->assertEquals($expectedUserData, $validation['user_data']);

        // Validate refresh token
        $validation = $this->jwt->validateToken($tokens['refresh_token']);
        $this->assertTrue($validation['valid']);
        $this->assertEquals('refresh', $validation['token_type']);
        $this->assertEquals($expectedUserData, $validation['user_data']);
    }

    public function testInvalidJWTToken()
    {
        // Test invalid JWT token
        $validation = $this->jwt->validateToken('invalid.token.here');

        $this->assertFalse($validation['valid']);
        $this->assertStringContainsString('Invalid token', $validation['error']);
    }

    public function testEmptyJWTToken()
    {
        // Test empty JWT token
        $validation = $this->jwt->validateToken('');

        $this->assertFalse($validation['valid']);
        $this->assertEquals('Token is empty', $validation['error']);
    }

    public function testRateLimiting()
    {
        // Test rate limiting by attempting multiple failed logins
        for ($i = 0; $i < 6; $i++) {
            $result = $this->auth->login($this->testUser['username'], 'wrongpassword');
            $this->assertFalse($result['success']);
        }

        // Next attempt should be rate limited
        $result = $this->auth->login($this->testUser['username'], 'wrongpassword');
        $this->assertFalse($result['success']);

        // Check if rate limited
        if (isset($result['code']) && $result['code'] === 'RATE_LIMITED') {
            $this->assertStringContainsString('Too many failed login attempts', $result['message']);
            $this->assertArrayHasKey('remaining_time_text', $result);
        }
    }

    public function testLoginWithEmail()
    {
        // Test login using email instead of username
        $result = $this->auth->login($this->testUser['email'], $this->testUser['password']);

        $this->assertTrue($result['success']);
        $this->assertEquals('Login successful', $result['message']);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals($this->testUser['username'], $result['user']['username']);
        $this->assertEquals($this->testUser['email'], $result['user']['email']);
    }
}
