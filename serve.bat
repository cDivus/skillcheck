@echo off
REM Run the app with the bundled portable PHP 8.4 (your system php is 8.2 and too old).
REM Usage: serve              -> http://127.0.0.1:8000
REM        serve --port=9000  -> custom port
"%~dp0php84\php.exe" "%~dp0artisan" serve %*
