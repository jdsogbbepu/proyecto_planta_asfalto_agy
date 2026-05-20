@echo off
set DB_NAME=db_planta_asfalto_agy
set DB_USER=root
set BACKUP_DIR=C:\xampp\htdocs\planta_asfalto_agy\backups
set MYSQL_DUMP="C:\xampp\mysql\bin\mysqldump.exe"

:: Crear directorio de respaldos si no existe
if not exist "%BACKUP_DIR%" (
    mkdir "%BACKUP_DIR%"
)

:: Obtener timestamp en formato robusto YYYYMMDD_HHMMSS usando WMIC
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value 2^>nul') do set datetime=%%I
set TIMESTAMP=%datetime:~0,8%_%datetime:~8,6%

set FILE_NAME=%BACKUP_DIR%\backup_%DB_NAME%_%TIMESTAMP%.sql

echo Realizando copia de seguridad de la base de datos '%DB_NAME%'...
%MYSQL_DUMP% --user=%DB_USER% %DB_NAME% > "%FILE_NAME%"

if %ERRORLEVEL% equ 0 (
    echo ===================================================
    echo [EXITO] Respaldo de base de datos creado con exito.
    echo Ubicacion: %FILE_NAME%
    echo ===================================================
) else (
    echo ===================================================
    echo [ERROR] Error al crear la copia de seguridad.
    echo Verifique la ruta del ejecutable mysqldump y accesos.
    echo ===================================================
)
pause
