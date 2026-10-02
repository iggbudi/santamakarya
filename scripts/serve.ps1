param([int]$Port = 8000)
$phpCommand = Get-Command php -ErrorAction SilentlyContinue
$phpExe = if (Test-Path 'C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe') {
    'C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe'
} elseif ($phpCommand) { $phpCommand.Source } else { throw 'PHP tidak ditemukan.' }
$env:Path = (Split-Path $phpExe) + ';' + $env:Path
& $phpExe -d extension=zip artisan serve --host=127.0.0.1 --port=$Port
exit $LASTEXITCODE
