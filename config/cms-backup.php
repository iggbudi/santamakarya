<?php

return [
    'mysqldump' => env('CMS_MYSQLDUMP', PHP_OS_FAMILY === 'Windows' ? 'C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysqldump.exe' : 'mysqldump'),
    'mysql' => env('CMS_MYSQL', PHP_OS_FAMILY === 'Windows' ? 'C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysql.exe' : 'mysql'),
];
