@echo off
echo ========================================
echo   Share Project Laravel via LocalTunnel
echo ========================================
echo.

REM Cek apakah npm sudah terinstall
where npm >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] npm belum terinstall!
    echo.
    echo Cara install:
    echo 1. Download Node.js dari: https://nodejs.org
    echo 2. Install Node.js (npm included)
    echo 3. Restart terminal
    echo.
    pause
    exit /b 1
)

echo [OK] npm sudah terinstall
echo.

REM Cek apakah localtunnel sudah terinstall
where lt >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo LocalTunnel belum terinstall. Install sekarang? (Y/N)
    set /p install=
    if /i "%install%"=="Y" (
        echo Installing localtunnel...
        npm install -g localtunnel
        echo.
    ) else (
        exit /b 1
    )
)

echo [OK] LocalTunnel sudah terinstall
echo.

REM Cek apakah server sudah jalan
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
echo   Memulai LocalTunnel...
echo ========================================
echo.
echo Setelah tunnel aktif:
echo 1. Copy URL yang muncul (https://xxx.loca.lt)
echo 2. Kirim URL tersebut ke teman-teman
echo 3. Teman akan diminta password saat pertama akses
echo 4. Password akan ditampilkan di terminal ini
echo.
echo Tekan Ctrl+C untuk stop tunnel
echo.
echo ========================================
echo.

REM Jalankan localtunnel
lt --port 80 --subdomain sim-imm
