@echo off
if not "%~1"=="max" (
    start /max cmd /c "%~f0" max
    exit
)
color 0A
title Laravel E-Commerce Server Launcher
cls

echo.
echo ================================================================================
echo                    LARAVEL E-COMMERCE APPLICATION LAUNCHER
echo ================================================================================
echo.
echo [REQUIREMENTS]
echo   1. Requires Laravel 10 or above
echo   2. PHP 8.1+ must be installed
echo   3. Composer must be installed
echo   4. Run : composer install
echo.
echo ================================================================================
echo [ACCESS INFORMATION]
echo.
echo   Admin Panel URL:  http://127.0.0.1:8000/admin/login
echo   Frontend URL:     http://127.0.0.1:8000
echo.
echo ================================================================================
echo [ADMIN CREDENTIALS]
echo.
echo   Email:    tirth@gmail.com
echo   Password: 12345678
echo.
echo   Note: Register a new admin account or use the demo credentials above
echo.
echo ================================================================================
echo [PAYMENT TEST CARD (STRIPE)]
echo.
echo   Card Number: 4000 0035 6000 0008
echo   CVV:         Any 3 digits
echo   Expiry:      Any future date (MM/YY)
echo.
echo ================================================================================
echo [DEVELOPER]
echo.
echo   Made By: Tirth Gajera
echo.
echo ================================================================================
echo.
echo.
set /p launch="Are you Ready to Launch? (Yes/No): "

if /i "%launch%"=="Yes" (
    echo.
    echo ================================================================================
    echo   Starting Laravel Development Server...
    echo ================================================================================
    echo.
    cd "Project"
    php artisan serve
) else if /i "%launch%"=="No" (
    echo.
    echo ================================================================================
    echo   Launch Cancelled. Exiting...
    echo ================================================================================
    echo.
    timeout /t 2 >nul
    exit
) else (
    echo.
    echo Invalid input. Please type 'Yes' or 'No'
    timeout /t 2 >nul
    exit
)

pause

