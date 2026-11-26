@echo off
title CargoTrace - Live Online Public Server
color 0A
cls

where python >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    python share.py
) else (
    echo [i] Menjalankan cloudflared langsung...
    tasklist /FI "IMAGENAME eq php.exe" 2>NUL | find /I /N "php.exe">NUL
    if not "%ERRORLEVEL%"=="0" (
        start /B php artisan serve --port=8000 > nul 2>&1
        timeout /t 2 /nobreak > nul
    )
    .\cloudflared.exe tunnel --url http://127.0.0.1:8000
)

pause
