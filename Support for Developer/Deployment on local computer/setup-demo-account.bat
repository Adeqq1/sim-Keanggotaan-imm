@echo off
echo ========================================
echo   Setup Akun Demo untuk Teman
echo ========================================
echo.

echo Membuat akun demo...
echo.

REM Buat akun admin demo
php artisan tinker --execute "try { \$user = App\Models\User::where('email', 'demo@example.com')->first(); if (\$user) { echo 'Akun demo sudah ada\n'; } else { App\Models\User::create(['name' => 'Demo Admin', 'email' => 'demo@example.com', 'password' => bcrypt('demo123'), 'role' => 'admin']); echo 'Akun demo admin berhasil dibuat\n'; } } catch (\Exception \$e) { echo 'Error: ' . \$e->getMessage(); }"

echo.

REM Buat akun kader demo
php artisan tinker --execute "try { \$user = App\Models\User::where('email', 'kader@example.com')->first(); if (\$user) { echo 'Akun kader demo sudah ada\n'; } else { App\Models\User::create(['name' => 'Demo Kader', 'email' => 'kader@example.com', 'password' => bcrypt('demo123'), 'role' => 'kader']); echo 'Akun demo kader berhasil dibuat\n'; } } catch (\Exception \$e) { echo 'Error: ' . \$e->getMessage(); }"

echo.
echo ========================================
echo   Akun Demo Berhasil Dibuat!
echo ========================================
echo.
echo Kirim kredensial ini ke teman-teman:
echo.
echo ADMIN:
echo   Email: demo@example.com
echo   Password: demo123
echo.
echo KADER:
echo   Email: kader@example.com
echo   Password: demo123
echo.
echo ========================================
echo.
pause
