@echo off
if exist "E:\tools\php" set "PATH=E:\tools\php;E:\tools\composer;E:\tools\nodejs;%PATH%"
if exist "C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64" set "PATH=C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64;C:\laragon\bin\composer;C:\laragon\bin\nodejs\node-v22;%PATH%"
if exist "C:\laragon\bin\nodejs\node-v22" set "PATH=C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64;C:\laragon\bin\composer;C:\laragon\bin\nodejs\node-v22;%PATH%"
echo ====================================================
echo  Menjalankan Vite Asset Bundler (Dev Mode)
echo ====================================================
npm run dev
