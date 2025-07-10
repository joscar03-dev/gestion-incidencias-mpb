@echo off
echo =================================================
echo    ESCALADO AUTOMATICO - LARAVEL SCHEDULER
echo =================================================
echo.
echo Este script ejecuta el scheduler de Laravel para
echo que el escalado de tickets funcione automaticamente.
echo.
echo Presiona Ctrl+C para detener el scheduler
echo.
echo =================================================
echo.

cd /d "c:\laragon\www\gestion-incidencias"

echo [%date% %time%] Iniciando scheduler de Laravel...
echo.

php artisan schedule:work

pause
