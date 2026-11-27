@echo off
if exist "E:\tools\php" set "PATH=E:\tools\php;E:\tools\composer;E:\tools\nodejs;%PATH%"
if exist "C:\laragon\bin\php" (
    for /d %%D in ("C:\laragon\bin\php\php-*") do set "PATH=%%~fD;%PATH%"
)
if exist "C:\laragon\bin\nodejs" (
    for /d %%D in ("C:\laragon\bin\nodejs\node-*") do set "PATH=%%~fD;%PATH%"
)
if exist "C:\laragon\bin\mysql" (
    for /d %%D in ("C:\laragon\bin\mysql\mysql-*") do set "PATH=%%~fD\bin;%PATH%"
)
if exist "C:\laragon\bin\composer" set "PATH=C:\laragon\bin\composer;%PATH%"
if exist "C:\laragon\bin\git\bin" set "PATH=C:\laragon\bin\git\bin;%PATH%"
echo ====================================================
echo  Menjalankan Server Laravel (SI-KP)
echo  Buka di browser: http://127.0.0.1:8000
echo  Tekan Ctrl+C untuk menghentikan server
echo ====================================================
php -d extension=zip artisan serve
