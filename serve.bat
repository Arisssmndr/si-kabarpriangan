@echo off
if exist "E:\tools\php" set "PATH=E:\tools\php;E:\tools\composer;E:\tools\nodejs;%PATH%"
echo ====================================================
echo  Menjalankan Server Laravel (SI-KP)
echo  Buka di browser: http://127.0.0.1:8000
echo  Tekan Ctrl+C untuk menghentikan server
echo ====================================================
php artisan serve
