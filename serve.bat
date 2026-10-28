@echo off
if exist "E:\tools\php" set "PATH=E:\tools\php;E:\tools\composer;E:\tools\nodejs;%PATH%"
if exist "C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64" set "PATH=C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64;C:\laragon\bin\composer;C:\laragon\bin\nodejs\node-v22;C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin;C:\laragon\bin\git\bin;%PATH%"
echo ====================================================
echo  Menjalankan Server Laravel (SI-KP)
echo  Buka di browser: http://127.0.0.1:8000
echo  Tekan Ctrl+C untuk menghentikan server
echo ====================================================
php -d extension=zip artisan serve
