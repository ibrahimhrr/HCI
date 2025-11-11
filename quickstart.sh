#!/bin/bash

# OnlyPlans Quick Start Script
# This script helps you get OnlyPlans running on any device

echo "================================================"
echo "📅 OnlyPlans - Quick Start Setup"
echo "================================================"
echo ""

# Check if running on macOS, Linux, or Windows (Git Bash)
if [[ "$OSTYPE" == "darwin"* ]]; then
    OS="macOS"
elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
    OS="Linux"
elif [[ "$OSTYPE" == "msys" ]] || [[ "$OSTYPE" == "cygwin" ]]; then
    OS="Windows"
else
    OS="Unknown"
fi

echo "Detected OS: $OS"
echo ""

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

echo "Checking prerequisites..."
echo ""

# Check for PHP
if command_exists php; then
    PHP_VERSION=$(php -v | head -n 1)
    echo "✅ PHP installed: $PHP_VERSION"
else
    echo "❌ PHP not found"
    PHP_MISSING=true
fi

# Check for MySQL
if command_exists mysql; then
    echo "✅ MySQL installed"
else
    echo "⚠️  MySQL not found (optional if using SQLite or Docker)"
fi

# Check for Docker
if command_exists docker; then
    echo "✅ Docker installed"
    DOCKER_AVAILABLE=true
else
    echo "ℹ️  Docker not found (optional)"
fi

echo ""
echo "================================================"
echo "Choose your setup method:"
echo "================================================"
echo ""
echo "1) Automated Installer (recommended for MAMP/XAMPP users)"
echo "2) SQLite - Most portable, zero configuration"
echo "3) Docker - Professional deployment"
echo "4) Manual setup - I'll configure it myself"
echo "5) Exit"
echo ""
read -p "Enter your choice (1-5): " choice

case $choice in
    1)
        echo ""
        echo "📋 Automated Installer Selected"
        echo "================================"
        echo ""
        echo "Steps to follow:"
        echo "1. Make sure MAMP/XAMPP/WAMP is running"
        echo "2. Open your browser"
        echo "3. Navigate to: http://localhost/HCI/install.php"
        echo "   (or http://localhost:8888/HCI/install.php for MAMP)"
        echo "4. Follow the installation wizard"
        echo ""
        read -p "Press Enter to continue..."
        
        if [[ "$OS" == "macOS" ]]; then
            open "http://localhost:8888/HCI/install.php" 2>/dev/null || open "http://localhost/HCI/install.php"
        elif [[ "$OS" == "Linux" ]]; then
            xdg-open "http://localhost/HCI/install.php"
        elif [[ "$OS" == "Windows" ]]; then
            start "http://localhost/HCI/install.php"
        fi
        ;;
        
    2)
        echo ""
        echo "🗄️  SQLite Setup Selected"
        echo "=========================="
        echo ""
        
        # Backup existing connection.php
        if [ -f "connection.php" ]; then
            echo "Backing up connection.php to connection_mysql.php.backup..."
            cp connection.php connection_mysql.php.backup
        fi
        
        # Use SQLite connection
        if [ -f "connection_sqlite.php" ]; then
            echo "Setting up SQLite connection..."
            cp connection_sqlite.php connection.php
            echo "✅ SQLite configured!"
            echo ""
            echo "Your database will be created automatically as: onlyplans.db"
            echo ""
            echo "To use, open: http://localhost/HCI/index.php"
            echo ""
            
            if [[ "$OS" == "macOS" ]]; then
                read -p "Open in browser now? (y/n): " open_browser
                if [[ "$open_browser" == "y" ]]; then
                    open "http://localhost:8888/HCI/index.php" 2>/dev/null || open "http://localhost/HCI/index.php"
                fi
            fi
        else
            echo "❌ connection_sqlite.php not found"
            echo "Please ensure all files are present"
        fi
        ;;
        
    3)
        echo ""
        echo "🐳 Docker Setup Selected"
        echo "========================"
        echo ""
        
        if [ -z "$DOCKER_AVAILABLE" ]; then
            echo "❌ Docker is not installed"
            echo ""
            echo "Please install Docker Desktop from: https://docker.com"
            echo ""
            echo "After installing:"
            echo "1. Run: docker-compose up"
            echo "2. Open: http://localhost:8080"
            exit 1
        fi
        
        if [ ! -f "docker-compose.yml" ]; then
            echo "❌ docker-compose.yml not found"
            exit 1
        fi
        
        # Switch to Docker connection
        if [ -f "connection.php" ]; then
            echo "Backing up connection.php..."
            cp connection.php connection_local.php.backup
        fi
        
        if [ -f "connection_docker.php" ]; then
            echo "Configuring Docker connection..."
            cp connection_docker.php connection.php
        fi
        
        echo "Starting Docker containers..."
        echo ""
        docker-compose up -d
        
        echo ""
        echo "✅ Docker containers started!"
        echo ""
        echo "Access your applications:"
        echo "  📅 Calendar:    http://localhost:8080"
        echo "  🗄️  phpMyAdmin: http://localhost:8081"
        echo ""
        echo "To stop: docker-compose down"
        echo "To view logs: docker-compose logs"
        echo ""
        
        if [[ "$OS" == "macOS" ]]; then
            read -p "Open calendar in browser? (y/n): " open_browser
            if [[ "$open_browser" == "y" ]]; then
                sleep 3  # Wait for containers to start
                open "http://localhost:8080"
            fi
        fi
        ;;
        
    4)
        echo ""
        echo "📖 Manual Setup"
        echo "==============="
        echo ""
        echo "Follow these steps:"
        echo ""
        echo "1. Import database:"
        echo "   - Open phpMyAdmin"
        echo "   - Create database: 'onlyplans'"
        echo "   - Import: database_setup.sql"
        echo ""
        echo "2. Configure connection:"
        echo "   - Edit: connection.php"
        echo "   - Update your database credentials"
        echo ""
        echo "3. Start your web server (MAMP/XAMPP)"
        echo ""
        echo "4. Open: http://localhost/HCI/index.php"
        echo ""
        echo "For detailed instructions, see README.md"
        ;;
        
    5)
        echo "Exiting..."
        exit 0
        ;;
        
    *)
        echo "Invalid choice"
        exit 1
        ;;
esac

echo ""
echo "================================================"
echo "Need help? Check README.md for detailed docs"
echo "================================================"
