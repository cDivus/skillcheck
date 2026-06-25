@echo off
REM Full dev environment (app server + Vite) using the bundled portable PHP 8.4.
REM Stop everything with Ctrl+C.
npx concurrently -c "#0f766e,#2dd4bf" "\"%~dp0php84\php.exe\" \"%~dp0artisan\" serve" "npm run dev" --names=server,vite --kill-others
