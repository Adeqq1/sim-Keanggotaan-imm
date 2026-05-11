@echo off
title Share Project Laravel ke Teman
color 0A

:menu
cls
echo.
echo ========================================
echo   SHARE PROJECT LARAVEL KE TEMAN
echo ========================================
echo.
echo   Project: SIM Keanggotaan IMM
echo   URL Lokal: http://sim-keanggotaan-imm.test
echo.
echo ========================================
echo.
echo Pilih metode share:
echo.
echo   1. LocalTunnel (Paling Mudah - No Signup)
echo   2. Ngrok (Paling Stabil - Perlu Signup)
echo   3. Setup Akun Demo
echo   4. Lihat Panduan
echo   5. Exit
echo.
echo ========================================
echo.
set /p choice=Pilih (1-5): 

if "%choice%"=="1" goto localtunnel
if "%choice%"=="2" goto ngrok
if "%choice%"=="3" goto demo
if "%choice%"=="4" goto panduan
if "%choice%"=="5" goto end
goto menu

:localtunnel
cls
echo.
echo ========================================
echo   LOCALTUNNEL
echo ========================================
echo.
echo Mengecek instalasi...
echo.

where npm >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] npm belum terinstall!
    echo.
    echo Install Node.js dulu dari: https://nodejs.org
    echo.
    pause
    goto menu
)

where lt >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo LocalTunnel belum terinstall.
    echo Install sekarang? (Y/N)
    set /p install=
    if /i "%install%"=="Y" (
        echo.
        echo Installing localtunnel...
        npm install -g localtunnel
        echo.
        echo Instalasi selesai!
        echo.
    ) else (
        goto menu
    )
)

echo [OK] Semua siap!
echo.
echo ========================================
echo   MEMULAI TUNNEL...
echo ========================================
echo.
echo Setelah tunnel aktif:
echo   1. Copy URL (https://xxx.loca.lt)
echo   2. Kirim ke teman
echo   3. Tekan Ctrl+C untuk stop
echo.
echo ========================================
echo.
pause

lt --port 80 --subdomain sim-imm

goto menu

:ngrok
cls
echo.
echo ========================================
echo   NGROK
echo ========================================
echo.
echo Mengecek instalasi...
echo.

where ngrok >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Ngrok belum terinstall!
    echo.
    echo Cara install:
    echo   1. Download: https://ngrok.com/download
    echo   2. Extract ngrok.exe ke folder ini
    echo   3. Daftar: https://dashboard.ngrok.com/signup
    echo   4. Setup: ngrok config add-authtoken YOUR_TOKEN
    echo.
    pause
    goto menu
)

echo [OK] Ngrok sudah terinstall!
echo.
echo ========================================
echo   MEMULAI TUNNEL...
echo ========================================
echo.
echo Setelah tunnel aktif:
echo   1. Copy URL (https://xxx.ngrok-free.app)
echo   2. Kirim ke teman
echo   3. Tekan Ctrl+C untuk stop
echo.
echo ========================================
echo.
pause

ngrok http 80 --host-header=sim-keanggotaan-imm.test

goto menu

:demo
cls
echo.
echo ========================================
echo   SETUP AKUN DEMO
echo ========================================
echo.
echo Membuat akun demo untuk teman...
echo.

php artisan tinker --execute "try { if (App\Models\User::where('email', 'demo@example.com')->exists()) { echo 'Akun admin demo sudah ada\n'; } else { App\Models\User::create(['name' => 'Demo Admin', 'email' => 'demo@example.com', 'password' => bcrypt('demo123'), 'role' => 'admin']); echo 'Akun admin demo berhasil dibuat\n'; } } catch (\Exception $e) { echo 'Error: ' . $e->getMessage() . '\n'; }"

php artisan tinker --execute "try { if (App\Models\User::where('email', 'kader@example.com')->exists()) { echo 'Akun kader demo sudah ada\n'; } else { App\Models\User::create(['name' => 'Demo Kader', 'email' => 'kader@example.com', 'password' => bcrypt('demo123'), 'role' => 'kader']); echo 'Akun kader demo berhasil dibuat\n'; } } catch (\Exception $e) { echo 'Error: ' . $e->getMessage() . '\n'; }"

echo.
echo ========================================
echo   AKUN DEMO BERHASIL!
echo ========================================
echo.
echo Kirim kredensial ini ke teman:
echo.
echo   ADMIN:
echo     Email: demo@example.com
echo     Password: demo123
echo.
echo   KADER:
echo     Email: kader@example.com
echo     Password: demo123
echo.
echo ========================================
echo.
pause
goto menu

:panduan
cls
echo.
echo ========================================
echo   PANDUAN LENGKAP
echo ========================================
echo.
echo File panduan yang tersedia:
echo.
echo   1. QUICK_START_SHARE.md
echo      - Panduan cepat 5 menit
echo.
echo   2. SHARE_PROJECT_GRATIS.md
echo      - Panduan lengkap semua metode
echo.
echo   3. README_SHARE_PROJECT.md
echo      - Ringkasan semua file
echo.
echo Buka file tersebut untuk panduan detail!
echo.
echo ========================================
echo.
pause
goto menu

:end
cls
echo.
echo Terima kasih! Semoga sukses share project-nya! 🚀
echo.
timeout /t 2 >nul
exit
