@echo off
echo WBMM Setup - General Santos City Public Market
echo.

set MYSQL=c:\xampp\mysql\bin\mysql.exe
set PHP=c:\xampp\php\php.exe

if not exist "%MYSQL%" (
    echo ERROR: XAMPP MySQL not found at %MYSQL%
    pause
    exit /b 1
)

echo [1/3] Creating database and importing schema...
"%MYSQL%" -u root -e "DROP DATABASE IF EXISTS wbmm_db; CREATE DATABASE wbmm_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
"%MYSQL%" -u root wbmm_db < "%~dp0schema.sql"
if errorlevel 1 (
    echo ERROR: schema import failed.
    pause
    exit /b 1
)

echo [2/3] Installing Composer dependencies...
cd /d "%~dp0"
if exist "%PHP%" (
    "%PHP%" C:\ProgramData\ComposerSetup\bin\composer.phar install 2>nul
    if errorlevel 1 composer install
) else (
    composer install
)

echo [3/3] Done!
echo.
echo Open: http://localhost/WBMM/public/
echo Login: admin@wbmm.com / Admin@1234
echo.
pause
