#!/bin/bash

# Script untuk clear semua session dan cache Laravel
# Gunakan script ini ketika berpindah dari localhost:8000 ke virtual host atau sebaliknya

echo "🧹 Clearing Laravel sessions and cache..."
echo ""

# Clear session dari database
echo "📦 Truncating sessions table..."
php artisan tinker --execute "DB::table('sessions')->truncate(); echo 'Sessions cleared!';"

# Clear all cache
echo "🗑️  Clearing configuration cache..."
php artisan config:clear

echo "🗑️  Clearing route cache..."
php artisan route:clear

echo "🗑️  Clearing application cache..."
php artisan cache:clear

echo "🗑️  Clearing view cache..."
php artisan view:clear

echo ""
echo "✅ Done! Now:"
echo "   1. Close your browser completely"
echo "   2. Open browser again"
echo "   3. Access http://sim-keanggotaan-imm.test/login"
echo ""
