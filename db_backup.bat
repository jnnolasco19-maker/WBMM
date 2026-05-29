@echo off
echo ===================================================
echo   WBMM Database Backup Utility (MySQL / XAMPP)
echo ===================================================
echo.
set hostname=localhost
set username=root
set database=wbmm_db
set password=

set /p hostname="Enter Hostname [localhost]: "
set /p username="Enter Username [root]: "
set /p database="Enter Database Name [wbmm_db]: "
set /p password="Enter Password (press Enter for none): "

set backup_dir=database_backups
if not exist %backup_dir% mkdir %backup_dir%

:: Get date and time for backup filename
set cur_date=%date:~10,4%-%date:~4,2%-%date:~7,2%
set cur_time=%time:~0,2%%time:~3,2%%time:~6,2%
:: Replace space with 0 in case hour is less than 10
set cur_time=%cur_time: =0%

set filename=%backup_dir%\backup_%database%_%cur_date%_%cur_time%.sql

echo.
echo Backing up database '%database%' to '%filename%'...

:: Try running mysqldump from global path or default XAMPP path
mysqldump -h %hostname% -u %username% --password="%password%" %database% > "%filename%" 2>nul
if %errorlevel% neq 0 (
    echo [INFO] Standard 'mysqldump' not in PATH. Trying default XAMPP path...
    C:\xampp\mysql\bin\mysqldump -h %hostname% -u %username% --password="%password%" %database% > "%filename%"
)

if %errorlevel% equ 0 (
    echo.
    echo [SUCCESS] Backup completed successfully!
    echo File saved at: %filename%
) else (
    echo.
    echo [ERROR] Backup failed. Please verify if MySQL is running and credentials are correct.
    if exist "%filename%" del "%filename%"
)
echo.
pause
