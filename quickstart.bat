@echo off
REM OnlyPlans Quick Start Script for Windows
REM This script helps you get OnlyPlans running on Windows

echo ================================================
echo 📅 OnlyPlans - Quick Start Setup
echo ================================================
echo.

echo Checking prerequisites...
echo.

REM Check for PHP
where php >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    echo ✅ PHP installed
    php -v | findstr PHP
) else (
    echo ❌ PHP not found
    set PHP_MISSING=1
)

REM Check for MySQL
where mysql >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    echo ✅ MySQL installed
) else (
    echo ⚠️  MySQL not found ^(optional if using SQLite or Docker^)
)

REM Check for Docker
where docker >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    echo ✅ Docker installed
    set DOCKER_AVAILABLE=1
) else (
    echo ℹ️  Docker not found ^(optional^)
)

echo.
echo ================================================
echo Choose your setup method:
echo ================================================
echo.
echo 1^) Automated Installer ^(recommended for XAMPP users^)
echo 2^) SQLite - Most portable, zero configuration
echo 3^) Docker - Professional deployment
echo 4^) Manual setup - I'll configure it myself
echo 5^) Exit
echo.
set /p choice="Enter your choice (1-5): "

if "%choice%"=="1" goto installer
if "%choice%"=="2" goto sqlite
if "%choice%"=="3" goto docker
if "%choice%"=="4" goto manual
if "%choice%"=="5" goto exit
goto invalid

:installer
echo.
echo 📋 Automated Installer Selected
echo ================================
echo.
echo Steps to follow:
echo 1. Make sure XAMPP/WAMP is running
echo 2. Open your browser
echo 3. Navigate to: http://localhost/HCI/install.php
echo 4. Follow the installation wizard
echo.
pause
start http://localhost/HCI/install.php
goto end

:sqlite
echo.
echo 🗄️  SQLite Setup Selected
echo ==========================
echo.

REM Backup existing connection.php
if exist connection.php (
    echo Backing up connection.php to connection_mysql.php.backup...
    copy connection.php connection_mysql.php.backup >nul
)

REM Use SQLite connection
if exist connection_sqlite.php (
    echo Setting up SQLite connection...
    copy connection_sqlite.php connection.php >nul
    echo ✅ SQLite configured!
    echo.
    echo Your database will be created automatically as: onlyplans.db
    echo.
    echo To use, open: http://localhost/HCI/index.php
    echo.
    set /p open_browser="Open in browser now? (y/n): "
    if /i "%open_browser%"=="y" start http://localhost/HCI/index.php
) else (
    echo ❌ connection_sqlite.php not found
    echo Please ensure all files are present
)
goto end

:docker
echo.
echo 🐳 Docker Setup Selected
echo ========================
echo.

if not defined DOCKER_AVAILABLE (
    echo ❌ Docker is not installed
    echo.
    echo Please install Docker Desktop from: https://docker.com
    echo.
    echo After installing:
    echo 1. Run: docker-compose up
    echo 2. Open: http://localhost:8080
    goto end
)

if not exist docker-compose.yml (
    echo ❌ docker-compose.yml not found
    goto end
)

REM Switch to Docker connection
if exist connection.php (
    echo Backing up connection.php...
    copy connection.php connection_local.php.backup >nul
)

if exist connection_docker.php (
    echo Configuring Docker connection...
    copy connection_docker.php connection.php >nul
)

echo Starting Docker containers...
echo.
docker-compose up -d

echo.
echo ✅ Docker containers started!
echo.
echo Access your applications:
echo   📅 Calendar:    http://localhost:8080
echo   🗄️  phpMyAdmin: http://localhost:8081
echo.
echo To stop: docker-compose down
echo To view logs: docker-compose logs
echo.

set /p open_browser="Open calendar in browser? (y/n): "
if /i "%open_browser%"=="y" (
    timeout /t 3 >nul
    start http://localhost:8080
)
goto end

:manual
echo.
echo 📖 Manual Setup
echo ===============
echo.
echo Follow these steps:
echo.
echo 1. Import database:
echo    - Open phpMyAdmin
echo    - Create database: 'onlyplans'
echo    - Import: database_setup.sql
echo.
echo 2. Configure connection:
echo    - Edit: connection.php
echo    - Update your database credentials
echo.
echo 3. Start your web server ^(XAMPP/WAMP^)
echo.
echo 4. Open: http://localhost/HCI/index.php
echo.
echo For detailed instructions, see README.md
goto end

:invalid
echo Invalid choice
goto end

:exit
echo Exiting...
goto end

:end
echo.
echo ================================================
echo Need help? Check README.md for detailed docs
echo ================================================
pause
