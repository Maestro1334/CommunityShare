@echo off
schtasks /Create /TN XAMPP /TR "C:/xampp/php/php-win.exe C:/xampp/htdocs/communityshare/app/cron/index.php" /SC MINUTE /MO 1
pause