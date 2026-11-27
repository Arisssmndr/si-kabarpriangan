@echo off
if exist "E:\tools\php" set "PATH=E:\tools\php;E:\tools\composer;E:\tools\nodejs;%PATH%"
if exist "C:\laragon\bin\php" (
    for /d %%D in ("C:\laragon\bin\php\php-*") do set "PATH=%%~fD;%PATH%"
)
if exist "C:\laragon\bin\nodejs" (
    for /d %%D in ("C:\laragon\bin\nodejs\node-*") do set "PATH=%%~fD;%PATH%"
)
if exist "C:\laragon\bin\composer" set "PATH=C:\laragon\bin\composer;%PATH%"
echo ====================================================
echo  Menjalankan Vite Asset Bundler (Dev Mode)
echo ====================================================
npm run dev
