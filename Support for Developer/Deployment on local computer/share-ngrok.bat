@echo off
echo ========================================
echo   Share Project Laravel via Ngrok
echo ========================================
echo.

REM Cek apakah ngrok sudah terinstall
where ngrok >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Ngrok belum terinstall!
    echo.
    echo Cara install:
    echo 1. Download dari: https://ngrok.com/download
    echo 2. Extract ngrok.exe ke folder ini atau C:\ngrok\
    echo 3. Daftar akun gratis di: https://dashboard.ngrok.com/signup
    echo 4. Setup authtoken: ngrok config add-authtoken YOUR_TOKEN
    echo.
    pause
    exit /b 1
)

echo [OK] Ngrok sudah terinstall
echo.

REM Cek apakah Laragon sudah jalan
echo Mengecek apakah server sudah jalan...
curl -s http://sim-keanggotaan-imm.test >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [WARNING] Server belum jalan atau tidak bisa diakses
    echo.
    echo Pastikan:
    echo 1. Laragon sudah running
    echo 2. http://sim-keanggotaan-imm.test bisa diakses
    echo.
    echo Tetap lanjut? (Y/N)
    set /p continue=
    if /i not "%continue%"=="Y" exit /b 1
)

echo.
echo ========================================
echo   Memulai Ngrok Tunnel...
echo ========================================
echo.
echo Setelah tunnel aktif:
echo 1. Copy URL yang muncul (https://xxx.ngrok-free.app)
echo 2. Kirim URL tersebut ke teman-teman
echo 3. Laptop harus tetap nyala dan terkoneksi internet
echo.
echo Tekan Ctrl+C untuk stop tunnel
echo.
echo ========================================
echo.

REM Jalankan ngrok
ngrok http 80 --host-header=sim-keanggotaan-imm.test
