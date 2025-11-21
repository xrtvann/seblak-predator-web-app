@echo off
echo ========================================
echo   Starting Ngrok for Seblak Predator
echo ========================================
echo.

REM Cek apakah ngrok bisa diakses (di PATH atau Microsoft Store)
where ngrok >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [OK] Ngrok found in system PATH
    echo.
    echo Starting ngrok tunnel on port 80...
    echo.
    echo IMPORTANT:
    echo 1. Copy the HTTPS URL that appears below
    echo 2. Set it in Midtrans Dashboard webhook URL
    echo 3. Add path: /seblak-predator/api/midtrans/notification.php
    echo.
    echo Example:
    echo https://abc123.ngrok-free.app/seblak-predator/api/midtrans/notification.php
    echo.
    echo ========================================
    echo.
    
    ngrok http 80
    goto :end
)

REM Jika tidak di PATH, cek di current directory
if exist "ngrok.exe" (
    echo [OK] Ngrok found in current directory
    echo.
    echo Starting ngrok tunnel on port 80...
    echo.
    echo IMPORTANT:
    echo 1. Copy the HTTPS URL that appears below
    echo 2. Set it in Midtrans Dashboard webhook URL
    echo 3. Add path: /seblak-predator/api/midtrans/notification.php
    echo.
    echo Example:
    echo https://abc123.ngrok-free.app/seblak-predator/api/midtrans/notification.php
    echo.
    echo ========================================
    echo.
    
    ngrok.exe http 80
    goto :end
)

REM Jika ngrok tidak ditemukan
echo [ERROR] ngrok not found!
echo.
echo Please install ngrok first:
echo.
echo Option 1 (Recommended):
echo   - Open Microsoft Store
echo   - Search "ngrok"
echo   - Click Install/Get
echo.
echo Option 2 (Manual):
echo   - Download from: https://ngrok.com/download
echo   - Extract ngrok.exe to: %CD%
echo.
echo Then run this script again.
echo.
pause

:end

