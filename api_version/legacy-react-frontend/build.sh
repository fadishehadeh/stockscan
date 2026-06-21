#!/bin/bash
# Build script for StockScan frontend
# Compiles React/Vite app and outputs to Laravel public/app folder

echo "Building StockScan frontend..."
npm run build

if [ $? -eq 0 ]; then
    echo ""
    echo "Build completed successfully!"
    echo "Frontend files have been compiled to: ../StockScan-API/public/app"
else
    echo ""
    echo "Build failed with error code $?"
    exit 1
fi
