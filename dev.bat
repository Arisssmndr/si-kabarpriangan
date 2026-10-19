@echo off
if exist "E:\tools\php" set "PATH=E:\tools\php;E:\tools\composer;E:\tools\nodejs;%PATH%"
echo ====================================================
echo  Menjalankan Vite Asset Bundler (Dev Mode)
echo ====================================================
npm run dev
