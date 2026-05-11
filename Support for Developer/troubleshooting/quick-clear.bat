@echo off
echo Membersihkan session dan cache...
php artisan tinker --execute "DB::table('sessions')->truncate();"
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
echo.
echo Selesai! Sekarang tutup browser dan buka lagi.
pause
