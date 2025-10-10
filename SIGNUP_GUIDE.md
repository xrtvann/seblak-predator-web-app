# 📝 Seblak Predator - Sign Up Guide

## How to Create Your Account

### 🌐 Access the Registration Page

**Direct Link:** http://localhost:8000/pages/auth/register.php

**OR from Login Page:**
1. Go to: http://localhost:8000/pages/auth/login.php
2. Click "Sign Up" at the bottom of the page

### 📋 Registration Form Fields

Fill out all required fields:

| Field | Description | Requirements |
|-------|-------------|--------------|
| **Name** | Your full name | Required, any text |
| **Username** | Unique username for login | Required, must be unique |
| **Email** | Your email address | Required, valid email format |
| **Password** | Account password | Required, secure password |

### 📝 Step-by-Step Registration Process

1. **Open Registration Page**
   - Navigate to: `http://localhost:8000/pages/auth/register.php`

2. **Fill Form Fields**
   ```
   Name: [Your Full Name]
   Username: [unique_username]
   Email: [your-email@example.com]
   Password: [secure_password]
   ```

3. **Submit Registration**
   - Click the red "Sign Up" button
   - Wait for processing

4. **Success Redirect**
   - You'll be redirected to login page with success message
   - Use your new credentials to log in

### ✅ What Happens During Registration

1. **Validation**: System checks if username is available
2. **Role Assignment**: You're automatically assigned "user" role
3. **Password Security**: Password is securely hashed using bcrypt
4. **Database Storage**: Account is saved to database
5. **Redirect**: You're sent to login page to sign in

### 🔐 After Registration

1. **Go to Login Page**: `http://localhost:8000/pages/auth/login.php`
2. **Enter Credentials**: Use your username and password
3. **Access Dashboard**: Successfully login to access the application

### ⚠️ Common Issues & Solutions

**Issue: "Username already exists"**
- Solution: Choose a different username

**Issue: "Registration failed"**
- Solution: Check all fields are filled correctly
- Ensure email format is valid
- Try a different username

**Issue: "Database connection error"**
- Solution: Make sure Laragon MySQL is running
- Check if database 'seblak_app' exists

### 🛠️ Technical Details

**Form Action:** `../../handler/auth.php`
**Method:** POST
**Required Action:** `action=register`

**Database Tables Used:**
- `users` - Stores user account information
- `roles` - Manages user roles (auto-creates 'user' role)

**Security Features:**
- Password hashing with bcrypt
- SQL injection prevention with prepared statements
- Input sanitization with htmlspecialchars()
- Unique username validation

### 📱 User Interface

The registration page features:
- Clean, modern design using Berry Bootstrap theme
- Responsive layout for desktop and mobile
- Form validation
- Professional styling with red accent colors
- Loading indicators and smooth transitions

---

**Ready to get started?** Visit http://localhost:8000/pages/auth/register.php and create your account!