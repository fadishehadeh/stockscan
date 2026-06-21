@echo off
REM Build script for StockScan frontend
REM Compiles React/Vite app and outputs to Laravel public/app folder

echo Building StockScan frontend...
call npm run build

if %ERRORLEVEL% EQU 0 (
    echo.
    echo Build completed successfully!
    echo Frontend files have been compiled to: ..\StockScan-API\public\app
) else (
    echo.
    echo Build failed with error code %ERRORLEVEL%
    exit /b %ERRORLEVEL%
)
