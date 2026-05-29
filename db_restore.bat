@echo off
echo ===================================================
echo   WBMM Database Restore Utility (MySQL / XAMPP)
echo ===================================================
echo.
set hostname=localhost
set username=root
set database=wbmm_db
set password=
set sqlfile=

set /p hostname="Enter Hostname [localhost]: "
set /p username="Enter Username [root]: "
set /p database="Enter Database Name [wbmm_db]: "
set /p password="Enter Password (press Enter for none): "
set /p sqlfile="Enter the path to the backup .sql file: "

if not exist "%sqlfile%" (
    echo.
    echo [ERROR] Backup file '%sqlfile%' not found!
    echo.
    pause
    exit /b
)

echo.
echo Restoring database '%database%' from '%sqlfile%'...

:: Try running mysql from global path or default XAMPP path
mysql -h %hostname% -u %username% --password="%password%" -e "CREATE DATABASE IF NOT EXISTS %database%;" 2>nul
mysql -h %hostname% -u %username% --password="%password%" %database% < "%sqlfile%" 2>nul
if %errorlevel% neq 0 (
    echo [INFO] Standard 'mysql' not in PATH. Trying default XAMPP path...
    C:\xampp\mysql\bin\mysql -h %hostname% -u %username% --password="%password%" -e "CREATE DATABASE IF NOT EXISTS %database%;"
    C:\xampp\mysql\bin\mysql -h %hostname% -u %username% --password="%password%" %database% < "%sqlfile%"
)

if %errorlevel% equ 0 (
    echo.
    echo [SUCCESS] Database restored successfully from '%sqlfile%'!
) else (
    echo.
    echo [ERROR] Restore failed. Please verify if MySQL is running and credentials are correct.
)
echo.
pause
