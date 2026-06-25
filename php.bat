@echo off
REM Shim: makes `php ...` use the bundled portable PHP 8.4 inside this project folder.
REM (Your system php is 8.2.4, too old for Laravel 13.) cmd.exe checks the current
REM directory before PATH, so running `php artisan serve` here uses php84\php.exe.
"%~dp0php84\php.exe" %*
