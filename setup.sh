#!/bin/bash

# Setup script for PHP Short URL System
# This script helps with initial setup

echo "🚀 PHP Short URL System Setup"
echo "=============================="

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "📝 Creating .env file from template..."
    cp .env.example .env
    echo "✅ .env file created. Please edit it with your database credentials."
    echo ""
    echo "⚠️  Don't forget to update these values in .env:"
    echo "   - DB_HOST"
    echo "   - DB_NAME" 
    echo "   - DB_USER"
    echo "   - DB_PASS"
    echo ""
else
    echo "✅ .env file already exists"
fi

# Check if PHP is available
if command -v php &> /dev/null; then
    echo "✅ PHP is available"
    
    # Test syntax
    echo "🔍 Checking PHP syntax..."
    php test_syntax.php
    
    if [ $? -eq 0 ]; then
        echo ""
        echo "📊 Next steps:"
        echo "1. Make sure your MySQL server is running"
        echo "2. Run: php scripts/migrate.php"
        echo "3. Run: php scripts/seed_admin.php"
        echo "4. Configure your web server to point to the 'public/' directory"
        echo ""
        echo "🌐 Then visit: http://your-domain.com/admin"
    fi
else
    echo "❌ PHP is not available. Please install PHP 8.0+"
fi